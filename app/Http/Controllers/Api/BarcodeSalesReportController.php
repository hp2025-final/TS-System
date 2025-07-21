<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BarcodeSalesReportController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = DB::table('sale_items as si')
                ->leftJoin('sales as s', 'si.sale_id', '=', 's.id')
                ->leftJoin('users as u', 's.user_id', '=', 'u.id')
                ->leftJoin('dress_items as di', 'si.dress_item_id', '=', 'di.id')
                ->select([
                    'si.id',
                    'di.barcode',
                    'si.dress_name',
                    'si.collection_name',
                    'si.sku',
                    'si.size',
                    'si.sale_price',
                    'si.cost_price',
                    'si.total_discount_amount',
                    'si.tax_amount',
                    'si.item_total',
                    'si.profit_amount',
                    's.invoice_number',
                    's.payment_method',
                    's.sale_date',
                    's.customer_name',
                    's.customer_phone',
                    'u.name as cashier_name',
                    'u.email as cashier_email',
                    'si.created_at'
                ])
                ->orderBy('s.sale_date', 'desc')
                ->orderBy('si.created_at', 'desc');

            // Search filters
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('di.barcode', 'LIKE', "%{$search}%")
                      ->orWhere('si.dress_name', 'LIKE', "%{$search}%")
                      ->orWhere('si.collection_name', 'LIKE', "%{$search}%")
                      ->orWhere('s.invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('si.sku', 'LIKE', "%{$search}%");
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

            // Payment method filter
            if ($request->filled('payment_method')) {
                $query->where('s.payment_method', $request->get('payment_method'));
            }

            // Date range filters
            if ($request->filled('date_from')) {
                $query->whereDate('s.sale_date', '>=', $request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('s.sale_date', '<=', $request->get('date_to'));
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
            $sales = $query->paginate($perPage);

            // Get summary statistics
            $totalQuery = DB::table('sale_items as si')
                ->leftJoin('sales as s', 'si.sale_id', '=', 's.id')
                ->leftJoin('users as u', 's.user_id', '=', 'u.id')
                ->leftJoin('dress_items as di', 'si.dress_item_id', '=', 'di.id');

            // Apply same filters to summary
            if ($request->filled('search')) {
                $search = $request->get('search');
                $totalQuery->where(function ($q) use ($search) {
                    $q->where('di.barcode', 'LIKE', "%{$search}%")
                      ->orWhere('si.dress_name', 'LIKE', "%{$search}%")
                      ->orWhere('si.collection_name', 'LIKE', "%{$search}%")
                      ->orWhere('s.invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('si.sku', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('barcode')) {
                $totalQuery->where('di.barcode', $request->get('barcode'));
            }

            if ($request->filled('collection')) {
                $totalQuery->where('si.collection_name', $request->get('collection'));
            }

            if ($request->filled('payment_method')) {
                $totalQuery->where('s.payment_method', $request->get('payment_method'));
            }

            if ($request->filled('date_from')) {
                $totalQuery->whereDate('s.sale_date', '>=', $request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $totalQuery->whereDate('s.sale_date', '<=', $request->get('date_to'));
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
                SUM(si.item_total) as total_sales_amount,
                SUM(si.cost_price) as total_cost_amount,
                SUM(si.total_discount_amount) as total_discount_amount,
                SUM(si.tax_amount) as total_tax_amount
            ')->first();

            // Get filter options for dropdowns
            $collections = DB::table('sale_items')
                ->select('collection_name')
                ->distinct()
                ->whereNotNull('collection_name')
                ->orderBy('collection_name')
                ->pluck('collection_name');

            $paymentMethods = DB::table('sales')
                ->select('payment_method')
                ->distinct()
                ->orderBy('payment_method')
                ->pluck('payment_method');

            return response()->json([
                'status' => 'success',
                'data' => [
                    'sales' => $sales,
                    'summary' => $summary,
                    'filters' => [
                        'collections' => $collections,
                        'payment_methods' => $paymentMethods
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch barcode sales report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function export(Request $request)
    {
        try {
            $query = DB::table('sale_items as si')
                ->leftJoin('sales as s', 'si.sale_id', '=', 's.id')
                ->leftJoin('users as u', 's.user_id', '=', 'u.id')
                ->leftJoin('dress_items as di', 'si.dress_item_id', '=', 'di.id')
                ->select([
                    'di.barcode',
                    'si.dress_name',
                    'si.collection_name',
                    'si.sku',
                    'si.size',
                    'si.sale_price',
                    'si.cost_price',
                    'si.total_discount_amount',
                    'si.tax_amount',
                    'si.item_total',
                    's.invoice_number',
                    's.payment_method',
                    's.sale_date',
                    's.customer_name',
                    's.customer_phone',
                    'u.name as cashier_name'
                ])
                ->orderBy('s.sale_date', 'desc');

            // Apply same filters as index method
            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('di.barcode', 'LIKE', "%{$search}%")
                      ->orWhere('si.dress_name', 'LIKE', "%{$search}%")
                      ->orWhere('si.collection_name', 'LIKE', "%{$search}%")
                      ->orWhere('s.invoice_number', 'LIKE', "%{$search}%")
                      ->orWhere('si.sku', 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('barcode')) {
                $query->where('di.barcode', $request->get('barcode'));
            }

            if ($request->filled('collection')) {
                $query->where('si.collection_name', $request->get('collection'));
            }

            if ($request->filled('payment_method')) {
                $query->where('s.payment_method', $request->get('payment_method'));
            }

            if ($request->filled('date_from')) {
                $query->whereDate('s.sale_date', '>=', $request->get('date_from'));
            }

            if ($request->filled('date_to')) {
                $query->whereDate('s.sale_date', '<=', $request->get('date_to'));
            }

            if ($request->filled('customer')) {
                $query->where(function($q) use ($request) {
                    $customer = $request->get('customer');
                    $q->where('s.customer_name', 'LIKE', "%{$customer}%")
                      ->orWhere('s.customer_phone', 'LIKE', "%{$customer}%");
                });
            }

            $sales = $query->get();

            // Prepare CSV headers
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="barcode-sales-report-' . date('Y-m-d') . '.csv"',
            ];

            // Create CSV content
            $callback = function() use ($sales) {
                $file = fopen('php://output', 'w');
                
                // CSV Headers
                fputcsv($file, [
                    'Sale Date',
                    'Barcode',
                    'Dress Name',
                    'Collection',
                    'SKU',
                    'Size',
                    'Customer Name',
                    'Customer Phone',
                    'Sale Price',
                    'Cost Price',
                    'Discount Amount',
                    'Tax Amount',
                    'Item Total',
                    'Invoice Number',
                    'Payment Method',
                    'Cashier'
                ]);

                // CSV Data
                foreach ($sales as $sale) {
                    fputcsv($file, [
                        Carbon::parse($sale->sale_date)->format('Y-m-d H:i:s'),
                        $sale->barcode,
                        $sale->dress_name,
                        $sale->collection_name,
                        $sale->sku,
                        $sale->size,
                        $sale->customer_name ?: '-',
                        $sale->customer_phone ?: '-',
                        number_format($sale->sale_price, 2),
                        number_format($sale->cost_price, 2),
                        number_format($sale->total_discount_amount, 2),
                        number_format($sale->tax_amount, 2),
                        number_format($sale->item_total, 2),
                        $sale->invoice_number,
                        ucfirst($sale->payment_method),
                        $sale->cashier_name
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to export barcode sales report',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
