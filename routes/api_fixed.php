<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\DressController;
use App\Http\Controllers\Api\DressItemController;
use App\Http\Controllers\Api\CollectionController;

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
            'items.*.size' => 'required|string',
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
                
                if (!$dressItem || $dressItem->status !== 'available') {
                    return response()->json(['error' => "Item with barcode {$item['barcode']} is not available"], 400);
                }

                $itemSubtotal = $dressItem->dress->sale_price;
                $itemDiscount = $item['total_discount'] ?? 0;
                $itemFinalPrice = $item['final_price'];
                $itemTax = $itemFinalPrice * ($dressItem->dress->tax_percentage / 100);

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
                    'size' => $dressItem->size,
                    'cost_price' => $dressItem->dress->cost_price,
                    'sale_price' => $dressItem->dress->sale_price,
                    'total_discount_amount' => $item['total_discount'] ?? 0,
                    'tax_percentage' => $dressItem->dress->tax_percentage,
                    'tax_amount' => $item['final_price'] * ($dressItem->dress->tax_percentage / 100),
                    'item_total' => $item['final_price'] + ($item['final_price'] * ($dressItem->dress->tax_percentage / 100)),
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
        $availableItems = \App\Models\DressItem::where('status', 'available')->count();
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

    // Collections
    Route::apiResource('collections', CollectionController::class);
    Route::put('/collections/{collection}/discount', [CollectionController::class, 'updateDiscount']);

    // Dresses
    Route::apiResource('dresses', DressController::class);
    Route::put('/dresses/{dress}/discount', [DressController::class, 'updateDiscount']);

    // Dress Items (Individual pieces)
    Route::apiResource('dress-items', DressItemController::class);
    Route::get('/dress-items/barcode/{barcode}', [DressItemController::class, 'getByBarcode']);
    Route::put('/dress-items/{dressItem}/discount', [DressItemController::class, 'updateDiscount']);
    Route::post('/validate-barcode', [DressItemController::class, 'validateBarcode']);

    // Sales
    Route::apiResource('sales', SaleController::class);
    Route::get('/sales/{sale}/invoice', [SaleController::class, 'generateInvoice']);

    // Returns & Exchanges (will add later)
    // Route::apiResource('returns', ReturnController::class);

    // Reports (will add later)
    // Route::get('/reports/daily-sales', [ReportController::class, 'dailySales']);
    // Route::get('/reports/profit', [ReportController::class, 'profit']);
    // Route::get('/reports/inventory', [ReportController::class, 'inventory']);
    // Route::get('/reports/low-stock', [ReportController::class, 'lowStock']);

    // Discount Management
    Route::get('/discounts/active', [DressItemController::class, 'getActiveDiscounts']);
});
