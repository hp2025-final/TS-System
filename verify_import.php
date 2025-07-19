<?php
/**
 * Verify Import Script - Check if data was imported correctly
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Data Import Verification ===\n";

try {
    // Check collections
    $collections = DB::table('collections')->get();
    echo "\n--- Collections ---\n";
    echo "Count: " . count($collections) . "\n";
    if (count($collections) > 0) {
        echo "Sample collections:\n";
        foreach ($collections as $collection) {
            echo "- {$collection->name} (ID: {$collection->id})\n";
        }
    }
    
    // Check dresses
    $dresses = DB::table('dresses')->get();
    echo "\n--- Dresses ---\n";
    echo "Count: " . count($dresses) . "\n";
    if (count($dresses) > 0) {
        echo "Sample dresses:\n";
        foreach ($dresses->take(10) as $dress) {
            echo "- {$dress->name} ({$dress->sku}) - Size: {$dress->size} - Price: {$dress->sale_price}\n";
        }
    }
    
    // Check dress items
    $dressItems = DB::table('dress_items')->get();
    echo "\n--- Dress Items ---\n";
    echo "Count: " . count($dressItems) . "\n";
    if (count($dressItems) > 0) {
        echo "Sample dress items:\n";
        foreach ($dressItems->take(10) as $item) {
            echo "- Barcode: {$item->barcode} (Dress ID: {$item->dress_id}) - Status: {$item->status}\n";
        }
    }
    
    // Check data integrity
    echo "\n--- Data Integrity Check ---\n";
    $orphanedDresses = DB::table('dresses')
        ->leftJoin('collections', 'dresses.collection_id', '=', 'collections.id')
        ->whereNull('collections.id')
        ->count();
    echo "Orphaned dresses (no collection): $orphanedDresses\n";
    
    $orphanedItems = DB::table('dress_items')
        ->leftJoin('dresses', 'dress_items.dress_id', '=', 'dresses.id')
        ->whereNull('dresses.id')
        ->count();
    echo "Orphaned dress items (no dress): $orphanedItems\n";
    
    // Check barcode uniqueness
    $totalBarcodes = DB::table('dress_items')->count();
    $uniqueBarcodes = DB::table('dress_items')->distinct('barcode')->count();
    echo "Total barcodes: $totalBarcodes\n";
    echo "Unique barcodes: $uniqueBarcodes\n";
    echo "Duplicate barcodes: " . ($totalBarcodes - $uniqueBarcodes) . "\n";
    
    if ($orphanedDresses == 0 && $orphanedItems == 0 && $totalBarcodes == $uniqueBarcodes) {
        echo "\n✅ Data import verification PASSED!\n";
        echo "All data imported correctly with proper relationships.\n";
    } else {
        echo "\n❌ Data integrity issues found!\n";
    }
    
    // Summary
    echo "\n=== Final Summary ===\n";
    echo "Collections: " . count($collections) . "\n";
    echo "Dresses: " . count($dresses) . "\n";
    echo "Dress Items: " . count($dressItems) . "\n";
    echo "Total inventory value: PKR " . number_format(DB::table('dresses')->sum('sale_price')) . "\n";
    
} catch (Exception $e) {
    echo "\n❌ Error during verification: " . $e->getMessage() . "\n";
}
