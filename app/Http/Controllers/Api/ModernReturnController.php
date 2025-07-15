<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\DressItem;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModernReturnController extends Controller
{
    /**
     * Search for items by barcode or invoice number
     */
    public function search($query)
    {
        try {
            $results = [];
            
            // First try to search by barcode
            $saleItems = SaleItem::with(['sale', 'dressItem'])
                ->where('barcode', $query)
                ->whereHas('dressItem', function($q) {
                    $q->where('status', 'sold');
                })
                ->get();
            
            // If no results by barcode, try by invoice number
            if ($saleItems->isEmpty()) {
                $saleItems = SaleItem::with(['sale', 'dressItem'])
                    ->whereHas('sale', function($q) use ($query) {
                        $q->where('invoice_number', 'LIKE', '%' . $query . '%');
                    })
                    ->whereHas('dressItem', function($q) {
                        $q->where('status', 'sold');
                    })
                    ->get();
            }
            
            foreach ($saleItems as $saleItem) {
                // Check if already returned
                $existingReturn = ReturnItem::where('sale_item_id', $saleItem->id)->first();
                if ($existingReturn) {
                    continue; // Skip already returned items
                }
                
                // Get individual item pricing data from sale_items table
                // Calculate correct amount paid: original_price + tax - discount
                $originalPrice = $saleItem->sale_price;
                $taxAmount = $saleItem->tax_amount;
                $discountAmount = $saleItem->total_discount_amount;
                $correctAmountPaid = $originalPrice + $taxAmount - $discountAmount;
                
                $results[] = [
                    'id' => $saleItem->id,
                    'sale_item_id' => $saleItem->id,
                    'dress_item_id' => $saleItem->dress_item_id,
                    'dress_name' => $saleItem->dress_name,
                    'collection_name' => $saleItem->collection_name,
                    'size' => $saleItem->size,
                    'barcode' => $saleItem->barcode,
                    'sku' => $saleItem->sku,
                    
                    // Individual item pricing breakdown - correctly calculated
                    'original_price' => number_format($originalPrice, 2, '.', ''),
                    'tax_percentage' => number_format($saleItem->tax_percentage, 0, '.', ''),
                    'tax_amount' => number_format($taxAmount, 2, '.', ''),
                    'total_discount_amount' => number_format($discountAmount, 2, '.', ''),
                    'item_total' => number_format($correctAmountPaid, 2, '.', ''),
                    
                    // Sale information
                    'sale_date' => $saleItem->sale->created_at->format('Y-m-d H:i:s'),
                    'invoice_number' => $saleItem->sale->invoice_number,
                    'customer_name' => $saleItem->sale->customer_name,
                    'customer_phone' => $saleItem->sale->customer_phone,
                    
                    // Status
                    'status' => $saleItem->dressItem->status,
                    'can_return' => true,
                    'return_eligible' => true
                ];
            }
            
            if (empty($results)) {
                return response()->json([
                    'message' => 'No eligible items found for return',
                    'items' => []
                ], 404);
            }
            
            return response()->json([
                'message' => 'Items found',
                'items' => $results,
                'count' => count($results)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error searching for items',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Process a return or exchange
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_item_id' => 'required|exists:sale_items,id',
            'return_type' => 'required|in:return,exchange',
            'reason' => 'required|string',
            'notes' => 'nullable|string',
            'exchange_item_id' => 'required_if:return_type,exchange|exists:dress_items,id'
        ]);
        
        DB::beginTransaction();
        
        try {
            $saleItem = SaleItem::with(['sale', 'dressItem'])->findOrFail($request->sale_item_id);
            
            // Check if already returned
            $existingReturn = ReturnItem::where('sale_item_id', $saleItem->id)->first();
            if ($existingReturn) {
                return response()->json([
                    'message' => 'This item has already been returned'
                ], 400);
            }
            
            if ($request->return_type === 'exchange') {
                return $this->processExchange($request, $saleItem);
            } else {
                return $this->processReturn($request, $saleItem);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error processing return',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Process a simple return (refund)
     */
    private function processReturn(Request $request, SaleItem $saleItem)
    {
        // Create return record
        $returnItem = ReturnItem::create([
            'sale_item_id' => $saleItem->id,
            'dress_item_id' => $saleItem->dress_item_id,
            'user_id' => auth()->id() ?? 1, // Default to user 1 if not authenticated
            'return_type' => 'return',
            'return_reason' => $request->reason,
            'notes' => $request->notes,
            'refund_amount' => $saleItem->item_total,
            'return_date' => now(),
        ]);
        
        // Update dress item status and returned_at timestamp
        $saleItem->dressItem->update([
            'status' => 'returned_resaleable',
            'returned_at' => now()
        ]);
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Return processed successfully',
            'return_id' => $returnItem->id,
            'refund_amount' => $saleItem->item_total,
            'data' => [
                'return_type' => 'return',
                'refund_amount' => $saleItem->item_total,
                'item_name' => $saleItem->dress_name,
                'return_date' => $returnItem->return_date->format('Y-m-d H:i:s')
            ]
        ]);
    }
    
    /**
     * Process an exchange
     */
    private function processExchange(Request $request, SaleItem $saleItem)
    {
        $exchangeItem = DressItem::with(['dress.collection'])->findOrFail($request->exchange_item_id);
        
        // Verify exchange item is available
        if (!in_array($exchangeItem->status, ['available', 'returned_resaleable'])) {
            return response()->json([
                'message' => 'Exchange item is not available'
            ], 400);
        }
        
        // Calculate exchange pricing using highest discount logic
        $dress = $exchangeItem->dress;
        $collection = $dress->collection;
        $originalPrice = $dress->sale_price;
        
        // Apply highest discount logic (same as POS)
        $highestDiscount = 0;
        $discountSource = '';
        $collectionDiscountPercentage = 0;
        $dressDiscountPercentage = 0;
        $sizeDiscountPercentage = 0;
        
        // Check collection discount
        if ($collection->discount_active && $collection->discount_percentage > 0) {
            if ($collection->discount_percentage > $highestDiscount) {
                $highestDiscount = $collection->discount_percentage;
                $discountSource = 'Collection';
                $collectionDiscountPercentage = $collection->discount_percentage;
            }
        }
        
        // Check dress discount
        if ($dress->discount_active && $dress->discount_percentage > 0) {
            if ($dress->discount_percentage > $highestDiscount) {
                $highestDiscount = $dress->discount_percentage;
                $discountSource = 'Style';
                $dressDiscountPercentage = $dress->discount_percentage;
                $collectionDiscountPercentage = 0; // Reset previous
            }
        }
        
        // Check size discount
        if ($exchangeItem->size_discount_active && $exchangeItem->size_discount_percentage > 0) {
            if ($exchangeItem->size_discount_percentage > $highestDiscount) {
                $highestDiscount = $exchangeItem->size_discount_percentage;
                $discountSource = 'Size';
                $sizeDiscountPercentage = $exchangeItem->size_discount_percentage;
                $collectionDiscountPercentage = 0; // Reset previous
                $dressDiscountPercentage = 0; // Reset previous
            }
        }
        
        // Calculate final pricing
        $discountAmount = $originalPrice * $highestDiscount / 100;
        $priceAfterDiscount = $originalPrice - $discountAmount;
        $taxAmount = $originalPrice * $dress->tax_percentage / 100;
        $finalPrice = $priceAfterDiscount + $taxAmount;
        
        // Calculate price difference
        $returnAmount = $saleItem->item_total;
        $exchangeAmount = $finalPrice;
        $priceDifference = $exchangeAmount - $returnAmount;
        
        // Create return record for original item
        $returnItem = ReturnItem::create([
            'sale_item_id' => $saleItem->id,
            'dress_item_id' => $saleItem->dress_item_id,
            'user_id' => auth()->id() ?? 1, // Default to user 1 if not authenticated
            'return_type' => 'exchange',
            'return_reason' => $request->reason,
            'notes' => $request->notes,
            'refund_amount' => $returnAmount,
            'exchange_item_id' => $exchangeItem->id,
            'return_date' => now(),
        ]);
        
        // Update original item status and returned_at timestamp
        $saleItem->dressItem->update([
            'status' => 'returned_resaleable',
            'returned_at' => now()
        ]);
        
        // Create new sale for exchange item
        $exchangeSale = Sale::create([
            'user_id' => auth()->id() ?? 1,
            'customer_name' => $saleItem->sale->customer_name,
            'customer_phone' => $saleItem->sale->customer_phone,
            'payment_method' => 'EXCHANGE',
            'subtotal' => $priceAfterDiscount,
            'tax_amount' => $taxAmount,
            'total_discount_amount' => $discountAmount,
            'total_amount' => $finalPrice,
            'invoice_number' => 'EX-' . time() . '-' . rand(1000, 9999),
            'fbr_invoice_number' => 'FBR-EX-' . time(),
            'sale_date' => now(),
        ]);
        
        // Create sale item for exchange
        $exchangeSaleItem = SaleItem::create([
            'sale_id' => $exchangeSale->id,
            'dress_item_id' => $exchangeItem->id,
            'dress_name' => $dress->name,
            'collection_name' => $collection->name,
            'sku' => $dress->sku,
            'barcode' => $exchangeItem->barcode,
            'size' => $dress->size,
            'cost_price' => $dress->cost_price,
            'sale_price' => $originalPrice,
            'collection_discount_percentage' => $collectionDiscountPercentage,
            'dress_discount_percentage' => $dressDiscountPercentage,
            'size_discount_percentage' => $sizeDiscountPercentage,
            'total_discount_amount' => $discountAmount,
            'tax_percentage' => $dress->tax_percentage,
            'tax_amount' => $taxAmount,
            'item_total' => $finalPrice,
            'profit_amount' => $finalPrice - $dress->cost_price,
            'exchange_return_id' => $returnItem->id,
            'is_exchange_item' => true
        ]);

        // Update return item with exchange details
        $returnItem->update([
            'exchange_sale_id' => $exchangeSale->id,
            'exchange_sale_item_id' => $exchangeSaleItem->id,
        ]);
        
        // Update exchange item status
        $exchangeItem->update([
            'status' => 'sold',
            'sold_at' => now()
        ]);
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Exchange processed successfully',
            'return_id' => $returnItem->id,
            'exchange_sale_id' => $exchangeSale->id,
            'data' => [
                'return_type' => 'exchange',
                'return_amount' => $returnAmount,
                'exchange_amount' => $exchangeAmount,
                'price_difference' => $priceDifference,
                'action_required' => $priceDifference > 0 ? 'collect_payment' : ($priceDifference < 0 ? 'give_refund' : 'even_exchange'),
                'original_item' => $saleItem->dress_name,
                'exchange_item' => $dress->name,
                'exchange_invoice' => $exchangeSale->invoice_number,
                'return_date' => $returnItem->return_date->format('Y-m-d H:i:s')
            ]
        ]);
    }
    
    /**
     * Get recent returns for today
     */
    public function recent()
    {
        try {
            $returns = ReturnItem::with(['saleItem'])
                ->whereDate('return_date', today())
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
                ->map(function($return) {
                    return [
                        'id' => $return->id,
                        'dress_name' => $return->saleItem->dress_name,
                        'return_type' => $return->return_type,
                        'refund_amount' => $return->refund_amount,
                        'reason' => $return->reason,
                        'created_at' => $return->created_at->format('Y-m-d H:i:s')
                    ];
                });
            
            return response()->json([
                'returns' => $returns
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading recent returns',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get return statistics
     */
    public function stats()
    {
        try {
            $today = today();
            $thisWeek = now()->startOfWeek();
            $thisMonth = now()->startOfMonth();
            
            // Get counts
            $todayReturns = ReturnItem::whereDate('return_date', $today)->count();
            $weeklyReturns = ReturnItem::where('return_date', '>=', $thisWeek)->count();
            $monthlyReturns = ReturnItem::where('return_date', '>=', $thisMonth)->count();
            
            // Calculate return rate (returns vs sales) for this month
            $monthlySales = Sale::where('created_at', '>=', $thisMonth)->count();
            $returnRate = $monthlySales > 0 ? round(($monthlyReturns / $monthlySales) * 100, 1) : 0;
            
            // Average return value
            $averageValue = ReturnItem::where('return_date', '>=', $thisMonth)
                ->avg('refund_amount') ?? 0;
            
            return response()->json([
                'today' => $todayReturns,
                'weekly' => $weeklyReturns,
                'monthly' => $monthlyReturns,
                'return_rate' => $returnRate,
                'average_value' => round($averageValue, 2)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error loading statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
