<?php

require_once 'vendor/autoload.php';

// Database connection
$dsn = 'mysql:host=localhost;dbname=tspos;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== REPLACING DATA WITH UPDATED CSV (SECOND UPDATE) ===\n\n";
    
    // Step 1: Create backup of current data
    echo "Step 1: Creating backup of current data...\n";
    $timestamp = date('Y-m-d_H-i-s');
    
    // Backup current data
    $collections = $pdo->query("SELECT * FROM collections ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    $dresses = $pdo->query("SELECT * FROM dresses ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    $items = $pdo->query("SELECT * FROM dress_items ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
    
    echo "✅ Current data: " . count($collections) . " collections, " . count($dresses) . " dresses, " . count($items) . " items\n\n";
    
    // Step 2: Clear existing data
    echo "Step 2: Clearing existing data...\n";
    
    try {
        // Disable foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
        
        // Clear tables
        $pdo->exec("DELETE FROM dress_items");
        $pdo->exec("DELETE FROM dresses");
        $pdo->exec("DELETE FROM collections");
        
        // Reset auto increment
        $pdo->exec("ALTER TABLE collections AUTO_INCREMENT = 1");
        $pdo->exec("ALTER TABLE dresses AUTO_INCREMENT = 1");
        $pdo->exec("ALTER TABLE dress_items AUTO_INCREMENT = 1");
        
        // Re-enable foreign key checks
        $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
        
        echo "✅ All existing data cleared\n\n";
        
    } catch (Exception $e) {
        throw $e;
    }
    
    // Step 3: Import updated collections
    echo "Step 3: Importing updated collections...\n";
    $pdo->beginTransaction();
    
    try {
        $collectionsFile = fopen('collections_data_updated.csv', 'r');
        fgetcsv($collectionsFile); // Skip header
        
        $collectionsCount = 0;
        $collectionMap = [];
        
        while (($data = fgetcsv($collectionsFile)) !== FALSE) {
            $stmt = $pdo->prepare("
                INSERT INTO collections (name, description, discount_percentage, discount_active, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $data[0], // name
                $data[1], // description
                $data[2], // discount_percentage
                $data[3] === 'true', // discount_active
                $data[4] // status
            ]);
            
            $collectionId = $pdo->lastInsertId();
            $collectionMap[$data[0]] = $collectionId;
            $collectionsCount++;
        }
        fclose($collectionsFile);
        
        $pdo->commit();
        echo "✅ Imported {$collectionsCount} collections\n\n";
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
    
    // Step 4: Import updated dresses
    echo "Step 4: Importing updated dresses...\n";
    $pdo->beginTransaction();
    
    try {
        $dressesFile = fopen('dresses_data_updated.csv', 'r');
        fgetcsv($dressesFile); // Skip header
        
        $dressesCount = 0;
        $dressMap = [];
        
        while (($data = fgetcsv($dressesFile)) !== FALSE) {
            $collectionId = $collectionMap[$data[0]];
            
            $stmt = $pdo->prepare("
                INSERT INTO dresses (collection_id, name, sku, description, size, cost_price, sale_price, 
                                   discount_percentage, discount_active, tax_percentage, status, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
            ");
            $stmt->execute([
                $collectionId, // collection_id
                $data[1], // name (now includes size like "Nooraya S")
                $data[2], // sku
                $data[3], // description
                $data[4], // size
                $data[5], // cost_price
                $data[6], // sale_price
                $data[7], // discount_percentage
                $data[8] === 'true', // discount_active
                $data[9], // tax_percentage
                $data[10] // status
            ]);
            
            $dressId = $pdo->lastInsertId();
            $dressMap[$data[2]] = $dressId; // Map SKU to dress ID
            $dressesCount++;
        }
        fclose($dressesFile);
        
        $pdo->commit();
        echo "✅ Imported {$dressesCount} dresses with updated names\n\n";
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
    
    // Step 5: Import updated dress items
    echo "Step 5: Importing updated dress items...\n";
    $pdo->beginTransaction();
    
    try {
        $itemsFile = fopen('dress_items_data_updated.csv', 'r');
        fgetcsv($itemsFile); // Skip header
        
        $itemsCount = 0;
        
        while (($data = fgetcsv($itemsFile)) !== FALSE) {
            $dressId = $dressMap[$data[0]]; // Get dress ID from SKU
            
            $stmt = $pdo->prepare("
                INSERT INTO dress_items (dress_id, barcode, status, size_discount_percentage, 
                                       size_discount_active, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $dressId, // dress_id
                $data[1], // barcode
                $data[2], // status
                $data[3], // size_discount_percentage
                $data[4] === 'true', // size_discount_active
                $data[5], // created_at
                $data[6]  // updated_at
            ]);
            
            $itemsCount++;
        }
        fclose($itemsFile);
        
        $pdo->commit();
        echo "✅ Imported {$itemsCount} dress items\n\n";
        
    } catch (Exception $e) {
        $pdo->rollback();
        throw $e;
    }
    
    // Step 6: Final verification
    echo "Step 6: Verifying updated data...\n";
    $newCollections = $pdo->query("SELECT COUNT(*) as count FROM collections WHERE status = 'active'")->fetch()['count'];
    $newDresses = $pdo->query("SELECT COUNT(*) as count FROM dresses WHERE status = 'active'")->fetch()['count'];
    $newItems = $pdo->query("SELECT COUNT(*) as count FROM dress_items WHERE status = 'available'")->fetch()['count'];
    
    $minBarcode = $pdo->query("SELECT MIN(barcode) as min FROM dress_items")->fetch()['min'];
    $maxBarcode = $pdo->query("SELECT MAX(barcode) as max FROM dress_items")->fetch()['max'];
    
    echo "Updated data imported successfully:\n";
    echo "✅ Collections: {$newCollections}\n";
    echo "✅ Dresses: {$newDresses}\n";
    echo "✅ Dress Items: {$newItems}\n";
    echo "✅ Barcode Range: {$minBarcode} to {$maxBarcode}\n";
    
    // Check for data integrity
    $orphanedDresses = $pdo->query("
        SELECT COUNT(*) as count 
        FROM dresses d 
        LEFT JOIN collections c ON d.collection_id = c.id 
        WHERE c.id IS NULL
    ")->fetch()['count'];
    
    $orphanedItems = $pdo->query("
        SELECT COUNT(*) as count 
        FROM dress_items di 
        LEFT JOIN dresses d ON di.dress_id = d.id 
        WHERE d.id IS NULL
    ")->fetch()['count'];
    
    $duplicateBarcodes = $pdo->query("
        SELECT COUNT(*) as count 
        FROM (
            SELECT barcode 
            FROM dress_items 
            GROUP BY barcode 
            HAVING COUNT(*) > 1
        ) as duplicates
    ")->fetch()['count'];
    
    echo "\nData Integrity Check:\n";
    echo "✅ Orphaned Dresses: {$orphanedDresses}\n";
    echo "✅ Orphaned Items: {$orphanedItems}\n";
    echo "✅ Duplicate Barcodes: {$duplicateBarcodes}\n";
    
    // Show sample of updated dress names
    echo "\nSample Updated Dress Names:\n";
    $sampleDresses = $pdo->query("
        SELECT d.name, d.size, c.name as collection_name, d.sale_price
        FROM dresses d
        JOIN collections c ON d.collection_id = c.id
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($sampleDresses as $dress) {
        echo "✅ {$dress['name']} - {$dress['collection_name']} - PKR {$dress['sale_price']}\n";
    }
    
    if ($orphanedDresses == 0 && $orphanedItems == 0 && $duplicateBarcodes == 0) {
        echo "\n🎉 DATA UPDATE COMPLETED SUCCESSFULLY! 🎉\n";
        echo "Your POS system has been updated with the latest inventory data.\n";
        echo "Product names now include sizes (e.g., 'Nooraya S', 'Nooraya M')\n";
    } else {
        echo "\n⚠️ Warning: Data integrity issues detected!\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>
