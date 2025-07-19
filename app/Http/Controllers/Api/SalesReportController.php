<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::now()->endOfDay()->toDateString());
        $groupBy = $request->get('group_by', 'day'); // day, week, month

        try {
            $salesData = $this->getSalesData($dateFrom, $dateTo, $groupBy);
            $summary = $this->getSalesSummary($dateFrom, $dateTo);
            $topItems = $this->getTopSellingItems($dateFrom, $dateTo);
            $topCollections = $this->getTopSellingCollections($dateFrom, $dateTo);
            $hourlyData = $this->getHourlySalesData($dateFrom, $dateTo);

            return response()->json([
                'success' => true,
                'data' => [
                    'sales_data' => $salesData,
                    'summary' => $summary,
                    'top_items' => $topItems,
                    'top_collections' => $topCollections,
                    'hourly_data' => $hourlyData,
                    'filters' => [
                        'date_from' => $dateFrom,
                        'date_to' => $dateTo,
                        'group_by' => $groupBy
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating sales report: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getSalesData($dateFrom, $dateTo, $groupBy)
    {
        $dateFormat = match($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        return DB::table('sales')
            ->select([
                DB::raw("DATE_FORMAT(created_at, '$dateFormat') as period"),
                DB::raw('COUNT(*) as total_sales'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('AVG(total_amount) as average_sale'),
                DB::raw('MIN(DATE(created_at)) as sale_date')
            ])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }

    private function getSalesSummary($dateFrom, $dateTo)
    {
        $summary = DB::table('sales')
            ->select([
                DB::raw('COUNT(*) as total_transactions'),
                DB::raw('SUM(total_amount) as total_revenue'),
                DB::raw('SUM(tax_amount) as total_tax'),
                DB::raw('SUM(total_discount_amount) as total_discounts'),
                DB::raw('AVG(total_amount) as average_transaction'),
                DB::raw('COUNT(DISTINCT DATE(created_at)) as active_days')
            ])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->first();

        // Get item count
        $itemsSold = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$dateFrom, $dateTo])
            ->count();

        // Get profit estimation
        $profitData = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select([
                DB::raw('SUM(profit_amount) as total_profit'),
                DB::raw('AVG(profit_amount) as average_profit_per_item')
            ])
            ->whereBetween('sales.created_at', [$dateFrom, $dateTo])
            ->first();

        return [
            'total_transactions' => $summary->total_transactions ?? 0,
            'total_revenue' => $summary->total_revenue ?? 0,
            'total_tax' => $summary->total_tax ?? 0,
            'total_discounts' => $summary->total_discounts ?? 0,
            'average_transaction' => $summary->average_transaction ?? 0,
            'active_days' => $summary->active_days ?? 0,
            'items_sold' => $itemsSold,
            'total_profit' => $profitData->total_profit ?? 0,
            'average_profit_per_item' => $profitData->average_profit_per_item ?? 0,
            'daily_average' => $summary->active_days > 0 ? ($summary->total_revenue ?? 0) / $summary->active_days : 0
        ];
    }

    private function getTopSellingItems($dateFrom, $dateTo, $limit = 10)
    {
        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select([
                'sale_items.dress_name',
                'sale_items.collection_name',
                'sale_items.size',
                DB::raw('COUNT(*) as quantity_sold'),
                DB::raw('SUM(sale_items.item_total) as total_revenue'),
                DB::raw('AVG(sale_items.sale_price) as average_price'),
                DB::raw('SUM(sale_items.profit_amount) as total_profit')
            ])
            ->whereBetween('sales.created_at', [$dateFrom, $dateTo])
            ->groupBy(['sale_items.dress_name', 'sale_items.collection_name', 'sale_items.size'])
            ->orderBy('quantity_sold', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getTopSellingCollections($dateFrom, $dateTo, $limit = 10)
    {
        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->select([
                'sale_items.collection_name',
                DB::raw('COUNT(*) as quantity_sold'),
                DB::raw('SUM(sale_items.item_total) as total_revenue'),
                DB::raw('AVG(sale_items.sale_price) as average_price'),
                DB::raw('COUNT(DISTINCT sale_items.dress_name) as unique_items'),
                DB::raw('SUM(sale_items.profit_amount) as total_profit')
            ])
            ->whereBetween('sales.created_at', [$dateFrom, $dateTo])
            ->groupBy('sale_items.collection_name')
            ->orderBy('total_revenue', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getHourlySalesData($dateFrom, $dateTo)
    {
        return DB::table('sales')
            ->select([
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as transaction_count'),
                DB::raw('SUM(total_amount) as revenue'),
                DB::raw('AVG(total_amount) as average_transaction')
            ])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();
    }

    public function exportSales(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::now()->endOfDay()->toDateString());

        try {
            $sales = DB::table('sales')
                ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
                ->select([
                    'sales.invoice_number',
                    'sales.created_at as sale_date',
                    'sales.customer_name',
                    'sales.customer_phone',
                    'sale_items.dress_name',
                    'sale_items.collection_name',
                    'sale_items.size',
                    'sale_items.barcode',
                    'sale_items.cost_price',
                    'sale_items.sale_price',
                    'sale_items.total_discount_amount',
                    'sale_items.tax_amount',
                    'sale_items.item_total',
                    'sale_items.profit_amount',
                    'sales.total_amount as invoice_total'
                ])
                ->whereBetween('sales.created_at', [$dateFrom, $dateTo])
                ->orderBy('sales.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sales,
                'summary' => $this->getSalesSummary($dateFrom, $dateTo)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting sales data: ' . $e->getMessage()
            ], 500);
        }
    }
}
