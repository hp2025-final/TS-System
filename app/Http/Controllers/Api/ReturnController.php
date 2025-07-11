<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReturnItem;
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
        $query = ReturnItem::with(['saleItem.sale', 'user', 'exchangeItem.dress.collection'])
            ->orderBy('created_at', 'desc');

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('return_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('return_date', '<=', $request->date_to);
        }

        // Filter by return type
        if ($request->filled('return_type')) {
            $query->where('return_type', $request->return_type);
        }

        $returns = $query->paginate($request->get('per_page', 50));

        return response()->json($returns);
    }

    /**
     * Store a new return
     */
    public function store(Request $request)
    {
        $request->validate([
            'sale_item_id' => 'required|exists:sale_items,id',
            'dress_item_id' => 'required|exists:dress_items,id',
            'return_type' => 'required|in:return,exchange',
            'return_reason' => 'required|string',
            'refund_amount' => 'required|numeric|min:0',
            'exchange_item_id' => 'nullable|exists:dress_items,id',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Verify the sale item and dress item match
            $saleItem = SaleItem::with('sale')->findOrFail($request->sale_item_id);
            $dressItem = DressItem::findOrFail($request->dress_item_id);

            // Verify that the sale item and dress item match
            if ($saleItem->dress_item_id !== $dressItem->id) {
                return response()->json([
                    'message' => 'Sale item and dress item do not match'
                ], 400);
            }

            // Check if item is actually sold
            if ($dressItem->status !== 'sold') {
                return response()->json([
                    'message' => 'Item must be in sold status to be returned/exchanged'
                ], 400);
            }

            // Check if this item has already been returned or exchanged
            $existingReturn = ReturnItem::where('sale_item_id', $request->sale_item_id)
                ->where('dress_item_id', $request->dress_item_id)
                ->first();
            
            if ($existingReturn) {
                return response()->json([
                    'message' => 'This item has already been returned/exchanged on ' . $existingReturn->return_date->format('Y-m-d')
                ], 400);
            }

            // Check if return is within allowed timeframe (7 days)
            $saleDate = $saleItem->sale->created_at;
            if ($saleDate->diffInDays(now()) > 7) {
                return response()->json([
                    'message' => 'Return period has expired (7 days limit)'
                ], 400);
            }

            // For exchanges, validate exchange item
            if ($request->return_type === 'exchange' && $request->exchange_item_id) {
                $exchangeItem = DressItem::findOrFail($request->exchange_item_id);
                if ($exchangeItem->status !== 'available') {
                    return response()->json([
                        'message' => 'Exchange item is not available'
                    ], 400);
                }

                // Prevent exchanging with the same item
                if ($exchangeItem->id === $dressItem->id) {
                    return response()->json([
                        'message' => 'Cannot exchange item with itself'
                    ], 400);
                }

                // Mark exchange item as sold
                $exchangeItem->update(['status' => 'sold', 'sold_at' => now()]);
            }

            // Create return record
            $return = ReturnItem::create([
                'sale_item_id' => $request->sale_item_id,
                'dress_item_id' => $request->dress_item_id,
                'user_id' => Auth::id(),
                'return_reason' => $request->return_reason,
                'return_type' => $request->return_type,
                'refund_amount' => $request->refund_amount,
                'exchange_item_id' => $request->exchange_item_id,
                'return_date' => now(),
                'notes' => $request->notes,
            ]);

            // Update dress item status - returned items should not be available for sale again
            $dressItem->update([
                'status' => 'returned', // Always mark as returned, never make available again
                'returned_at' => now()
            ]);

            DB::commit();

            $return->load(['saleItem.sale', 'user']);
            
            return response()->json([
                'message' => 'Return processed successfully',
                'return' => $return
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error processing return',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified return
     */
    public function show(ReturnItem $return)
    {
        $return->load(['saleItem.sale', 'dressItem.dress.collection', 'user', 'exchangeItem.dress.collection']);
        return response()->json($return);
    }

    /**
     * Update the specified return
     */
    public function update(Request $request, ReturnItem $return)
    {
        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $return->update($request->only(['notes']));

        return response()->json([
            'message' => 'Return updated successfully',
            'return' => $return
        ]);
    }

    /**
     * Remove the specified return
     */
    public function destroy(ReturnItem $return)
    {
        // Only allow deletion if it's a recent return (within 1 hour)
        if ($return->created_at->diffInHours(now()) > 1) {
            return response()->json([
                'message' => 'Cannot delete returns older than 1 hour'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Restore dress item status
            if ($return->dressItem) {
                $return->dressItem->update([
                    'status' => 'sold',
                    'returned_at' => null
                ]);
            }

            // If it was an exchange, restore exchange item status
            if ($return->exchangeItem) {
                $return->exchangeItem->update([
                    'status' => 'available',
                    'sold_at' => null
                ]);
            }

            $return->delete();

            DB::commit();

            return response()->json([
                'message' => 'Return deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Error deleting return',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
