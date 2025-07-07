<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReturnItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\DressItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReturnController extends Controller
{
    /**
     * Display a listing of returns
     */
    public function index(Request $request)
    {
        $query = ReturnItem::with(['saleItem.dressItem.dress', 'saleItem.sale', 'processedBy'])
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $returns = $query->paginate($request->get('per_page', 15));

        return response()->json($returns);
    }

    /**
     * Store a new return request
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'items' => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.reason' => 'required|string|in:defective,wrong_size,customer_request,damaged',
            'items.*.return_type' => 'required|in:refund,exchange',
            'items.*.exchange_item_id' => 'nullable|exists:dress_items,id',
        ]);

        try {
            DB::beginTransaction();

            $sale = Sale::findOrFail($request->sale_id);
            $returnNumber = $this->generateReturnNumber();

            foreach ($request->items as $itemData) {
                $saleItem = SaleItem::with(['dressItem', 'sale'])->findOrFail($itemData['sale_item_id']);
                
                // Verify the sale item belongs to the specified sale
                if ($saleItem->sale_id !== $sale->id) {
                    return response()->json([
                        'message' => 'Sale item does not belong to the specified sale'
                    ], 400);
                }

                // Check return eligibility (within 7 days)
                if ($sale->created_at->diffInDays(now()) > 7) {
                    return response()->json([
                        'message' => 'Return period has expired (7 days limit)'
                    ], 400);
                }

                // Check quantity
                if ($itemData['quantity'] > $saleItem->quantity) {
                    return response()->json([
                        'message' => 'Return quantity cannot exceed sold quantity'
                    ], 400);
                }

                // For exchanges, validate the exchange item
                $exchangeItem = null;
                if ($itemData['return_type'] === 'exchange' && isset($itemData['exchange_item_id'])) {
                    $exchangeItem = DressItem::findOrFail($itemData['exchange_item_id']);
                    if ($exchangeItem->status !== 'available') {
                        return response()->json([
                            'message' => 'Exchange item is not available'
                        ], 400);
                    }
                }

                // Calculate refund amount
                $refundAmount = $saleItem->unit_price * $itemData['quantity'];

                // Create return record
                $return = ReturnItem::create([
                    'sale_item_id' => $saleItem->id,
                    'return_number' => $returnNumber,
                    'quantity' => $itemData['quantity'],
                    'reason' => $itemData['reason'],
                    'return_type' => $itemData['return_type'],
                    'exchange_dress_item_id' => $itemData['exchange_item_id'] ?? null,
                    'refund_amount' => $itemData['return_type'] === 'refund' ? $refundAmount : 0,
                    'status' => 'pending',
                    'requested_by' => Auth::id(),
                    'requested_at' => now(),
                ]);

                // If it's an exchange, reserve the exchange item
                if ($exchangeItem) {
                    $exchangeItem->update(['status' => 'reserved']);
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Return request submitted successfully',
                'return_number' => $returnNumber
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Return request failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified return
     */
    public function show(ReturnItem $returnItem)
    {
        $returnItem->load([
            'saleItem.dressItem.dress.collection',
            'saleItem.sale',
            'exchangeDressItem.dress',
            'processedBy',
            'requestedBy'
        ]);
        
        return response()->json($returnItem);
    }

    /**
     * Process a return (approve/reject)
     */
    public function process(Request $request, ReturnItem $returnItem)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($returnItem->status !== 'pending') {
            return response()->json([
                'message' => 'Return has already been processed'
            ], 400);
        }

        try {
            DB::beginTransaction();

            if ($request->action === 'approve') {
                // Return the original item to available status
                $returnItem->saleItem->dressItem->update([
                    'status' => 'available',
                    'sold_at' => null
                ]);

                // If it's an exchange, mark exchange item as sold
                if ($returnItem->return_type === 'exchange' && $returnItem->exchangeDressItem) {
                    $returnItem->exchangeDressItem->update([
                        'status' => 'sold',
                        'sold_at' => now()
                    ]);
                }

                $returnItem->update([
                    'status' => 'approved',
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                    'notes' => $request->notes
                ]);

            } else {
                // Reject the return
                // If it was an exchange, release the reserved item
                if ($returnItem->exchangeDressItem) {
                    $returnItem->exchangeDressItem->update(['status' => 'available']);
                }

                $returnItem->update([
                    'status' => 'rejected',
                    'processed_by' => Auth::id(),
                    'processed_at' => now(),
                    'notes' => $request->notes
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => "Return {$request->action}d successfully",
                'return' => $returnItem->fresh()->load([
                    'saleItem.dressItem.dress',
                    'exchangeDressItem.dress',
                    'processedBy'
                ])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Failed to process return',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get return statistics
     */
    public function statistics(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        $returns = ReturnItem::whereBetween('created_at', [$dateFrom, $dateTo])->get();

        $stats = [
            'total_returns' => $returns->count(),
            'approved_returns' => $returns->where('status', 'approved')->count(),
            'rejected_returns' => $returns->where('status', 'rejected')->count(),
            'pending_returns' => $returns->where('status', 'pending')->count(),
            'total_refund_amount' => $returns->where('status', 'approved')->sum('refund_amount'),
            'return_reasons' => $returns->groupBy('reason')->map->count(),
            'return_types' => $returns->groupBy('return_type')->map->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Generate unique return number
     */
    private function generateReturnNumber()
    {
        $today = now()->format('Ymd');
        $count = ReturnItem::whereDate('created_at', now())->count() + 1;
        return "RT{$today}" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}
