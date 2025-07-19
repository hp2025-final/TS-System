<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReturnsReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::now()->endOfDay()->toDateString());
        $groupBy = $request->get('group_by', 'day'); // day, week, month

        try {
            $returnsData = $this->getReturnsData($dateFrom, $dateTo, $groupBy);
            $summary = $this->getReturnsSummary($dateFrom, $dateTo);
            $reasonsBreakdown = $this->getReturnReasons($dateFrom, $dateTo);
            $topReturnedItems = $this->getTopReturnedItems($dateFrom, $dateTo);
            $exchangeData = $this->getExchangeData($dateFrom, $dateTo);

            return response()->json([
                'success' => true,
                'data' => [
                    'returns_data' => $returnsData,
                    'summary' => $summary,
                    'reasons_breakdown' => $reasonsBreakdown,
                    'top_returned_items' => $topReturnedItems,
                    'exchange_data' => $exchangeData,
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
                'message' => 'Error generating returns report: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getReturnsData($dateFrom, $dateTo, $groupBy)
    {
        $dateFormat = match($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            default => '%Y-%m-%d'
        };

        return DB::table('returns')
            ->select([
                DB::raw("DATE_FORMAT(created_at, '$dateFormat') as period"),
                DB::raw('COUNT(*) as total_returns'),
                DB::raw('SUM(refund_amount) as total_refunds'),
                DB::raw('AVG(refund_amount) as average_refund'),
                DB::raw('SUM(CASE WHEN return_type = "exchange" THEN 1 ELSE 0 END) as total_exchanges'),
                DB::raw('SUM(CASE WHEN return_type = "return" THEN 1 ELSE 0 END) as total_refunds_count'),
                DB::raw('MIN(DATE(created_at)) as return_date')
            ])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->groupBy('period')
            ->orderBy('period')
            ->get();
    }

    private function getReturnsSummary($dateFrom, $dateTo)
    {
        $summary = DB::table('returns')
            ->select([
                DB::raw('COUNT(*) as total_returns'),
                DB::raw('SUM(refund_amount) as total_refund_amount'),
                DB::raw('AVG(refund_amount) as average_refund'),
                DB::raw('SUM(CASE WHEN return_type = "exchange" THEN 1 ELSE 0 END) as total_exchanges'),
                DB::raw('SUM(CASE WHEN return_type = "return" THEN 1 ELSE 0 END) as total_refunds'),
                DB::raw('COUNT(DISTINCT DATE(created_at)) as active_days')
            ])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->first();

        // Get sales data for comparison
        $salesSummary = DB::table('sales')
            ->select([
                DB::raw('COUNT(*) as total_sales'),
                DB::raw('SUM(total_amount) as total_sales_amount')
            ])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->first();

        // Calculate return rates
        $returnRate = $salesSummary->total_sales > 0 
            ? round(($summary->total_returns / $salesSummary->total_sales) * 100, 2) 
            : 0;

        $refundRate = $salesSummary->total_sales_amount > 0 
            ? round(($summary->total_refund_amount / $salesSummary->total_sales_amount) * 100, 2) 
            : 0;

        return [
            'total_returns' => $summary->total_returns ?? 0,
            'total_refund_amount' => $summary->total_refund_amount ?? 0,
            'average_refund' => $summary->average_refund ?? 0,
            'total_exchanges' => $summary->total_exchanges ?? 0,
            'total_refunds' => $summary->total_refunds ?? 0,
            'active_days' => $summary->active_days ?? 0,
            'daily_average_returns' => $summary->active_days > 0 ? ($summary->total_returns ?? 0) / $summary->active_days : 0,
            'daily_average_refunds' => $summary->active_days > 0 ? ($summary->total_refund_amount ?? 0) / $summary->active_days : 0,
            'return_rate_percentage' => $returnRate,
            'refund_rate_percentage' => $refundRate,
            'exchange_rate' => $summary->total_returns > 0 ? round(($summary->total_exchanges / $summary->total_returns) * 100, 2) : 0
        ];
    }

    private function getReturnReasons($dateFrom, $dateTo)
    {
        return DB::table('returns')
            ->select([
                'return_reason',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(refund_amount) as total_refund'),
                DB::raw("ROUND((COUNT(*) * 100.0 / (SELECT COUNT(*) FROM returns WHERE created_at BETWEEN '$dateFrom' AND '$dateTo')), 2) as percentage")
            ])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->whereNotNull('return_reason')
            ->groupBy('return_reason')
            ->orderBy('count', 'desc')
            ->get();
    }

    private function getTopReturnedItems($dateFrom, $dateTo, $limit = 10)
    {
        return DB::table('returns')
            ->join('sale_items', 'returns.sale_item_id', '=', 'sale_items.id')
            ->select([
                'sale_items.dress_name',
                'sale_items.collection_name',
                'sale_items.size',
                DB::raw('COUNT(*) as return_count'),
                DB::raw('SUM(returns.refund_amount) as total_refunds'),
                DB::raw('AVG(returns.refund_amount) as average_refund'),
                'returns.return_reason as common_reason'
            ])
            ->whereBetween('returns.created_at', [$dateFrom, $dateTo])
            ->groupBy(['sale_items.dress_name', 'sale_items.collection_name', 'sale_items.size'])
            ->orderBy('return_count', 'desc')
            ->limit($limit)
            ->get();
    }

    private function getExchangeData($dateFrom, $dateTo)
    {
        $exchanges = DB::table('returns')
            ->leftJoin('sales as exchange_sales', 'returns.exchange_sale_id', '=', 'exchange_sales.id')
            ->leftJoin('sale_items as original_items', 'returns.sale_item_id', '=', 'original_items.id')
            ->select([
                'returns.id',
                'returns.created_at',
                'original_items.dress_name as original_item',
                'original_items.collection_name as original_collection',
                'original_items.item_total as original_amount',
                'exchange_sales.total_amount as exchange_amount',
                DB::raw('(exchange_sales.total_amount - original_items.item_total) as price_difference'),
                'returns.return_reason'
            ])
            ->where('returns.return_type', 'exchange')
            ->whereBetween('returns.created_at', [$dateFrom, $dateTo])
            ->orderBy('returns.created_at', 'desc')
            ->get();

        $exchangeSummary = [
            'total_exchanges' => $exchanges->count(),
            'total_original_value' => $exchanges->sum('original_amount'),
            'total_exchange_value' => $exchanges->sum('exchange_amount'),
            'net_difference' => $exchanges->sum('price_difference'),
            'average_original_value' => $exchanges->avg('original_amount'),
            'average_exchange_value' => $exchanges->avg('exchange_amount'),
            'upgrades_count' => $exchanges->where('price_difference', '>', 0)->count(),
            'downgrades_count' => $exchanges->where('price_difference', '<', 0)->count(),
            'even_exchanges' => $exchanges->where('price_difference', '=', 0)->count()
        ];

        return [
            'exchanges' => $exchanges,
            'summary' => $exchangeSummary
        ];
    }

    public function exportReturns(Request $request)
    {
        $dateFrom = $request->get('date_from', Carbon::now()->startOfMonth()->toDateString());
        $dateTo = $request->get('date_to', Carbon::now()->endOfDay()->toDateString());

        try {
            $returns = DB::table('returns')
                ->join('sale_items', 'returns.sale_item_id', '=', 'sale_items.id')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->leftJoin('sales as exchange_sales', 'returns.exchange_sale_id', '=', 'exchange_sales.id')
                ->select([
                    'returns.created_at as return_date',
                    'returns.return_type',
                    'returns.return_reason',
                    'returns.refund_amount',
                    'sales.invoice_number as original_invoice',
                    'sale_items.dress_name',
                    'sale_items.collection_name',
                    'sale_items.size',
                    'sale_items.barcode',
                    'sale_items.item_total as original_amount',
                    'exchange_sales.invoice_number as exchange_invoice',
                    'exchange_sales.total_amount as exchange_amount',
                    'returns.notes'
                ])
                ->whereBetween('returns.created_at', [$dateFrom, $dateTo])
                ->orderBy('returns.created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $returns,
                'summary' => $this->getReturnsSummary($dateFrom, $dateTo)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting returns data: ' . $e->getMessage()
            ], 500);
        }
    }
}
