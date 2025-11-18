<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DressItem;
use App\Models\Collection;
use App\Models\Dress;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BarcodeListController extends Controller
{
    /**
     * Get barcode list with filters
     */
    public function index(Request $request)
    {
        $query = DressItem::with(['dress.collection']);

        // Date filters
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Collection filter
        if ($request->has('collection_id') && $request->collection_id) {
            $query->whereHas('dress', function($q) use ($request) {
                $q->where('collection_id', $request->collection_id);
            });
        }

        // Dress filter
        if ($request->has('dress_id') && $request->dress_id) {
            $query->where('dress_id', $request->dress_id);
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Get paginated results
        $items = $query->orderBy('created_at', 'desc')->paginate(50);

        return response()->json([
            'items' => $items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'barcode' => $item->barcode,
                    'status' => $item->status,
                    'sale_price' => $item->dress->sale_price,
                    'collection_name' => $item->dress->collection->name,
                    'dress_name' => $item->dress->name,
                    'size' => $item->dress->size,
                    'dress_sku' => $item->dress->sku,
                    'created_at' => $item->created_at->format('d-m-y'),
                    'updated_at' => $item->updated_at->format('d-m-y H:i'),
                    'sold_at' => $item->sold_at ? Carbon::parse($item->sold_at)->format('d-m-y') : null,
                    'returned_at' => $item->returned_at ? Carbon::parse($item->returned_at)->format('d-m-y') : null,
                ];
            }),
            'pagination' => [
                'total' => $items->total(),
                'per_page' => $items->perPage(),
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
            ]
        ]);
    }

    /**
     * Export to Excel (CSV format)
     */
    public function export(Request $request)
    {
        $query = DressItem::with(['dress.collection']);

        // Apply same filters as index
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('collection_id') && $request->collection_id) {
            $query->whereHas('dress', function($q) use ($request) {
                $q->where('collection_id', $request->collection_id);
            });
        }

        if ($request->has('dress_id') && $request->dress_id) {
            $query->where('dress_id', $request->dress_id);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $items = $query->orderBy('created_at', 'desc')->get();

        // Create CSV content
        $headers = [
            'Date (Created)',
            'Date (Updated)',
            'Barcode',
            'Collection',
            'Dress',
            'Size',
            'Status',
            'Sale Price',
            'Date (Sold)',
            'Date (Returned)',
            'Dress SKU'
        ];

        $csvContent = implode(',', $headers) . "\n";

        foreach ($items as $item) {
            $row = [
                $item->created_at->format('d-m-y'),
                $item->updated_at->format('d-m-y H:i'),
                $item->barcode,
                $item->dress->collection->name,
                $item->dress->name,
                $item->dress->size,
                $item->status,
                $item->dress->sale_price,
                $item->sold_at ? Carbon::parse($item->sold_at)->format('d-m-y') : '',
                $item->returned_at ? Carbon::parse($item->returned_at)->format('d-m-y') : '',
                $item->dress->sku,
            ];

            // Escape commas and quotes in data
            $row = array_map(function($field) {
                if (strpos($field, ',') !== false || strpos($field, '"') !== false) {
                    return '"' . str_replace('"', '""', $field) . '"';
                }
                return $field;
            }, $row);

            $csvContent .= implode(',', $row) . "\n";
        }

        $filename = 'barcode_list_' . date('Y-m-d_His') . '.csv';

        return response($csvContent, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get filter options
     */
    public function filterOptions()
    {
        return response()->json([
            'collections' => Collection::select('id', 'name')->orderBy('name')->get(),
            'dresses' => Dress::select('id', 'name', 'collection_id')->orderBy('name')->get(),
            'statuses' => [
                ['value' => 'available', 'label' => 'Available'],
                ['value' => 'sold', 'label' => 'Sold'],
                ['value' => 'returned_defective', 'label' => 'Returned Defective'],
                ['value' => 'returned_resaleable', 'label' => 'Returned Resaleable'],
                ['value' => 'damaged', 'label' => 'Damaged'],
                ['value' => 'retrieved_ho', 'label' => 'Retrieved HO'],
            ]
        ]);
    }
}
