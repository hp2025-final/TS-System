<?php
/**
 * Database Backup Script
 * Creates CSV backups of current collections, dresses, and dress_items data
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Database Backup Script ===\n";
echo "Creating backups of current data...\n\n";

$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
    echo "Created backup directory: $backupDir\n";
}

$timestamp = date('Y-m-d_H-i-s');

try {
    // Backup Collections
    echo "Backing up collections...\n";
    $collections = DB::table('collections')->get();
    $collectionsFile = "$backupDir/collections_backup_$timestamp.csv";
    $collectionsHandle = fopen($collectionsFile, 'w');
    
    // Write header
    fputcsv($collectionsHandle, ['id', 'name', 'description', 'discount_percentage', 'discount_active', 'status', 'created_at', 'updated_at']);
    
    // Write data
    foreach ($collections as $collection) {
        fputcsv($collectionsHandle, [
            $collection->id,
            $collection->name,
            $collection->description,
            $collection->discount_percentage,
            $collection->discount_active ? 1 : 0,
            $collection->status,
            $collection->created_at,
            $collection->updated_at
        ]);
    }
    fclose($collectionsHandle);
    echo "✓ Collections backed up to: $collectionsFile\n";
    
    // Backup Dresses
    echo "Backing up dresses...\n";
    $dresses = DB::table('dresses')->get();
    $dressesFile = "$backupDir/dresses_backup_$timestamp.csv";
    $dressesHandle = fopen($dressesFile, 'w');
    
    // Write header
    fputcsv($dressesHandle, ['id', 'collection_id', 'name', 'sku', 'description', 'size', 'hs_code', 'cost_price', 'sale_price', 'discount_percentage', 'discount_active', 'tax_percentage', 'status', 'created_at', 'updated_at']);
    
    // Write data
    foreach ($dresses as $dress) {
        fputcsv($dressesHandle, [
            $dress->id,
            $dress->collection_id,
            $dress->name,
            $dress->sku,
            $dress->description,
            $dress->size,
            $dress->hs_code,
            $dress->cost_price,
            $dress->sale_price,
            $dress->discount_percentage,
            $dress->discount_active ? 1 : 0,
            $dress->tax_percentage,
            $dress->status,
            $dress->created_at,
            $dress->updated_at
        ]);
    }
    fclose($dressesHandle);
    echo "✓ Dresses backed up to: $dressesFile\n";
    
    // Backup Dress Items
    echo "Backing up dress items...\n";
    $dressItems = DB::table('dress_items')->get();
    $dressItemsFile = "$backupDir/dress_items_backup_$timestamp.csv";
    $dressItemsHandle = fopen($dressItemsFile, 'w');
    
    // Write header
    fputcsv($dressItemsHandle, ['id', 'dress_id', 'barcode', 'size_discount_percentage', 'size_discount_active', 'status', 'sold_at', 'returned_at', 'created_at', 'updated_at']);
    
    // Write data
    foreach ($dressItems as $item) {
        fputcsv($dressItemsHandle, [
            $item->id,
            $item->dress_id,
            $item->barcode,
            $item->size_discount_percentage,
            $item->size_discount_active ? 1 : 0,
            $item->status,
            $item->sold_at,
            $item->returned_at,
            $item->created_at,
            $item->updated_at
        ]);
    }
    fclose($dressItemsHandle);
    echo "✓ Dress items backed up to: $dressItemsFile\n";
    
    echo "\n=== Backup Summary ===\n";
    echo "✓ Collections: " . count($collections) . " records backed up\n";
    echo "✓ Dresses: " . count($dresses) . " records backed up\n";
    echo "✓ Dress Items: " . count($dressItems) . " records backed up\n";
    echo "\n🎉 Backup completed successfully!\n";
    echo "Backup files saved in: $backupDir\n";
    
} catch (Exception $e) {
    echo "\n❌ Error occurred during backup: " . $e->getMessage() . "\n";
    exit(1);
}
