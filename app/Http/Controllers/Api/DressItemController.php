<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DressItem;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DressItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DressItem::with(['dress.collection']);

        // Filter by dress
        if ($request->has('dress_id')) {
            $query->where('dress_id', $request->dress_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by size
        if ($request->has('size')) {
            $query->whereHas('dress', function($q) use ($request) {
                $q->where('size', $request->size);
            });
        }

        // Filter available items only (includes resaleable returns)
        if ($request->boolean('available_only')) {
            $query->resaleable(); // Use new scope that includes both available and returned_resaleable
        }

        // Search by barcode
        if ($request->has('barcode')) {
            $query->where('barcode', 'like', '%' . $request->barcode . '%');
        }

        $items = $query->get();

        return response()->json($items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dress_id' => 'required|exists:dresses,id',
            'barcode' => 'required|string|unique:dress_items',
            'size_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'size_discount_active' => 'boolean',
            'status' => 'in:available,sold,returned,damaged'
        ]);

        $dressItem = DressItem::create($request->all());
        $dressItem->load(['dress.collection']);

        return response()->json([
            'message' => 'Dress item created successfully',
            'dress_item' => $dressItem
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(DressItem $dressItem)
    {
        $dressItem->load(['dress.collection']);
        return response()->json($dressItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DressItem $dressItem)
    {
        $request->validate([
            'dress_id' => 'required|exists:dresses,id',
            'barcode' => 'required|string|unique:dress_items,barcode,' . $dressItem->id,
            'size_discount_percentage' => 'nullable|numeric|min:0|max:100',
            'size_discount_active' => 'boolean',
            'status' => 'in:available,sold,returned,damaged'
        ]);

        $dressItem->update($request->all());
        $dressItem->load(['dress.collection']);

        return response()->json([
            'message' => 'Dress item updated successfully',
            'dress_item' => $dressItem
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DressItem $dressItem)
    {
        // Check if item has been sold
        if ($dressItem->status === 'sold') {
            return response()->json([
                'message' => 'Cannot delete sold items'
            ], Response::HTTP_CONFLICT);
        }

        $dressItem->delete();

        return response()->json([
            'message' => 'Dress item deleted successfully'
        ]);
    }

    /**
     * Get dress item by barcode
     */
    public function getByBarcode($barcode)
    {
        $dressItem = DressItem::with(['dress.collection'])
            ->where('barcode', $barcode)
            ->first();

        if (!$dressItem) {
            return response()->json([
                'message' => 'Item not found'
            ], Response::HTTP_NOT_FOUND);
        }

        // Calculate pricing and discounts - apply only the highest discount
        $dress = $dressItem->dress;
        $collection = $dress->collection;
        $originalPrice = $dress->sale_price;
        $finalPrice = $originalPrice;
        $discountInfo = null;
        $highestDiscount = 0;
        $discountSource = '';

        // Check collection discount
        if ($collection->discount_active && $collection->discount_percentage > 0) {
            if ($collection->discount_percentage > $highestDiscount) {
                $highestDiscount = $collection->discount_percentage;
                $discountSource = 'Collection';
            }
        }

        // Check dress discount
        if ($dress->discount_active && $dress->discount_percentage > 0) {
            if ($dress->discount_percentage > $highestDiscount) {
                $highestDiscount = $dress->discount_percentage;
                $discountSource = 'Style';
            }
        }

        // Check size discount
        if ($dressItem->size_discount_active && $dressItem->size_discount_percentage > 0) {
            if ($dressItem->size_discount_percentage > $highestDiscount) {
                $highestDiscount = $dressItem->size_discount_percentage;
                $discountSource = 'Size';
            }
        }

        // Apply the highest discount only
        if ($highestDiscount > 0) {
            $discount = ($originalPrice * $highestDiscount / 100);
            $finalPrice -= $discount;
            $discountInfo = "{$discountSource}: -{$highestDiscount}%";
        }

        // Calculate GST on original price (before discount)
        $taxPercentage = $dress->tax_percentage ?? 18.00; // Default to 18% if not set
        $taxAmount = round(($originalPrice * $taxPercentage / 100), 2);
        $finalPriceWithTax = round($finalPrice + $taxAmount, 2);

        // Add computed fields to the response
        $dressItem->original_price = $originalPrice;
        $dressItem->final_price = round($finalPrice, 2);
        $dressItem->total_discount = round($originalPrice - $finalPrice, 2);
        $dressItem->discount_info = $discountInfo;
        $dressItem->tax_percentage = $taxPercentage;
        $dressItem->tax_amount = $taxAmount;
        $dressItem->final_price_with_tax = $finalPriceWithTax;
        
        return response()->json($dressItem);
    }

    /**
     * Validate barcode uniqueness
     */
    public function validateBarcode(Request $request)
    {
        $request->validate([
            'barcode' => 'required|string'
        ]);

        $exists = DressItem::where('barcode', $request->barcode)->exists();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Barcode already exists' : 'Barcode is available'
        ]);
    }

    /**
     * Update size-specific discount
     */
    public function updateDiscount(Request $request, DressItem $dressItem)
    {
        $request->validate([
            'size_discount_percentage' => 'required|numeric|min:0|max:100',
            'size_discount_active' => 'required|boolean'
        ]);

        $dressItem->update([
            'size_discount_percentage' => $request->size_discount_percentage,
            'size_discount_active' => $request->size_discount_active
        ]);

        return response()->json([
            'message' => 'Size discount updated successfully',
            'dress_item' => $dressItem
        ]);
    }

    /**
     * Get active discounts across all levels
     */
    public function getActiveDiscounts()
    {
        $discounts = [
            'collections' => \App\Models\Collection::withActiveDiscount()->get(),
            'dresses' => \App\Models\Dress::withActiveDiscount()->with('collection')->get(),
            'sizes' => DressItem::where('size_discount_active', true)
                ->where('size_discount_percentage', '>', 0)
                ->with(['dress.collection'])
                ->get()
        ];

        return response()->json($discounts);
    }

    /**
     * Get available items for exchange
     */
    public function getAvailableForExchange(Request $request)
    {
        $search = $request->get('search');
        
        $query = DressItem::with(['dress.collection'])
            ->resaleable(); // Include both available and returned_resaleable items
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('barcode', 'LIKE', "%{$search}%")
                  ->orWhereHas('dress', function($subQ) use ($search) {
                      $subQ->where('name', 'LIKE', "%{$search}%")
                           ->orWhere('sku', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        $items = $query->limit(20)->get();
        
        return response()->json(['data' => $items]);
    }

    /**
     * Get resaleable returned items for inventory management
     */
    public function getResaleableItems(Request $request)
    {
        $search = $request->get('search');
        
        $query = DressItem::with(['dress.collection', 'returns' => function($q) {
                $q->latest()->first();
            }])
            ->where('status', 'returned_resaleable');
            
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('barcode', 'LIKE', "%{$search}%")
                  ->orWhereHas('dress', function($subQ) use ($search) {
                      $subQ->where('name', 'LIKE', "%{$search}%")
                           ->orWhere('sku', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        $items = $query->orderBy('returned_at', 'desc')->paginate(20);
        
        return response()->json($items);
    }

    /**
     * Make a resaleable item available for sale again
     */
    public function makeAvailable(Request $request, DressItem $dressItem)
    {
        if ($dressItem->status !== 'returned_resaleable') {
            return response()->json([
                'message' => 'Item must be in returned_resaleable status to make available'
            ], 400);
        }

        $request->validate([
            'quality_check_passed' => 'required|boolean',
            'notes' => 'nullable|string|max:500'
        ]);

        if (!$request->quality_check_passed) {
            return response()->json([
                'message' => 'Quality check must pass before making item available'
            ], 400);
        }

        $dressItem->update([
            'status' => 'available',
            'returned_at' => null,
            'updated_at' => now()
        ]);

        // Log the action (you could create a separate audit log)
        \Log::info('Item made available for resale', [
            'dress_item_id' => $dressItem->id,
            'barcode' => $dressItem->barcode,
            'user_id' => auth()->id(),
            'notes' => $request->notes
        ]);

        return response()->json([
            'message' => 'Item successfully made available for sale',
            'dress_item' => $dressItem->load(['dress.collection'])
        ]);
    }

    /**
     * Mark an item as damaged (quality control rejected)
     */
    public function markDamaged(Request $request, DressItem $dressItem)
    {
        if (!in_array($dressItem->status, ['returned_resaleable', 'available'])) {
            return response()->json([
                'message' => 'Item cannot be marked as damaged from current status'
            ], 400);
        }

        $request->validate([
            'damage_reason' => 'nullable|string|max:500'
        ]);

        $previousStatus = $dressItem->status;
        
        $dressItem->update([
            'status' => 'damaged',
            'updated_at' => now()
        ]);

        // Log the action
        \Log::info('Item marked as damaged', [
            'dress_item_id' => $dressItem->id,
            'barcode' => $dressItem->barcode,
            'previous_status' => $previousStatus,
            'user_id' => auth()->id(),
            'damage_reason' => $request->damage_reason
        ]);

        return response()->json([
            'message' => 'Item successfully marked as damaged',
            'dress_item' => $dressItem->load(['dress.collection'])
        ]);
    }
}
