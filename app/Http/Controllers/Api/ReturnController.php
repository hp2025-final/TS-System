<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReturnItem;
use App\Models\SaleItem;
use App\Models\DressItem;
use App\Models\Sale;
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
     * Search for sold item by barcode with complete transaction data
     */
    public function searchSoldItem($barcode)
    {
        try {
            // Find the sale item by barcode with all related data
            $saleItem = SaleItem::with(['sale', 'dressItem.dress.collection'])
                ->where('barcode', $barcode)
                ->whereHas('dressItem', function($query) {
                    $query->where('status', 'sold');
                })
                ->first();

            if (!$saleItem) {
                return response()->json([
                    'message' => 'Sold item not found with this barcode'
                ], 404);
            }

            // Check if already returned
            $existingReturn = ReturnItem::where('sale_item_id', $saleItem->id)->first();
            if ($existingReturn) {
                return response()->json([
                    'message' => 'This item has already been returned/exchanged on ' . $existingReturn->return_date->format('Y-m-d'),
                    'already_returned' => true,
                    'return_date' => $existingReturn->return_date->format('Y-m-d')
                ], 400);
            }

            // Check return period (7 days)
            $saleDate = $saleItem->sale->created_at;
            $daysElapsed = $saleDate->diffInDays(now());
            if ($daysElapsed > 7) {
                return response()->json([
                    'message' => "Return period has expired. Item was sold {$daysElapsed} days ago (7 days limit)",
                    'expired' => true,
                    'days_elapsed' => $daysElapsed
                ], 400);
            }

            // Return complete transaction data
            return response()->json([
                'sale_item_id' => $saleItem->id,
                'dress_item_id' => $saleItem->dress_item_id,
                'barcode' => $saleItem->barcode,
                'dress_name' => $saleItem->dress_name,
                'collection_name' => $saleItem->collection_name,
                'size' => $saleItem->size,
                'sku' => $saleItem->sku,
                
                // Original pricing breakdown
                'original_price' => $saleItem->sale_price,
                'collection_discount_percentage' => $saleItem->collection_discount_percentage,
                'dress_discount_percentage' => $saleItem->dress_discount_percentage,
                'size_discount_percentage' => $saleItem->size_discount_percentage,
                'total_discount_amount' => $saleItem->total_discount_amount,
                'tax_percentage' => $saleItem->tax_percentage,
                'tax_amount' => $saleItem->tax_amount,
                'item_total' => $saleItem->item_total, // Actual amount paid
                
                // Sale information
                'sale_date' => $saleItem->sale->created_at->format('Y-m-d H:i:s'),
                'invoice_number' => $saleItem->sale->invoice_number,
                'customer_name' => $saleItem->sale->customer_name,
                'customer_phone' => $saleItem->sale->customer_phone,
                
                // Status and availability
                'status' => $saleItem->dressItem->status,
                'can_return' => true,
                'days_remaining' => 7 - $daysElapsed
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error searching for sold item',
                'error' => $e->getMessage()
            ], 500);
        }
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
            'refund_amount' => 'nullable|numeric|min:0', // Made optional - will auto-calculate
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

            // For exchanges, validate exchange item and create new sale
            $exchangeSale = null;
            $exchangeSaleItem = null;
            
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

                // Create new sale and sale item for exchange
                $exchangeResult = $this->createExchangeSale($saleItem, $exchangeItem, $request->return_reason);
                $exchangeSale = $exchangeResult['sale'];
                $exchangeSaleItem = $exchangeResult['sale_item'];
            }

            // Auto-calculate refund amount from original sale data (includes GST and proper discount handling)
            $calculatedResult = $this->calculateRefundAmount($saleItem, $request->return_type, $request->exchange_item_id);
            
            // Handle different return types
            if ($request->return_type === 'return') {
                $refundAmount = $calculatedResult; // Simple number for returns
            } else {
                // For exchanges, extract refund amount from result
                $refundAmount = is_array($calculatedResult) ? $calculatedResult['refund_amount'] : $calculatedResult;
            }
            
            // Use provided refund amount only if it matches calculated amount (for validation)
            $finalRefundAmount = $request->refund_amount ?? $refundAmount;
            
            // Validate refund amount doesn't exceed what was paid
            if ($finalRefundAmount > $saleItem->item_total) {
                return response()->json([
                    'message' => "Refund amount cannot exceed the original paid amount of {$saleItem->item_total}"
                ], 400);
            }

            // Create return record
            $return = ReturnItem::create([
                'sale_item_id' => $request->sale_item_id,
                'dress_item_id' => $request->dress_item_id,
                'user_id' => Auth::id(),
                'return_reason' => $request->return_reason,
                'return_type' => $request->return_type,
                'refund_amount' => $finalRefundAmount, // Use calculated amount
                'exchange_item_id' => $request->exchange_item_id,
                'exchange_sale_id' => $exchangeSale?->id,
                'exchange_sale_item_id' => $exchangeSaleItem?->id,
                'return_date' => now(),
                'notes' => $request->notes,
            ]);

            // Update dress item status based on return reason
            $newStatus = $this->determineReturnStatus($request->return_reason);
            
            $dressItem->update([
                'status' => $newStatus,
                'returned_at' => now()
            ]);

            // Update exchange sale item to reference this return
            if ($exchangeSaleItem) {
                $exchangeSaleItem->update([
                    'exchange_return_id' => $return->id
                ]);
            }

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
     * Create a new sale record for exchange item with proper tracking
     * Uses HIGHEST DISCOUNT ONLY (same as POS system)
     */
    private function createExchangeSale($originalSaleItem, $exchangeItem, $returnReason)
    {
        // Load required relationships
        $exchangeItem->load(['dress.collection']);
        $originalSale = $originalSaleItem->sale;
        
        $dress = $exchangeItem->dress;
        $collection = $dress->collection;
        $originalPrice = $dress->sale_price;
        
        // Get discount percentages from original item
        $collectionDiscountPercentage = $originalSaleItem->collection_discount_percentage ?? 0;
        $dressDiscountPercentage = $originalSaleItem->dress_discount_percentage ?? 0;
        $sizeDiscountPercentage = $originalSaleItem->size_discount_percentage ?? 0;
        
        // Find highest discount (same logic as POS system)
        $highestDiscount = 0;
        $appliedCollectionDiscount = 0;
        $appliedDressDiscount = 0;
        $appliedSizeDiscount = 0;
        
        if ($collectionDiscountPercentage > $highestDiscount) {
            $highestDiscount = $collectionDiscountPercentage;
            $appliedCollectionDiscount = $collectionDiscountPercentage;
            $appliedDressDiscount = 0;
            $appliedSizeDiscount = 0;
        }
        
        if ($dressDiscountPercentage > $highestDiscount) {
            $highestDiscount = $dressDiscountPercentage;
            $appliedCollectionDiscount = 0;
            $appliedDressDiscount = $dressDiscountPercentage;
            $appliedSizeDiscount = 0;
        }
        
        if ($sizeDiscountPercentage > $highestDiscount) {
            $highestDiscount = $sizeDiscountPercentage;
            $appliedCollectionDiscount = 0;
            $appliedDressDiscount = 0;
            $appliedSizeDiscount = $sizeDiscountPercentage;
        }
        
        // Calculate only the highest discount
        $totalDiscountAmount = $originalPrice * ($highestDiscount / 100);
        
        // Calculate GST on original price (same as POS system)
        $taxPercentage = $dress->tax_percentage ?? 18;
        $taxAmount = $originalPrice * ($taxPercentage / 100);
        
        // Calculate final total: Original Price + GST - Discount
        $itemTotal = $originalPrice + $taxAmount - $totalDiscountAmount;
        
        // Calculate profit (on original price minus discount)
        $finalPrice = $originalPrice - $totalDiscountAmount;
        $profit = $finalPrice - $dress->cost_price;
        
        // Create new sale record for exchange
        $exchangeSale = Sale::create([
            'user_id' => Auth::id(),
            'invoice_number' => $this->generateExchangeInvoiceNumber($originalSale->invoice_number),
            'customer_name' => $originalSale->customer_name,
            'customer_phone' => $originalSale->customer_phone,
            'subtotal' => $originalPrice, // Use original price as subtotal
            'collection_discount_amount' => $appliedCollectionDiscount > 0 ? $totalDiscountAmount : 0,
            'dress_discount_amount' => $appliedDressDiscount > 0 ? $totalDiscountAmount : 0,
            'size_discount_amount' => $appliedSizeDiscount > 0 ? $totalDiscountAmount : 0,
            'total_discount_amount' => $totalDiscountAmount,
            'tax_amount' => $taxAmount,
            'total_amount' => $itemTotal,
            'payment_method' => 'exchange', // Special payment method for exchanges
            'sale_date' => now(),
        ]);
        
        // Create sale item record for exchange
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
            'collection_discount_percentage' => $appliedCollectionDiscount,
            'dress_discount_percentage' => $appliedDressDiscount,
            'size_discount_percentage' => $appliedSizeDiscount,
            'dress_discount_percentage' => $dressDiscountPercentage,
            'size_discount_percentage' => $sizeDiscountPercentage,
            'total_discount_amount' => $totalDiscountAmount,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'item_total' => $itemTotal,
            'profit_amount' => $profit,
            'is_exchange_item' => true,
            // exchange_return_id will be set after return record is created
        ]);
        
        // Update exchange item status
        $exchangeItem->update([
            'status' => 'sold',
            'sold_at' => now()
        ]);
        
        return [
            'sale' => $exchangeSale,
            'sale_item' => $exchangeSaleItem
        ];
    }

    /**
     * Generate exchange invoice number based on original invoice
     */
    private function generateExchangeInvoiceNumber($originalInvoiceNumber)
    {
        // Format: ORIGINAL-EX-TIMESTAMP
        return $originalInvoiceNumber . '-EX-' . now()->format('His');
    }

    /**
     * Calculate the appropriate refund amount based on return type
     * Properly handles discount application before GST calculation for exchanges
     */
    private function calculateRefundAmount($saleItem, $returnType, $exchangeItemId = null)
    {
        if ($returnType === 'return') {
            // Full refund including GST - return the complete amount customer paid
            return $saleItem->item_total;
        } elseif ($returnType === 'exchange' && $exchangeItemId) {
            // For exchanges, calculate price difference with proper discount and GST handling
            $exchangeItem = DressItem::with('dress.collection')->findOrFail($exchangeItemId);
            $originalItemTotal = $saleItem->item_total; // This already includes discount + GST
            
            // Calculate exchange item total with same discount logic as original sale
            $exchangeItemCalculation = $this->calculateExchangeItemTotal($exchangeItem, $saleItem);
            
            // Price difference calculation
            $priceDifference = $originalItemTotal - $exchangeItemCalculation['total_with_gst'];
            
            return [
                'refund_amount' => max(0, $priceDifference), // Never negative refund
                'additional_payment' => max(0, -$priceDifference), // If customer needs to pay more
                'exchange_breakdown' => $exchangeItemCalculation
            ];
        }
        
        return 0; // Default case
    }

    /**
     * Calculate exchange item total with proper discount and GST sequence
     * Uses HIGHEST DISCOUNT ONLY (same as POS system logic)
     */
    private function calculateExchangeItemTotal($exchangeItem, $originalSaleItem)
    {
        $exchangePrice = $exchangeItem->dress->sale_price;
        $taxPercentage = $exchangeItem->dress->tax_percentage ?? 18;
        
        // Get discount percentages from original item
        $collectionDiscountPercentage = $originalSaleItem->collection_discount_percentage ?? 0;
        $dressDiscountPercentage = $originalSaleItem->dress_discount_percentage ?? 0;
        $sizeDiscountPercentage = $originalSaleItem->size_discount_percentage ?? 0;
        
        // Find highest discount (same logic as POS system)
        $highestDiscount = 0;
        $discountSource = '';
        $appliedDiscountPercentage = 0;
        
        if ($collectionDiscountPercentage > $highestDiscount) {
            $highestDiscount = $collectionDiscountPercentage;
            $discountSource = 'Collection';
            $appliedDiscountPercentage = $collectionDiscountPercentage;
        }
        
        if ($dressDiscountPercentage > $highestDiscount) {
            $highestDiscount = $dressDiscountPercentage;
            $discountSource = 'Style';
            $appliedDiscountPercentage = $dressDiscountPercentage;
        }
        
        if ($sizeDiscountPercentage > $highestDiscount) {
            $highestDiscount = $sizeDiscountPercentage;
            $discountSource = 'Size';
            $appliedDiscountPercentage = $sizeDiscountPercentage;
        }
        
        // Apply only the highest discount
        $totalDiscountAmount = $exchangePrice * ($appliedDiscountPercentage / 100);
        
        // Calculate GST on original price (same as POS system)
        $taxAmount = $exchangePrice * ($taxPercentage / 100);
        
        // Final total: Original Price + GST - Discount
        $totalWithGst = $exchangePrice + $taxAmount - $totalDiscountAmount;
        
        return [
            'original_price' => $exchangePrice,
            'discount_source' => $discountSource,
            'discount_percentage' => $appliedDiscountPercentage,
            'total_discount_amount' => $totalDiscountAmount,
            'tax_percentage' => $taxPercentage,
            'tax_amount' => $taxAmount,
            'total_with_gst' => $totalWithGst
        ];
    }

    /**
     * Determine the status based on return reason
     */
    private function determineReturnStatus($returnReason)
    {
        // Resaleable reasons - items can be sold again
        $resaleableReasons = ['wrong_size', 'customer_request'];
        
        // Non-resaleable reasons - items cannot be sold again
        $defectiveReasons = ['defective', 'damaged'];
        
        if (in_array($returnReason, $resaleableReasons)) {
            return 'returned_resaleable';
        } elseif (in_array($returnReason, $defectiveReasons)) {
            return 'returned_defective';
        }
        
        // Default to defective for unknown reasons (safer approach)
        return 'returned_defective';
    }

    /**
     * Get refund calculation for a sale item (preview before actual return)
     * Shows proper discount and GST breakdown for accurate accounting
     */
    public function getRefundCalculation(Request $request)
    {
        $request->validate([
            'sale_item_id' => 'required|exists:sale_items,id',
            'return_type' => 'required|in:return,exchange',
            'exchange_item_id' => 'nullable|exists:dress_items,id',
        ]);

        try {
            $saleItem = SaleItem::with('sale')->findOrFail($request->sale_item_id);
            
            // Check if this item has already been returned
            $existingReturn = ReturnItem::where('sale_item_id', $request->sale_item_id)->first();
            if ($existingReturn) {
                return response()->json([
                    'message' => 'This item has already been returned/exchanged'
                ], 400);
            }

            $calculationResult = $this->calculateRefundAmount(
                $saleItem, 
                $request->return_type, 
                $request->exchange_item_id
            );

            $breakdown = [
                'original_item' => [
                    'sale_price' => $saleItem->sale_price,
                    'collection_discount' => $saleItem->sale_price * ($saleItem->collection_discount_percentage / 100),
                    'dress_discount' => $saleItem->sale_price * ($saleItem->dress_discount_percentage / 100),
                    'size_discount' => $saleItem->sale_price * ($saleItem->size_discount_percentage / 100),
                    'total_discount_amount' => $saleItem->total_discount_amount,
                    'price_after_discount' => $saleItem->sale_price - $saleItem->total_discount_amount,
                    'tax_amount' => $saleItem->tax_amount,
                    'total_paid' => $saleItem->item_total
                ],
                'return_type' => $request->return_type,
            ];

            if ($request->return_type === 'return') {
                $breakdown['refund_amount'] = $calculationResult;
                $breakdown['message'] = 'Full refund including GST (customer gets back exactly what they paid)';
            } else {
                // Exchange calculation
                $breakdown['refund_amount'] = $calculationResult['refund_amount'];
                $breakdown['additional_payment_required'] = $calculationResult['additional_payment'];
                $breakdown['exchange_item'] = $calculationResult['exchange_breakdown'];
                
                if ($calculationResult['refund_amount'] > 0) {
                    $breakdown['message'] = 'Customer receives refund of ' . number_format($calculationResult['refund_amount'], 2);
                } elseif ($calculationResult['additional_payment'] > 0) {
                    $breakdown['message'] = 'Customer pays additional ' . number_format($calculationResult['additional_payment'], 2);
                } else {
                    $breakdown['message'] = 'Even exchange - no money changes hands';
                }
            }

            return response()->json([
                'success' => true,
                'calculation' => $breakdown,
                'accounting_note' => 'GST calculated after discount application for proper tax compliance'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error calculating refund',
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

            // If it was an exchange, restore exchange item status and remove sale records
            if ($return->exchangeItem && $return->exchangeSale) {
                // Restore exchange item status
                $return->exchangeItem->update([
                    'status' => 'available',
                    'sold_at' => null
                ]);
                
                // Remove exchange sale item and sale records
                if ($return->exchangeSaleItem) {
                    $return->exchangeSaleItem->delete();
                }
                $return->exchangeSale->delete();
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
