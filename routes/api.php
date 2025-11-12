<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\DressController;
use App\Http\Controllers\Api\DressItemController;
use App\Http\Controllers\Api\CollectionController;
use App\Http\Controllers\Api\ReturnController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public routes (no authentication required)
Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);

// Company config route  
Route::get('/company-config', function () {
    return response()->json(config('company'));
});

// Barcode search route (for POS scanner)
Route::get('/test-barcode/{barcode}', [DressItemController::class, 'getByBarcode']);

// Test Sales Endpoint (Public for testing - remove in production)
Route::prefix('sales')->group(function () {
    Route::post('/test', function (Request $request) {
        // Validate input data
        $request->validate([
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'payment_method' => 'required|string|in:cash,card,upi',
            'items' => 'required|array|min:1',
            'items.*.dress_item_id' => 'required|integer|exists:dress_items,id',
            'items.*.barcode' => 'required|string',
            'items.*.final_price' => 'required|numeric|min:0',
            'items.*.total_discount' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'total_discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0'
        ]);

        // Process the sale
        try {
            $subtotal = 0;
            $totalDiscountAmount = 0;
            $taxAmount = 0;
            $collectionDiscountTotal = 0;
            $dressDiscountTotal = 0;
            $sizeDiscountTotal = 0;

            // Calculate totals
            foreach ($request->items as $item) {
                $dressItem = \App\Models\DressItem::with(['dress.collection'])->find($item['dress_item_id']);
                
                if (!$dressItem || !in_array($dressItem->status, ['available', 'returned_resaleable'])) {
                    return response()->json(['error' => "Item with barcode {$item['barcode']} is not available"], 400);
                }

                $itemSubtotal = $dressItem->dress->sale_price;
                $itemDiscount = $item['total_discount'] ?? 0;
                $itemFinalPrice = $item['final_price'];
                // GST should be calculated on original price (before discount)
                $itemTax = $dressItem->dress->sale_price * ($dressItem->dress->tax_percentage / 100);

                $subtotal += $itemFinalPrice;
                $totalDiscountAmount += $itemDiscount;
                $taxAmount += $itemTax;

                // Update dress item status
                $dressItem->update(['status' => 'sold', 'sold_at' => now()]);
            }

            $totalAmount = $subtotal + $taxAmount;

            // Create a test sale without user authentication
            $sale = \App\Models\Sale::create([
                'user_id' => 1, // Default to first user for testing
                'invoice_number' => 'TS-' . now()->format('ymdHis'),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'subtotal' => $request->subtotal,
                'collection_discount_amount' => $collectionDiscountTotal,
                'dress_discount_amount' => $dressDiscountTotal,
                'size_discount_amount' => $sizeDiscountTotal,
                'total_discount_amount' => $totalDiscountAmount,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'sale_date' => now(),
            ]);

            // Create sale items
            foreach ($request->items as $item) {
                $dressItem = \App\Models\DressItem::with(['dress.collection'])->find($item['dress_item_id']);
                
                \App\Models\SaleItem::create([
                    'sale_id' => $sale->id,
                    'dress_item_id' => $dressItem->id,
                    'dress_name' => $dressItem->dress->name,
                    'collection_name' => $dressItem->dress->collection->name,
                    'sku' => $dressItem->dress->sku,
                    'barcode' => $dressItem->barcode,
                    'size' => $dressItem->dress->size,
                    'cost_price' => $dressItem->dress->cost_price,
                    'sale_price' => $dressItem->dress->sale_price,
                    'total_discount_amount' => $item['total_discount'] ?? 0,
                    'tax_percentage' => $dressItem->dress->tax_percentage,
                    // GST calculated on original price (before discount)
                    'tax_amount' => $dressItem->dress->sale_price * ($dressItem->dress->tax_percentage / 100),
                    'item_total' => $item['final_price'] + ($dressItem->dress->sale_price * ($dressItem->dress->tax_percentage / 100)),
                    'profit_amount' => $item['final_price'] - $dressItem->dress->cost_price,
                ]);
            }

            return response()->json([
                'success' => true,
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'total_amount' => $totalAmount,
                'message' => 'Sale completed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    });
});

// Public Collections route (temporary for testing)
Route::apiResource('collections', CollectionController::class);
Route::put('/collections/{collection}/discount', [CollectionController::class, 'updateDiscount']);
Route::get('/collections-export', [CollectionController::class, 'export']);

// Public Dresses route (temporary for testing)
Route::apiResource('dresses', DressController::class);
Route::put('/dresses/{dress}/discount', [DressController::class, 'updateDiscount']);
Route::get('/dresses-export', [DressController::class, 'export']);

// Protected routes (require authentication)
Route::middleware(['auth:sanctum'])->group(function () {
    // Auth routes
    Route::post('/logout', [App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/me', [App\Http\Controllers\Api\AuthController::class, 'me']);

    // Dashboard stats
    Route::get('/dashboard/stats', function () {
        $totalSales = \App\Models\Sale::sum('total_amount');
        $todaySales = \App\Models\Sale::whereDate('created_at', today())->sum('total_amount');
        $totalItems = \App\Models\DressItem::count();
        $availableItems = \App\Models\DressItem::whereIn('status', ['available', 'returned_resaleable'])->count();
        $soldItems = \App\Models\DressItem::where('status', 'sold')->count();
        $lowStockItems = \App\Models\Dress::whereHas('dressItems', function($query) {
            $query->where('status', 'available');
        }, '<=', 5)->count();

        return response()->json([
            'total_sales' => $totalSales,
            'today_sales' => $todaySales,
            'total_items' => $totalItems,
            'available_items' => $availableItems,
            'sold_items' => $soldItems,
            'low_stock_items' => $lowStockItems,
            'recent_sales' => \App\Models\Sale::with(['saleItems.dressItem.dress'])
                ->latest()
                ->take(5)
                ->get(),
        ]);
    });

    // Dress Items (Individual pieces) - specific routes first
    Route::get('/dress-items/available', [DressItemController::class, 'getAvailableForExchange']);
    Route::get('/dress-items/resaleable', [DressItemController::class, 'getResaleableItems']);
    Route::put('/dress-items/{dressItem}/make-available', [DressItemController::class, 'makeAvailable']);
    Route::put('/dress-items/{dressItem}/mark-damaged', [DressItemController::class, 'markDamaged']);
    Route::get('/dress-items/barcode/{barcode}', [DressItemController::class, 'getByBarcode']);
    Route::put('/dress-items/{dressItem}/discount', [DressItemController::class, 'updateDiscount']);
    Route::post('/validate-barcode', [DressItemController::class, 'validateBarcode']);
    Route::apiResource('dress-items', DressItemController::class);

    // Sales - specific routes first
    Route::get('/sales/search-items', [SaleController::class, 'searchSoldItems']);
    Route::get('/sales/{sale}/invoice', [SaleController::class, 'generateInvoice']);
    Route::apiResource('sales', SaleController::class);

    // Modern Returns & Exchanges
    Route::get('/returns/search/{query}', [App\Http\Controllers\Api\ModernReturnController::class, 'search']);
    Route::get('/returns/recent', [App\Http\Controllers\Api\ModernReturnController::class, 'recent']);
    Route::get('/returns/stats', [App\Http\Controllers\Api\ModernReturnController::class, 'stats']);
    Route::post('/returns', [App\Http\Controllers\Api\ModernReturnController::class, 'store']);

    // Reports
    Route::prefix('reports')->group(function () {
        Route::get('/daily', [App\Http\Controllers\Api\ReportController::class, 'dailyReport']);
        Route::get('/inventory', [App\Http\Controllers\Api\ReportController::class, 'inventoryReport']);
        Route::get('/sales', [App\Http\Controllers\Api\SalesReportController::class, 'index']);
        Route::get('/sales/export', [App\Http\Controllers\Api\SalesReportController::class, 'exportSales']);
        Route::get('/returns', [App\Http\Controllers\Api\ReturnsReportController::class, 'index']);
        Route::get('/returns/export', [App\Http\Controllers\Api\ReturnsReportController::class, 'exportReturns']);
        Route::get('/barcode-sales', [App\Http\Controllers\Api\BarcodeSalesReportController::class, 'index']);
        Route::get('/barcode-sales/export', [App\Http\Controllers\Api\BarcodeSalesReportController::class, 'export']);
        Route::get('/barcode-returns', [App\Http\Controllers\Api\BarcodeReturnsReportController::class, 'index']);
        Route::get('/barcode-returns/export', [App\Http\Controllers\Api\BarcodeReturnsReportController::class, 'export']);
    });

    // Discount Management
    Route::get('/discounts/active', [DressItemController::class, 'getActiveDiscounts']);

    // Bulk Upload (Admin only)
    Route::prefix('bulk-upload')->group(function () {
        Route::post('/upload', [App\Http\Controllers\Api\BulkUploadController::class, 'upload']);
        Route::get('/stats', [App\Http\Controllers\Api\BulkUploadController::class, 'stats']);
        Route::get('/template', [App\Http\Controllers\Api\BulkUploadController::class, 'downloadTemplate']);
    });

    // Bulk Retrieve (Admin only)
    Route::prefix('bulk-retrieve')->group(function () {
        Route::post('/retrieve', [App\Http\Controllers\Api\BulkUploadController::class, 'bulkRetrieve']);
        Route::get('/template', [App\Http\Controllers\Api\BulkUploadController::class, 'downloadRetrieveTemplate']);
    });

    // Database Backup (Admin only)
    Route::get('/backup/download', [App\Http\Controllers\Api\BackupController::class, 'download']);
});

// Barcode List (Public - outside auth middleware)
Route::prefix('barcode-list')->group(function () {
    Route::get('/', [App\Http\Controllers\Api\BarcodeListController::class, 'index']);
    Route::get('/export', [App\Http\Controllers\Api\BarcodeListController::class, 'export']);
    Route::get('/filter-options', [App\Http\Controllers\Api\BarcodeListController::class, 'filterOptions']);
});
