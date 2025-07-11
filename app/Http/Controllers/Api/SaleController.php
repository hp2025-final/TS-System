<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\DressItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SaleController extends Controller
{
    /**
     * Display a listing of sales
     */
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'saleItems.dressItem.dress'])
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $sales = $query->paginate($request->get('per_page', 15));

        return response()->json($sales);
    }

    /**
     * Store a new sale
     */
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.dress_item_id' => 'required|exists:dress_items,id',
            'payment_method' => 'required|in:cash,bank_transfer',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals and track discounts by type
            $subtotal = 0;
            $totalCollectionDiscount = 0;
            $totalDressDiscount = 0;
            $totalSizeDiscount = 0;
            $itemsData = [];

            foreach ($request->items as $item) {
                $dressItem = DressItem::with(['dress.collection'])->findOrFail($item['dress_item_id']);
                
                // Check availability
                if ($dressItem->status !== 'available') {
                    return response()->json([
                        'message' => "Item {$dressItem->barcode} is not available"
                    ], 400);
                }

                $dress = $dressItem->dress;
                $collection = $dress->collection;
                $originalPrice = $dress->sale_price;
                $finalPrice = $originalPrice;
                
                // Calculate discounts - only highest discount applies
                $collectionDiscount = 0;
                $dressDiscount = 0;
                $sizeDiscount = 0;
                $appliedDiscountAmount = 0;
                
                $highestDiscountRate = 0;
                $discountType = '';

                // Check collection discount
                if ($collection->discount_active && $collection->discount_percentage > 0) {
                    if ($collection->discount_percentage > $highestDiscountRate) {
                        $highestDiscountRate = $collection->discount_percentage;
                        $discountType = 'collection';
                    }
                }

                // Check dress discount
                if ($dress->discount_active && $dress->discount_percentage > 0) {
                    if ($dress->discount_percentage > $highestDiscountRate) {
                        $highestDiscountRate = $dress->discount_percentage;
                        $discountType = 'dress';
                    }
                }

                // Check size discount
                if ($dressItem->size_discount_active && $dressItem->size_discount_percentage > 0) {
                    if ($dressItem->size_discount_percentage > $highestDiscountRate) {
                        $highestDiscountRate = $dressItem->size_discount_percentage;
                        $discountType = 'size';
                    }
                }

                // Apply the highest discount
                if ($highestDiscountRate > 0) {
                    $appliedDiscountAmount = $originalPrice * ($highestDiscountRate / 100);
                    $finalPrice = $originalPrice - $appliedDiscountAmount;
                    
                    // Track by discount type
                    if ($discountType === 'collection') {
                        $collectionDiscount = $appliedDiscountAmount;
                        $totalCollectionDiscount += $collectionDiscount;
                    } elseif ($discountType === 'dress') {
                        $dressDiscount = $appliedDiscountAmount;
                        $totalDressDiscount += $dressDiscount;
                    } elseif ($discountType === 'size') {
                        $sizeDiscount = $appliedDiscountAmount;
                        $totalSizeDiscount += $sizeDiscount;
                    }
                }

                $subtotal += $finalPrice;

                // Calculate profit
                $profit = $finalPrice - $dress->cost_price;

                $itemsData[] = [
                    'dress_item' => $dressItem,
                    'dress' => $dress,
                    'collection' => $collection,
                    'original_price' => $originalPrice,
                    'final_price' => $finalPrice,
                    'collection_discount' => $collectionDiscount,
                    'dress_discount' => $dressDiscount,
                    'size_discount' => $sizeDiscount,
                    'total_discount' => $appliedDiscountAmount,
                    'profit' => $profit,
                    'collection_discount_percentage' => $discountType === 'collection' ? $highestDiscountRate : 0,
                    'dress_discount_percentage' => $discountType === 'dress' ? $highestDiscountRate : 0,
                    'size_discount_percentage' => $discountType === 'size' ? $highestDiscountRate : 0,
                ];
            }

            $totalDiscountAmount = $totalCollectionDiscount + $totalDressDiscount + $totalSizeDiscount;
            $taxAmount = $request->tax_amount ?? 0;
            $totalAmount = $subtotal + $taxAmount;

            // Create sale record
            $sale = Sale::create([
                'user_id' => Auth::id(),
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'subtotal' => $subtotal,
                'collection_discount_amount' => $totalCollectionDiscount,
                'dress_discount_amount' => $totalDressDiscount,
                'size_discount_amount' => $totalSizeDiscount,
                'total_discount_amount' => $totalDiscountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'sale_date' => now(),
            ]);

            // Create sale items and update inventory
            foreach ($itemsData as $itemData) {
                $dressItem = $itemData['dress_item'];
                $dress = $itemData['dress'];
                $collection = $itemData['collection'];
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'dress_item_id' => $dressItem->id,
                    'dress_name' => $dress->name,
                    'collection_name' => $collection->name,
                    'sku' => $dress->sku,
                    'barcode' => $dressItem->barcode,
                    'size' => $dressItem->dress->size,
                    'cost_price' => $dress->cost_price,
                    'sale_price' => $itemData['original_price'],
                    'collection_discount_percentage' => $itemData['collection_discount_percentage'],
                    'dress_discount_percentage' => $itemData['dress_discount_percentage'],
                    'size_discount_percentage' => $itemData['size_discount_percentage'],
                    'total_discount_amount' => $itemData['total_discount'],
                    'tax_percentage' => $dress->tax_percentage,
                    // GST calculated on original price (before discount)
                    'tax_amount' => ($itemData['original_price'] * $dress->tax_percentage) / 100,
                    'item_total' => $itemData['final_price'],
                    'profit_amount' => $itemData['profit'],
                ]);

                // Update item status to sold
                $dressItem->update([
                    'status' => 'sold',
                    'sold_at' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Sale completed successfully',
                'sale' => $sale->load(['saleItems.dressItem.dress.collection', 'user']),
                'invoice_url' => url("/api/sales/{$sale->id}/invoice")
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Sale failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified sale
     */
    public function show(Sale $sale)
    {
        $sale->load(['user', 'saleItems.dressItem.dress.collection']);
        return response()->json($sale);
    }

    /**
     * Generate invoice for a sale
     */
    public function generateInvoice(Sale $sale)
    {
        $sale->load(['user', 'saleItems.dressItem.dress.collection']);
        
        return response()->json([
            'sale' => $sale,
            'company' => [
                'name' => 'TS Fashion Store',
                'address' => 'Main Street, Fashion District',
                'phone' => '+92-xxx-xxxxxxx',
                'email' => 'info@tsfashion.com'
            ]
        ]);
    }

    /**
     * Get daily sales report
     */
    public function dailyReport(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        
        $sales = Sale::whereDate('sale_date', $date)
            ->with(['saleItems.dressItem.dress'])
            ->get();

        $summary = [
            'date' => $date,
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total_amount'),
            'total_profit' => $sales->sum('profit_amount'),
            'payment_methods' => $sales->groupBy('payment_method')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('total_amount')
                ];
            }),
            'hourly_sales' => $sales->groupBy(function ($sale) {
                return $sale->created_at->format('H:00');
            })->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('total_amount')
                ];
            })
        ];

        return response()->json([
            'summary' => $summary,
            'sales' => $sales
        ]);
    }

    /**
     * Calculate final price with all applicable discounts
     */
    private function calculateFinalPrice(DressItem $dressItem)
    {
        $basePrice = $dressItem->dress->sale_price;
        $discount = 0;

        // Size level discount has highest priority
        if ($dressItem->size_discount_percentage > 0 && $dressItem->size_discount_active) {
            $discount = $dressItem->size_discount_percentage;
        }
        // Dress level discount
        elseif ($dressItem->dress->discount_percentage > 0 && $dressItem->dress->discount_active) {
            $discount = $dressItem->dress->discount_percentage;
        }
        // Collection level discount
        elseif ($dressItem->dress->collection->discount_percentage > 0 && $dressItem->dress->collection->discount_active) {
            $discount = $dressItem->dress->collection->discount_percentage;
        }

        return $basePrice * (1 - ($discount / 100));
    }

    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber()
    {
        $now = now();
        $year = $now->format('y');
        $month = $now->format('m');
        $day = $now->format('d');
        $hour = $now->format('H');
        $minute = $now->format('i');
        $second = $now->format('s');
        
        return "TS-{$year}{$month}{$day}{$hour}{$minute}{$second}";
    }

    /**
     * Search sold items for returns
     */
    public function searchSoldItems(Request $request)
    {
        $query = $request->get('query');
        
        if (!$query) {
            return response()->json(['data' => []]);
        }

        $saleItems = SaleItem::with(['sale', 'dressItem.dress.collection'])
            ->where(function ($q) use ($query) {
                $q->where('barcode', 'LIKE', "%{$query}%")
                  ->orWhereHas('sale', function ($subQ) use ($query) {
                      $subQ->where('invoice_number', 'LIKE', "%{$query}%")
                           ->orWhere('fbr_invoice_number', 'LIKE', "%{$query}%");
                  });
            })
            // Exclude items that have already been returned/exchanged
            ->whereNotExists(function ($subQuery) {
                $subQuery->select(\DB::raw(1))
                    ->from('returns')
                    ->whereColumn('returns.sale_item_id', 'sale_items.id')
                    ->whereColumn('returns.dress_item_id', 'sale_items.dress_item_id');
            })
            // Only include items where the dress item is still in 'sold' status
            ->whereHas('dressItem', function ($q) {
                $q->where('status', 'sold');
            })
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return response()->json(['data' => $saleItems]);
    }
}
