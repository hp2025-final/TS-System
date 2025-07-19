<?php
/**
 * Comprehensive Data Verification
 * Check all aspects of the imported data
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Comprehensive Data Verification ===\n";

try {
    // 1. Database Counts
    echo "\n--- Database Counts ---\n";
    $collections = DB::table('collections')->count();
    $dresses = DB::table('dresses')->count();
    $dressItems = DB::table('dress_items')->count();
    
    echo "Collections: $collections\n";
    echo "Dresses: $dresses\n";
    echo "Dress Items: $dressItems\n";
    
    // 2. Check Collections Data
    echo "\n--- Collections Detail ---\n";
    $collectionsData = DB::table('collections')->get();
    foreach ($collectionsData as $collection) {
        $dressCount = DB::table('dresses')->where('collection_id', $collection->id)->count();
        echo "- {$collection->name} (ID: {$collection->id}): {$dressCount} dresses\n";
    }
    
    // 3. Check Sample Dresses
    echo "\n--- Sample Dresses ---\n";
    $sampleDresses = DB::table('dresses')
        ->join('collections', 'dresses.collection_id', '=', 'collections.id')
        ->select('dresses.*', 'collections.name as collection_name')
        ->limit(10)
        ->get();
    
    foreach ($sampleDresses as $dress) {
        $itemCount = DB::table('dress_items')->where('dress_id', $dress->id)->count();
        echo "- {$dress->name} ({$dress->sku}) - {$dress->collection_name} - Size: {$dress->size} - Items: {$itemCount}\n";
    }
    
    // 4. Check Sample Dress Items
    echo "\n--- Sample Dress Items ---\n";
    $sampleItems = DB::table('dress_items')
        ->join('dresses', 'dress_items.dress_id', '=', 'dresses.id')
        ->select('dress_items.*', 'dresses.name as dress_name', 'dresses.sku')
        ->limit(10)
        ->get();
    
    foreach ($sampleItems as $item) {
        echo "- Barcode: {$item->barcode} - {$item->dress_name} ({$item->sku}) - Status: {$item->status}\n";
    }
    
    // 5. Check for Data Integrity Issues
    echo "\n--- Data Integrity Check ---\n";
    
    // Check for orphaned dresses
    $orphanedDresses = DB::table('dresses')
        ->leftJoin('collections', 'dresses.collection_id', '=', 'collections.id')
        ->whereNull('collections.id')
        ->count();
    echo "Orphaned dresses: $orphanedDresses\n";
    
    // Check for orphaned dress items
    $orphanedItems = DB::table('dress_items')
        ->leftJoin('dresses', 'dress_items.dress_id', '=', 'dresses.id')
        ->whereNull('dresses.id')
        ->count();
    echo "Orphaned dress items: $orphanedItems\n";
    
    // Check for duplicate barcodes
    $duplicateBarcodes = DB::table('dress_items')
        ->select('barcode', DB::raw('COUNT(*) as count'))
        ->groupBy('barcode')
        ->having('count', '>', 1)
        ->count();
    echo "Duplicate barcodes: $duplicateBarcodes\n";
    
    // Check for duplicate SKUs
    $duplicateSKUs = DB::table('dresses')
        ->select('sku', DB::raw('COUNT(*) as count'))
        ->groupBy('sku')
        ->having('count', '>', 1)
        ->count();
    echo "Duplicate SKUs: $duplicateSKUs\n";
    
    // 6. Test API Endpoints
    echo "\n--- API Endpoint Tests ---\n";
    
    // Test Collections API
    $collectionsUrl = 'http://localhost:8000/api/collections';
    $collectionsResponse = @file_get_contents($collectionsUrl);
    if ($collectionsResponse) {
        $collectionsAPI = json_decode($collectionsResponse, true);
        echo "✓ Collections API: " . count($collectionsAPI) . " collections\n";
    } else {
        echo "❌ Collections API failed\n";
    }
    
    // Test Dresses API
    $dressesUrl = 'http://localhost:8000/api/dresses?limit=5';
    $dressesResponse = @file_get_contents($dressesUrl);
    if ($dressesResponse) {
        $dressesAPI = json_decode($dressesResponse, true);
        if (isset($dressesAPI['data'])) {
            echo "✓ Dresses API: " . count($dressesAPI['data']) . " dresses returned\n";
            if (count($dressesAPI['data']) > 0) {
                echo "  Sample: " . $dressesAPI['data'][0]['name'] . " - " . $dressesAPI['data'][0]['collection']['name'] . "\n";
            }
        }
    } else {
        echo "❌ Dresses API failed\n";
    }
    
    // Test Dress Items API
    $itemsUrl = 'http://localhost:8000/api/dress-items?limit=5';
    $itemsResponse = @file_get_contents($itemsUrl);
    if ($itemsResponse) {
        $itemsAPI = json_decode($itemsResponse, true);
        if (is_array($itemsAPI)) {
            echo "✓ Dress Items API: " . count($itemsAPI) . " items returned\n";
            if (count($itemsAPI) > 0) {
                echo "  Sample: Barcode " . $itemsAPI[0]['barcode'] . " - " . $itemsAPI[0]['dress']['name'] . "\n";
            }
        }
    } else {
        echo "❌ Dress Items API failed\n";
    }
    
    // Test specific barcode search
    $barcodeUrl = 'http://localhost:8000/api/dress-items?barcode=5071502001';
    $barcodeResponse = @file_get_contents($barcodeUrl);
    if ($barcodeResponse) {
        $barcodeAPI = json_decode($barcodeResponse, true);
        if (is_array($barcodeAPI) && count($barcodeAPI) > 0) {
            echo "✓ Barcode Search API: Found barcode 5071502001\n";
            echo "  Item: " . $barcodeAPI[0]['dress']['name'] . " - " . $barcodeAPI[0]['dress']['collection']['name'] . "\n";
        }
    } else {
        echo "❌ Barcode Search API failed\n";
    }
    
    // 7. Summary
    echo "\n=== Summary ===\n";
    if ($orphanedDresses == 0 && $orphanedItems == 0 && $duplicateBarcodes == 0 && $duplicateSKUs == 0) {
        echo "✅ Data integrity is PERFECT!\n";
    } else {
        echo "⚠️  Some data integrity issues found\n";
    }
    
    echo "Total inventory value: PKR " . number_format(DB::table('dresses')->sum('sale_price')) . "\n";
    echo "Available items: " . DB::table('dress_items')->where('status', 'available')->count() . "\n";
    echo "Collections with discounts: " . DB::table('collections')->where('discount_active', true)->count() . "\n";
    
    echo "\n🎉 Verification completed!\n";
    
} catch (Exception $e) {
    echo "\n❌ Error during verification: " . $e->getMessage() . "\n";
}
