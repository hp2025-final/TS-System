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
            $query->where('size', $request->size);
        }

        // Filter available items only
        if ($request->boolean('available_only')) {
            $query->available();
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
            'size' => 'required|in:XS,S,M,L,XL,XXL',
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
            'size' => 'required|in:XS,S,M,L,XL,XXL',
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

        // Add computed fields to the response
        $dressItem->original_price = $originalPrice;
        $dressItem->final_price = round($finalPrice, 2);
        $dressItem->total_discount = round($originalPrice - $finalPrice, 2);
        $dressItem->discount_info = $discountInfo;
        
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
}
