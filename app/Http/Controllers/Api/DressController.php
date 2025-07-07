<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dress;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Dress::with(['collection', 'dressItems']);

        // Filter by collection
        if ($request->has('collection_id')) {
            $query->where('collection_id', $request->collection_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter active dresses only
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Search by name or SKU
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $dresses = $query->withCount('dressItems')->get();

        return response()->json($dresses);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'collection_id' => 'required|exists:collections,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:dresses',
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_active' => 'boolean',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'in:active,inactive'
        ]);

        $dress = Dress::create($request->all());
        $dress->load('collection');

        return response()->json([
            'message' => 'Dress created successfully',
            'dress' => $dress
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Dress $dress)
    {
        $dress->load(['collection', 'dressItems' => function ($query) {
            $query->orderBy('size');
        }]);

        return response()->json($dress);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dress $dress)
    {
        $request->validate([
            'collection_id' => 'required|exists:collections,id',
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:dresses,sku,' . $dress->id,
            'description' => 'nullable|string',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_active' => 'boolean',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'in:active,inactive'
        ]);

        $dress->update($request->all());
        $dress->load('collection');

        return response()->json([
            'message' => 'Dress updated successfully',
            'dress' => $dress
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Dress $dress)
    {
        // Check if dress has items
        if ($dress->dressItems()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete dress with existing items'
            ], Response::HTTP_CONFLICT);
        }

        $dress->delete();

        return response()->json([
            'message' => 'Dress deleted successfully'
        ]);
    }

    /**
     * Update dress discount settings
     */
    public function updateDiscount(Request $request, Dress $dress)
    {
        $request->validate([
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'discount_active' => 'required|boolean'
        ]);

        $dress->update([
            'discount_percentage' => $request->discount_percentage,
            'discount_active' => $request->discount_active
        ]);

        return response()->json([
            'message' => 'Dress discount updated successfully',
            'dress' => $dress
        ]);
    }
}
