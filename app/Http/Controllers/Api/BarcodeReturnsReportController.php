<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarcodeReturnsReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DB::table('returns as r')
                ->leftJoin('sale_items as si', 'r.sale_item_id', '=', 'si.id')
                ->leftJoin('sales as s', 'si.sale_id', '=', 's.id')
                ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
                ->leftJoin('dress_items as di', 'si.dress_item_id', '=', 'di.id')
                ->select([
                    'r.id',
                    'di.barcode',
                    'si.dress_name',
                    'si.collection_name',
                    'si.sku',
                    'si.size',
                    'r.refund_amount',
                    'r.return_type',
                    'r.return_reason as reason',
                    's.invoice_number',
                    's.customer_name',
                    's.customer_phone',
                    's.sale_date as original_sale_date',
                    'r.return_date',
                    'u.name as processed_by_name',
                    'r.created_at',
                    'r.notes'
                ])
                ->orderBy('r.return_date', 'desc')
                ->orderBy('r.created_at', 'desc');

            // Search filters
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('di.barcode', 'LIKE', "%{$search}%")
                      ->orWhere('si.dress_name', 'LIKE', "%{$search}%")
                      ->orWhere('si.collection_name', 'LIKE', "%{$search}%")
                      ->orWhere('s.invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('si.sku', 'LIKE', "%{$search}%")
                      ->orWhere('r.return_reason', 'LIKE', "%{$search}%");
                });
            }

            // Barcode filter
            if ($request->filled('barcode')) {
                $query->where('di.barcode', $request->get('barcode'));
            }

            // Collection filter
            if ($request->filled('collection')) {
                $query->where('si.collection_name', $request->get('collection'));
            }

            // Return type filter
            if ($request->filled('return_type')) {
                $query->where('r.return_type', $request->get('return_type'));
            }

            // Date range filters
            if ($request->filled('date_from')) {
                $query->whereDate('r.return_date', '>=', $request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('r.return_date', '<=', $request->get('date_to'));
            }

            // Customer filter
            if ($request->filled('customer')) {
                $query->where(function($q) use ($request) {
                    $customer = $request->get('customer');
                    $q->where('s.customer_name', 'LIKE', "%{$customer}%")
                      ->orWhere('s.customer_phone', 'LIKE', "%{$customer}%");
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 50);
            $returns = $query->paginate($perPage);

            // Get summary statistics
            $totalQuery = DB::table('returns as r')
                ->leftJoin('sale_items as si', 'r.sale_item_id', '=', 'si.id')
                ->leftJoin('sales as s', 'si.sale_id', '=', 's.id')
                ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
                ->leftJoin('dress_items as di', 'si.dress_item_id', '=', 'di.id');

            // Apply same filters to summary
            if ($request->filled('search')) {
                $search = $request->get('search');
                $totalQuery->where(function ($q) use ($search) {
                    $q->where('di.barcode', 'LIKE', "%{$search}%")
                      ->orWhere('si.dress_name', 'LIKE', "%{$search}%")
                      ->orWhere('si.collection_name', 'LIKE', "%{$search}%")
                      ->orWhere('s.invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('si.sku', 'LIKE', "%{$search}%")
                      ->orWhere('r.return_reason', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('barcode')) {
                $totalQuery->where('di.barcode', $request->get('barcode'));
            }

            if ($request->filled('collection')) {
                $totalQuery->where('si.collection_name', $request->get('collection'));
            }

            if ($request->filled('return_type')) {
                $totalQuery->where('r.return_type', $request->get('return_type'));
            }

            if ($request->filled('date_from')) {
                $totalQuery->whereDate('r.return_date', '>=', $request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $totalQuery->whereDate('r.return_date', '<=', $request->get('date_to'));
            }

            if ($request->filled('customer')) {
                $totalQuery->where(function($q) use ($request) {
                    $customer = $request->get('customer');
                    $q->where('s.customer_name', 'LIKE', "%{$customer}%")
                      ->orWhere('s.customer_phone', 'LIKE', "%{$customer}%");
                });
            }

            $summary = $totalQuery->selectRaw('
                COUNT(*) as total_items,
                SUM(r.refund_amount) as total_refunds,
                0 as total_exchanges,
                COUNT(CASE WHEN r.return_type = "return" THEN 1 END) as total_refund_count,
                COUNT(CASE WHEN r.return_type = "exchange" THEN 1 END) as total_exchange_count
            ')->first();

            // Get filter options for dropdowns
            $collections = DB::table('returns as r')
                ->leftJoin('sale_items as si', 'r.sale_item_id', '=', 'si.id')
                ->select('si.collection_name')
                ->distinct()
                ->whereNotNull('si.collection_name')
                ->orderBy('si.collection_name')
                ->pluck('si.collection_name');

            $returnTypes = ['return', 'exchange'];

            return response()->json([
                'status' => 'success',
                'data' => [
                    'returns' => $returns,
                    'summary' => $summary,
                    'filters' => [
                        'collections' => $collections,
                        'return_types' => $returnTypes
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch barcode returns report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $query = DB::table('returns as r')
                ->leftJoin('sale_items as si', 'r.sale_item_id', '=', 'si.id')
                ->leftJoin('sales as s', 'si.sale_id', '=', 's.id')
                ->leftJoin('users as u', 'r.user_id', '=', 'u.id')
                ->leftJoin('dress_items as di', 'si.dress_item_id', '=', 'di.id')
                ->select([
                    'di.barcode',
                    'si.dress_name',
                    'si.collection_name',
                    'si.sku',
                    'si.size',
                    'r.refund_amount',
                    'r.return_type',
                    'r.return_reason as reason',
                    's.invoice_number',
                    's.customer_name',
                    's.customer_phone',
                    's.sale_date as original_sale_date',
                    'r.return_date',
                    'u.name as processed_by_name',
                    'r.notes'
                ])
                ->orderBy('r.return_date', 'desc');

            // Apply same filters as index method
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('di.barcode', 'LIKE', "%{$search}%")
                      ->orWhere('si.dress_name', 'LIKE', "%{$search}%")
                      ->orWhere('si.collection_name', 'LIKE', "%{$search}%")
                      ->orWhere('s.invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('si.sku', 'LIKE', "%{$search}%")
                      ->orWhere('r.return_reason', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('barcode')) {
                $query->where('di.barcode', $request->get('barcode'));
            }

            if ($request->filled('collection')) {
                $query->where('si.collection_name', $request->get('collection'));
            }

            if ($request->filled('return_type')) {
                $query->where('r.return_type', $request->get('return_type'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('r.return_date', '>=', $request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('r.return_date', '<=', $request->get('date_to'));
            }

            if ($request->filled('customer')) {
                $query->where(function($q) use ($request) {
                    $customer = $request->get('customer');
                    $q->where('s.customer_name', 'LIKE', "%{$customer}%")
                      ->orWhere('s.customer_phone', 'LIKE', "%{$customer}%");
                });
            }

            $returns = $query->get();

            // Prepare CSV headers
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="barcode-returns-report-' . date('Y-m-d') . '.csv"',
            ];

            // Create CSV content
            $callback = function() use ($returns) {
                $file = fopen('php://output', 'w');
                
                // CSV Headers
                fputcsv($file, [
                    'Return Date',
                    'Barcode',
                    'Dress Name',
                    'Collection',
                    'SKU',
                    'Size',
                    'Customer Name',
                    'Customer Phone',
                    'Return Type',
                    'Refund Amount',
                    'Reason',
                    'Invoice Number',
                    'Original Sale Date',
                    'Processed By',
                    'Notes'
                ]);

                // CSV Data
                foreach ($returns as $return) {
                    fputcsv($file, [
                        Carbon::parse($return->return_date)->format('Y-m-d H:i:s'),
                        $return->barcode,
                        $return->dress_name,
                        $return->collection_name,
                        $return->sku,
                        $return->size,
                        $return->customer_name ?: '-',
                        $return->customer_phone ?: '-',
                        ucfirst($return->return_type),
                        number_format($return->refund_amount ?? 0, 2),
                        $return->reason,
                        $return->invoice_number,
                        Carbon::parse($return->original_sale_date)->format('Y-m-d H:i:s'),
                        $return->processed_by_name,
                        $return->notes ?: '-'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to export barcode returns report',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
