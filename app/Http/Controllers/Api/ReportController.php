<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\DressItem;
use App\Models\Collection;
use App\Models\Dress;
use App\Models\ReturnItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Daily dashboard report
     */
    public function dailyReport(Request $request)
    {
        $today = now()->startOfDay();
        
        // Today's sales data
        $todaySales = Sale::whereDate('sale_date', $today)->get();
        $todayItems = SaleItem::whereHas('sale', function($query) use ($today) {
            $query->whereDate('sale_date', $today);
        })->count();
        
        $todayTransactions = $todaySales->count();
        
        // Available items count
        $availableItems = DressItem::whereIn('status', ['available', 'returned_resaleable'])->count();
        
        // Low stock count (dresses with 5 or fewer available items)
        $lowStockCount = Dress::with('dressItems')
            ->get()
            ->filter(function ($dress) {
                return $dress->dressItems->whereIn('status', ['available', 'returned_resaleable'])->count() <= 5 
                    && $dress->dressItems->whereIn('status', ['available', 'returned_resaleable'])->count() > 0;
            })
            ->count();
        
        // Recent sales (last 10)
        $recentSales = Sale::with(['saleItems'])
            ->whereDate('sale_date', $today)
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'invoice_number' => $sale->invoice_number,
                    'customer_name' => $sale->customer_name,
                    'total_amount' => number_format($sale->total_amount, 2, '.', ''),
                    'payment_method' => $sale->payment_method,
                    'items_count' => $sale->saleItems->count(),
                    'created_at' => $sale->created_at->toISOString(),
                ];
            });
        
        return response()->json([
            'today_sales' => $todaySales->sum('total_amount'),
            'today_items' => $todayItems,
            'today_transactions' => $todayTransactions,
            'available_items' => $availableItems,
            'low_stock_count' => $lowStockCount,
            'recent_sales' => $recentSales,
        ]);
    }

    /**
     * Sales summary report
     */
    public function salesSummary(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        $sales = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])->get();

        $summary = [
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'total_sales' => $sales->count(),
            'total_revenue' => $sales->sum('total_amount'),
            'total_profit' => $sales->sum('profit_amount'),
            'average_sale_value' => $sales->count() > 0 ? $sales->avg('total_amount') : 0,
            'payment_methods' => $sales->groupBy('payment_method')->map(function ($group) {
                return [
                    'count' => $group->count(),
                    'amount' => $group->sum('total_amount'),
                    'percentage' => $group->count() / max($group->count(), 1) * 100
                ];
            }),
            'daily_breakdown' => $sales->groupBy(function ($sale) {
                return $sale->sale_date->format('Y-m-d');
            })->map(function ($group) {
                return [
                    'sales_count' => $group->count(),
                    'revenue' => $group->sum('total_amount'),
                    'profit' => $group->sum('profit_amount')
                ];
            })->sortKeys()
        ];

        return response()->json($summary);
    }

    /**
     * Inventory report
     */
    public function inventoryReport(Request $request)
    {
        $collections = Collection::with(['dresses.dressItems'])->get();
        
        $byCollection = [];
        
        foreach ($collections as $collection) {
            $totalItems = 0;
            $availableItems = 0;
            $soldItems = 0;

            foreach ($collection->dresses as $dress) {
                $dressItems = $dress->dressItems;
                $available = $dressItems->whereIn('status', ['available', 'returned_resaleable']);
                $sold = $dressItems->where('status', 'sold');

                $totalItems += $dressItems->count();
                $availableItems += $available->count();
                $soldItems += $sold->count();
            }

            $byCollection[] = [
                'id' => $collection->id,
                'name' => $collection->name,
                'total_items' => $totalItems,
                'available_items' => $availableItems,
                'sold_items' => $soldItems,
                'discount_percentage' => $collection->discount_percentage,
                'discount_active' => $collection->discount_active,
            ];
        }

        return response()->json([
            'by_collection' => $byCollection,
            'summary' => [
                'total_items' => collect($byCollection)->sum('total_items'),
                'available_items' => collect($byCollection)->sum('available_items'),
                'sold_items' => collect($byCollection)->sum('sold_items'),
            ]
        ]);
    }

    /**
     * Low stock alert
     */
    public function lowStockAlert(Request $request)
    {
        $threshold = $request->get('threshold', 3); // Default: alert when less than 3 items

        $lowStockDresses = Dress::with(['collection', 'dressItems'])
            ->get()
            ->filter(function ($dress) use ($threshold) {
                $availableItems = $dress->dressItems->where('status', 'available')->count();
                return $availableItems <= $threshold && $availableItems > 0;
            })
            ->map(function ($dress) {
                return [
                    'dress_id' => $dress->id,
                    'dress_name' => $dress->name,
                    'collection_name' => $dress->collection->name,
                    'available_count' => $dress->dressItems->where('status', 'available')->count(),
                    'total_count' => $dress->dressItems->count(),
                    'sizes_available' => $dress->dressItems->where('status', 'available')->pluck('size')->unique()->values(),
                    'reorder_suggestion' => max(10 - $dress->dressItems->where('status', 'available')->count(), 0)
                ];
            })
            ->values();

        $outOfStockDresses = Dress::with(['collection', 'dressItems'])
            ->get()
            ->filter(function ($dress) {
                return $dress->dressItems->where('status', 'available')->count() === 0;
            })
            ->map(function ($dress) {
                return [
                    'dress_id' => $dress->id,
                    'dress_name' => $dress->name,
                    'collection_name' => $dress->collection->name,
                    'last_sold' => $dress->dressItems->where('status', 'sold')->max('sold_at'),
                    'total_sold' => $dress->dressItems->where('status', 'sold')->count()
                ];
            })
            ->values();

        return response()->json([
            'low_stock' => $lowStockDresses,
            'out_of_stock' => $outOfStockDresses,
            'summary' => [
                'low_stock_count' => $lowStockDresses->count(),
                'out_of_stock_count' => $outOfStockDresses->count(),
                'total_alerts' => $lowStockDresses->count() + $outOfStockDresses->count()
            ]
        ]);
    }

    /**
     * Profit analysis report
     */
    public function profitAnalysis(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        $saleItems = SaleItem::with(['dressItem.dress.collection', 'sale'])
            ->whereHas('sale', function ($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('sale_date', [$dateFrom, $dateTo]);
            })
            ->get();

        // Profit by collection
        $profitByCollection = $saleItems->groupBy('dressItem.dress.collection.name')
            ->map(function ($items) {
                return [
                    'total_sales' => $items->sum('line_total'),
                    'total_profit' => $items->sum('profit_amount'),
                    'profit_margin' => $items->sum('line_total') > 0 ? 
                        ($items->sum('profit_amount') / $items->sum('line_total')) * 100 : 0,
                    'items_sold' => $items->sum('quantity')
                ];
            });

        // Profit by dress
        $profitByDress = $saleItems->groupBy('dressItem.dress.name')
            ->map(function ($items) {
                $dress = $items->first()->dressItem->dress;
                return [
                    'dress_name' => $dress->name,
                    'collection_name' => $dress->collection->name,
                    'total_sales' => $items->sum('line_total'),
                    'total_profit' => $items->sum('profit_amount'),
                    'profit_margin' => $items->sum('line_total') > 0 ? 
                        ($items->sum('profit_amount') / $items->sum('line_total')) * 100 : 0,
                    'items_sold' => $items->sum('quantity'),
                    'avg_selling_price' => $items->avg('unit_price')
                ];
            })
            ->sortByDesc('total_profit')
            ->take(20);

        return response()->json([
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'overall_profit' => [
                'total_revenue' => $saleItems->sum('line_total'),
                'total_profit' => $saleItems->sum('profit_amount'),
                'overall_margin' => $saleItems->sum('line_total') > 0 ? 
                    ($saleItems->sum('profit_amount') / $saleItems->sum('line_total')) * 100 : 0
            ],
            'profit_by_collection' => $profitByCollection,
            'top_profitable_dresses' => $profitByDress->values()
        ]);
    }

    /**
     * Customer insights (based on available data)
     */
    public function customerInsights(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        $sales = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])
            ->whereNotNull('customer_name')
            ->get();

        $customerData = $sales->groupBy('customer_phone')
            ->map(function ($customerSales) {
                $customer = $customerSales->first();
                return [
                    'customer_name' => $customer->customer_name,
                    'customer_phone' => $customer->customer_phone,
                    'total_purchases' => $customerSales->count(),
                    'total_spent' => $customerSales->sum('total_amount'),
                    'average_purchase' => $customerSales->avg('total_amount'),
                    'last_purchase' => $customerSales->max('sale_date'),
                    'first_purchase' => $customerSales->min('sale_date')
                ];
            })
            ->sortByDesc('total_spent')
            ->take(50);

        return response()->json([
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'summary' => [
                'total_customers' => $customerData->count(),
                'returning_customers' => $customerData->where('total_purchases', '>', 1)->count(),
                'average_customer_value' => $customerData->avg('total_spent')
            ],
            'top_customers' => $customerData->values()
        ]);
    }

    /**
     * Returns and exchange report
     */
    public function returnsReport(Request $request)
    {
        $dateFrom = $request->get('date_from', now()->subDays(30)->toDateString());
        $dateTo = $request->get('date_to', now()->toDateString());

        $returns = ReturnItem::with(['saleItem.dressItem.dress.collection'])
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->get();

        $summary = [
            'total_returns' => $returns->count(),
            'approved_returns' => $returns->where('status', 'approved')->count(),
            'rejected_returns' => $returns->where('status', 'rejected')->count(),
            'pending_returns' => $returns->where('status', 'pending')->count(),
            'total_refund_amount' => $returns->where('status', 'approved')->sum('refund_amount'),
            'return_rate' => 0, // Will calculate based on sales
            'reasons' => $returns->groupBy('reason')->map->count(),
            'types' => $returns->groupBy('return_type')->map->count()
        ];

        // Calculate return rate
        $totalSales = Sale::whereBetween('sale_date', [$dateFrom, $dateTo])->count();
        $summary['return_rate'] = $totalSales > 0 ? ($returns->count() / $totalSales) * 100 : 0;

        return response()->json([
            'period' => ['from' => $dateFrom, 'to' => $dateTo],
            'summary' => $summary,
            'returns' => $returns->map(function ($return) {
                return [
                    'return_number' => $return->return_number,
                    'dress_name' => $return->saleItem->dressItem->dress->name,
                    'collection_name' => $return->saleItem->dressItem->dress->collection->name,
                    'reason' => $return->reason,
                    'type' => $return->return_type,
                    'status' => $return->status,
                    'refund_amount' => $return->refund_amount,
                    'requested_at' => $return->requested_at
                ];
            })
        ]);
    }
}
