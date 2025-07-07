<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Collection::query();

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter active collections only
        if ($request->boolean('active_only')) {
            $query->active();
        }

        // Get collections with detailed size breakdown
        $collections = $query->with(['dresses.dressItems' => function ($query) {
            $query->where('status', 'available');
        }])->get();

        // Transform the data to include size breakdown
        $transformedCollections = $collections->map(function ($collection) {
            $totalDresses = $collection->dresses->count();
            $totalItems = 0;
            $sizeBreakdown = [
                'XS' => 0,
                'S' => 0,
                'M' => 0,
                'L' => 0,
                'XL' => 0,
            ];

            foreach ($collection->dresses as $dress) {
                foreach ($dress->dressItems as $item) {
                    $totalItems++;
                    if (isset($sizeBreakdown[$item->size])) {
                        $sizeBreakdown[$item->size]++;
                    }
                }
            }

            return [
                'id' => $collection->id,
                'name' => $collection->name,
                'description' => $collection->description,
                'discount_percentage' => $collection->discount_percentage,
                'discount_active' => $collection->discount_active,
                'status' => $collection->status,
                'created_at' => $collection->created_at,
                'updated_at' => $collection->updated_at,
                'dresses_count' => $totalDresses,
                'total_items' => $totalItems,
                'size_breakdown' => $sizeBreakdown,
            ];
        });

        return response()->json($transformedCollections);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_active' => 'boolean',
            'status' => 'in:active,inactive'
        ]);

        $collection = Collection::create($request->all());

        return response()->json([
            'message' => 'Collection created successfully',
            'collection' => $collection
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Collection $collection)
    {
        $collection->load(['dresses' => function ($query) {
            $query->with('dressItems');
        }]);

        return response()->json($collection);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Collection $collection)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_active' => 'boolean',
            'status' => 'in:active,inactive'
        ]);

        $collection->update($request->all());

        return response()->json([
            'message' => 'Collection updated successfully',
            'collection' => $collection
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection)
    {
        // Check if collection has dresses
        if ($collection->dresses()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete collection with existing dresses'
            ], Response::HTTP_CONFLICT);
        }

        $collection->delete();

        return response()->json([
            'message' => 'Collection deleted successfully'
        ]);
    }

    /**
     * Update collection discount settings
     */
    public function updateDiscount(Request $request, Collection $collection)
    {
        $request->validate([
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'discount_active' => 'required|boolean'
        ]);

        $collection->update([
            'discount_percentage' => $request->discount_percentage,
            'discount_active' => $request->discount_active
        ]);

        return response()->json([
            'message' => 'Collection discount updated successfully',
            'collection' => $collection
        ]);
    }
}
