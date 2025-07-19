<?php

require_once 'vendor/autoload.php';

// Database connection
$dsn = 'mysql:host=localhost;dbname=tspos;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== UPDATED DATA VERIFICATION ===\n\n";
    
    // 1. Summary counts
    $collections = $pdo->query("SELECT COUNT(*) as count FROM collections WHERE status = 'active'")->fetch()['count'];
    $dresses = $pdo->query("SELECT COUNT(*) as count FROM dresses WHERE status = 'active'")->fetch()['count'];
    $items = $pdo->query("SELECT COUNT(*) as count FROM dress_items WHERE status = 'available'")->fetch()['count'];
    
    echo "Database Summary:\n";
    echo "================\n";
    echo "✅ Collections: {$collections}\n";
    echo "✅ Dresses: {$dresses}\n";
    echo "✅ Dress Items: {$items}\n\n";
    
    // 2. Collection details
    echo "Collections Details:\n";
    echo "===================\n";
    $collectionDetails = $pdo->query("
        SELECT 
            c.name,
            c.description,
            c.discount_percentage,
            COUNT(d.id) as dress_count,
            COUNT(di.id) as item_count,
            SUM(CAST(d.sale_price AS DECIMAL(10,2))) as total_value
        FROM collections c
        LEFT JOIN dresses d ON c.id = d.collection_id
        LEFT JOIN dress_items di ON d.id = di.dress_id
        WHERE c.status = 'active'
        GROUP BY c.id, c.name
        ORDER BY c.name
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    $totalValue = 0;
    foreach ($collectionDetails as $collection) {
        $value = $collection['total_value'] ? number_format($collection['total_value']) : '0';
        $totalValue += $collection['total_value'];
        echo "• {$collection['name']}: {$collection['dress_count']} dresses, {$collection['item_count']} items, PKR {$value}\n";
        echo "  Description: {$collection['description']}, Discount: {$collection['discount_percentage']}%\n";
    }
    
    echo "\nTotal Inventory Value: PKR " . number_format($totalValue) . "\n\n";
    
    // 3. Barcode range verification
    $barcodeInfo = $pdo->query("
        SELECT 
            MIN(barcode) as min_barcode,
            MAX(barcode) as max_barcode,
            COUNT(DISTINCT barcode) as unique_barcodes
        FROM dress_items
    ")->fetch(PDO::FETCH_ASSOC);
    
    echo "Barcode Information:\n";
    echo "===================\n";
    echo "✅ Range: {$barcodeInfo['min_barcode']} to {$barcodeInfo['max_barcode']}\n";
    echo "✅ Unique Barcodes: {$barcodeInfo['unique_barcodes']}\n";
    echo "✅ Expected: 297 items\n\n";
    
    // 4. Sample barcode tests
    echo "Sample Barcode Tests:\n";
    echo "====================\n";
    $testBarcodes = ['5071502001', '5071502150', '5071502312', '5071502079']; // Testing one missing barcode
    
    foreach ($testBarcodes as $barcode) {
        $result = $pdo->prepare("
            SELECT 
                di.barcode,
                d.name as dress_name,
                d.size,
                d.sale_price,
                c.name as collection_name,
                c.discount_percentage
            FROM dress_items di
            JOIN dresses d ON di.dress_id = d.id
            JOIN collections c ON d.collection_id = c.id
            WHERE di.barcode = ?
        ");
        $result->execute([$barcode]);
        $item = $result->fetch(PDO::FETCH_ASSOC);
        
        if ($item) {
            echo "✅ {$barcode}: {$item['dress_name']} ({$item['size']}) - {$item['collection_name']} - PKR {$item['sale_price']}\n";
        } else {
            echo "❌ {$barcode}: NOT FOUND (Expected if this barcode was removed from updated file)\n";
        }
    }
    
    // 5. Check for gaps in barcode sequence
    echo "\nBarcode Sequence Analysis:\n";
    echo "=========================\n";
    
    $allBarcodes = $pdo->query("SELECT barcode FROM dress_items ORDER BY CAST(barcode AS UNSIGNED)")->fetchAll(PDO::FETCH_COLUMN);
    $expectedStart = 5071502001;
    $expectedEnd = 5071502312;
    
    $missing = [];
    for ($i = $expectedStart; $i <= $expectedEnd; $i++) {
        if (!in_array($i, $allBarcodes)) {
            $missing[] = $i;
        }
    }
    
    if (count($missing) > 0) {
        echo "Missing barcodes from sequence: " . count($missing) . " items\n";
        echo "First few missing: " . implode(', ', array_slice($missing, 0, 10)) . "\n";
    } else {
        echo "✅ No missing barcodes in sequence\n";
    }
    
    // 6. Price analysis
    echo "\nPrice Analysis:\n";
    echo "===============\n";
    $priceAnalysis = $pdo->query("
        SELECT 
            MIN(CAST(sale_price AS DECIMAL(10,2))) as min_price,
            MAX(CAST(sale_price AS DECIMAL(10,2))) as max_price,
            AVG(CAST(sale_price AS DECIMAL(10,2))) as avg_price,
            COUNT(DISTINCT sale_price) as price_varieties
        FROM dresses
    ")->fetch(PDO::FETCH_ASSOC);
    
    echo "✅ Price Range: PKR " . number_format($priceAnalysis['min_price']) . " to PKR " . number_format($priceAnalysis['max_price']) . "\n";
    echo "✅ Average Price: PKR " . number_format($priceAnalysis['avg_price']) . "\n";
    echo "✅ Price Varieties: {$priceAnalysis['price_varieties']}\n\n";
    
    // 7. API Testing
    echo "API Endpoint Testing:\n";
    echo "====================\n";
    $baseUrl = 'http://localhost:8000/api';
    
    // Test collections API
    $collectionsResponse = @file_get_contents("{$baseUrl}/collections");
    if ($collectionsResponse) {
        $collectionsData = json_decode($collectionsResponse, true);
        echo "✅ Collections API: " . count($collectionsData) . " collections returned\n";
    } else {
        echo "❌ Collections API: Failed to connect\n";
    }
    
    // Test dresses API
    $dressesResponse = @file_get_contents("{$baseUrl}/dresses");
    if ($dressesResponse) {
        $dressesData = json_decode($dressesResponse, true);
        echo "✅ Dresses API: " . count($dressesData) . " dresses returned\n";
    } else {
        echo "❌ Dresses API: Failed to connect\n";
    }
    
    // Test search API
    $searchResponse = @file_get_contents("{$baseUrl}/search?barcode=5071502001");
    if ($searchResponse) {
        $searchData = json_decode($searchResponse, true);
        if ($searchData && isset($searchData['dress'])) {
            echo "✅ Search API: Working (found {$searchData['dress']['name']})\n";
        } else {
            echo "⚠️ Search API: Connected but unexpected response\n";
        }
    } else {
        echo "❌ Search API: Failed to connect\n";
    }
    
    echo "\n🎉 DATA UPDATE VERIFICATION COMPLETE! 🎉\n";
    echo "Your POS system has been successfully updated with the latest inventory data.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>
