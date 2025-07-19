<?php
/**
 * Data Import Script - Clear and Import New Data
 * This script will:
 * 1. Clear existing data from collections, dresses, and dress_items tables
 * 2. Import new data from the CSV files we created
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Data Import Script ===\n";
echo "This will clear ALL existing data and import new data from CSV files.\n";
echo "Make sure you have backed up your data before proceeding!\n\n";

// Ask for confirmation
echo "Do you want to proceed? Type 'yes' to continue: ";
$handle = fopen("php://stdin", "r");
$confirmation = trim(fgets($handle));
fclose($handle);

if (strtolower($confirmation) !== 'yes') {
    echo "Import cancelled.\n";
    exit(1);
}

try {
    DB::beginTransaction();
    
    echo "\n--- Step 1: Clearing existing data ---\n";
    
    // Clear in correct order due to foreign key constraints
    echo "Clearing dress_items table...\n";
    DB::table('dress_items')->delete();
    echo "✓ dress_items cleared\n";
    
    echo "Clearing dresses table...\n";
    DB::table('dresses')->delete();
    echo "✓ dresses cleared\n";
    
    echo "Clearing collections table...\n";
    DB::table('collections')->delete();
    echo "✓ collections cleared\n";
    
    // Reset auto-increment counters
    DB::statement('ALTER TABLE collections AUTO_INCREMENT = 1');
    DB::statement('ALTER TABLE dresses AUTO_INCREMENT = 1');
    DB::statement('ALTER TABLE dress_items AUTO_INCREMENT = 1');
    echo "✓ Auto-increment counters reset\n";
    
    echo "\n--- Step 2: Importing Collections ---\n";
    
    // Import collections
    $collectionsFile = __DIR__ . '/collections_data.csv';
    if (!file_exists($collectionsFile)) {
        throw new Exception("Collections CSV file not found: $collectionsFile");
    }
    
    $collectionsHandle = fopen($collectionsFile, 'r');
    $collectionsHeader = fgetcsv($collectionsHandle); // Skip header
    $collectionsCount = 0;
    
    while (($row = fgetcsv($collectionsHandle)) !== FALSE) {
        DB::table('collections')->insert([
            'id' => $row[0],
            'name' => $row[1],
            'description' => $row[2],
            'discount_percentage' => $row[3],
            'discount_active' => (bool)$row[4],
            'status' => $row[5],
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $collectionsCount++;
    }
    fclose($collectionsHandle);
    echo "✓ Imported $collectionsCount collections\n";
    
    echo "\n--- Step 3: Importing Dresses ---\n";
    
    // Import dresses
    $dressesFile = __DIR__ . '/dresses_data_fixed.csv';
    if (!file_exists($dressesFile)) {
        throw new Exception("Dresses CSV file not found: $dressesFile");
    }
    
    $dressesHandle = fopen($dressesFile, 'r');
    $dressesHeader = fgetcsv($dressesHandle); // Skip header
    $dressesCount = 0;
    
    while (($row = fgetcsv($dressesHandle)) !== FALSE) {
        DB::table('dresses')->insert([
            'id' => $row[0],
            'collection_id' => $row[1],
            'name' => $row[2],
            'sku' => $row[3],
            'description' => $row[4],
            'size' => $row[5],
            'hs_code' => null, // Not in our CSV, will be null
            'cost_price' => $row[6],
            'sale_price' => $row[7],
            'discount_percentage' => $row[8],
            'discount_active' => (bool)$row[9],
            'tax_percentage' => $row[10],
            'status' => $row[11],
            'created_at' => now(),
            'updated_at' => now()
        ]);
        $dressesCount++;
    }
    fclose($dressesHandle);
    echo "✓ Imported $dressesCount dresses\n";
    
    echo "\n--- Step 4: Importing Dress Items ---\n";
    
    // Import dress items
    $dressItemsFile = __DIR__ . '/dress_items_data.csv';
    if (!file_exists($dressItemsFile)) {
        throw new Exception("Dress items CSV file not found: $dressItemsFile");
    }
    
    $dressItemsHandle = fopen($dressItemsFile, 'r');
    $dressItemsHeader = fgetcsv($dressItemsHandle); // Skip header
    $dressItemsCount = 0;
    
    while (($row = fgetcsv($dressItemsHandle)) !== FALSE) {
        DB::table('dress_items')->insert([
            'id' => $row[0],
            'dress_id' => $row[1],
            'barcode' => $row[2],
            'size_discount_percentage' => 0.00, // Default value
            'size_discount_active' => false, // Default value
            'status' => $row[3],
            'sold_at' => null,
            'returned_at' => null,
            'created_at' => $row[4],
            'updated_at' => $row[5]
        ]);
        $dressItemsCount++;
    }
    fclose($dressItemsHandle);
    echo "✓ Imported $dressItemsCount dress items\n";
    
    DB::commit();
    
    echo "\n=== Import Summary ===\n";
    echo "✓ Collections: $collectionsCount imported\n";
    echo "✓ Dresses: $dressesCount imported\n";
    echo "✓ Dress Items: $dressItemsCount imported\n";
    echo "\n🎉 Data import completed successfully!\n";
    
    // Verify data
    echo "\n--- Verification ---\n";
    $actualCollections = DB::table('collections')->count();
    $actualDresses = DB::table('dresses')->count();
    $actualDressItems = DB::table('dress_items')->count();
    
    echo "Database counts:\n";
    echo "- Collections: $actualCollections\n";
    echo "- Dresses: $actualDresses\n";
    echo "- Dress Items: $actualDressItems\n";
    
    if ($actualCollections == $collectionsCount && 
        $actualDresses == $dressesCount && 
        $actualDressItems == $dressItemsCount) {
        echo "\n✅ All data imported correctly!\n";
    } else {
        echo "\n❌ Warning: Data counts don't match expected values\n";
    }
    
} catch (Exception $e) {
    DB::rollBack();
    echo "\n❌ Error occurred during import: " . $e->getMessage() . "\n";
    echo "All changes have been rolled back.\n";
    exit(1);
}
