<?php

require_once 'vendor/autoload.php';

// Database connection
$dsn = 'mysql:host=localhost;dbname=tspos;charset=utf8';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== RETURNS TABLE INVESTIGATION ===\n\n";
    
    // Check returns table structure
    echo "1. Returns table structure:\n";
    $columns = $pdo->query("DESCRIBE returns")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $column) {
        echo "   - {$column['Field']} ({$column['Type']}) - {$column['Null']} - {$column['Key']}\n";
    }
    
    echo "\n2. Count of returns:\n";
    $count = $pdo->query("SELECT COUNT(*) as count FROM returns")->fetch(PDO::FETCH_ASSOC);
    echo "   Total returns: {$count['count']}\n";
    
    if ($count['count'] > 0) {
        echo "\n3. Sample returns data:\n";
        $returns = $pdo->query("SELECT * FROM returns LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($returns as $return) {
            echo "   Return ID: {$return['id']}\n";
            echo "   Sale Item ID: {$return['sale_item_id']}\n";
            echo "   Return Type: {$return['return_type']}\n";
            echo "   Return Date: {$return['return_date']}\n";
            echo "   Refund Amount: {$return['refund_amount']}\n";
            echo "   Exchange Amount: {$return['exchange_amount']}\n";
            echo "   ---\n";
        }
        
        echo "\n4. Test the report query:\n";
        $testQuery = "
            SELECT 
                r.id,
                di.barcode,
                si.dress_name,
                si.collection_name,
                si.sku,
                si.size,
                r.refund_amount,
                r.exchange_amount,
                r.return_type,
                r.reason,
                r.condition_at_return,
                s.invoice_number,
                s.customer_name,
                s.customer_phone,
                s.sale_date as original_sale_date,
                r.return_date,
                u.name as processed_by_name
            FROM returns as r
            LEFT JOIN sale_items as si ON r.sale_item_id = si.id
            LEFT JOIN sales as s ON si.sale_id = s.id
            LEFT JOIN users as u ON r.processed_by = u.id
            LEFT JOIN dress_items as di ON si.dress_item_id = di.id
            ORDER BY r.return_date DESC, r.created_at DESC
            LIMIT 3
        ";
        
        $testResults = $pdo->query($testQuery)->fetchAll(PDO::FETCH_ASSOC);
        echo "   Query results count: " . count($testResults) . "\n";
        
        foreach ($testResults as $result) {
            echo "   ---\n";
            echo "   Return ID: {$result['id']}\n";
            echo "   Barcode: " . ($result['barcode'] ?? 'NULL') . "\n";
            echo "   Dress Name: " . ($result['dress_name'] ?? 'NULL') . "\n";
            echo "   Collection: " . ($result['collection_name'] ?? 'NULL') . "\n";
            echo "   Return Type: " . ($result['return_type'] ?? 'NULL') . "\n";
            echo "   Customer: " . ($result['customer_name'] ?? 'NULL') . "\n";
            echo "   Invoice: " . ($result['invoice_number'] ?? 'NULL') . "\n";
        }
    }
    
    echo "\n5. Check sale_items table:\n";
    $saleItemsCount = $pdo->query("SELECT COUNT(*) as count FROM sale_items")->fetch(PDO::FETCH_ASSOC);
    echo "   Total sale items: {$saleItemsCount['count']}\n";
    
    echo "\n6. Check sales table:\n";
    $salesCount = $pdo->query("SELECT COUNT(*) as count FROM sales")->fetch(PDO::FETCH_ASSOC);
    echo "   Total sales: {$salesCount['count']}\n";
    
    echo "\n7. Check dress_items table:\n";
    $dressItemsCount = $pdo->query("SELECT COUNT(*) as count FROM dress_items")->fetch(PDO::FETCH_ASSOC);
    echo "   Total dress items: {$dressItemsCount['count']}\n";
    
    // Check if there are any returns with missing relationships
    if ($count['count'] > 0) {
        echo "\n8. Check for broken relationships:\n";
        
        $brokenSaleItems = $pdo->query("
            SELECT COUNT(*) as count 
            FROM returns r 
            LEFT JOIN sale_items si ON r.sale_item_id = si.id 
            WHERE si.id IS NULL
        ")->fetch(PDO::FETCH_ASSOC);
        echo "   Returns with missing sale_items: {$brokenSaleItems['count']}\n";
        
        $brokenSales = $pdo->query("
            SELECT COUNT(*) as count 
            FROM returns r 
            LEFT JOIN sale_items si ON r.sale_item_id = si.id 
            LEFT JOIN sales s ON si.sale_id = s.id 
            WHERE si.id IS NOT NULL AND s.id IS NULL
        ")->fetch(PDO::FETCH_ASSOC);
        echo "   Returns with missing sales: {$brokenSales['count']}\n";
        
        $brokenDressItems = $pdo->query("
            SELECT COUNT(*) as count 
            FROM returns r 
            LEFT JOIN sale_items si ON r.sale_item_id = si.id 
            LEFT JOIN dress_items di ON si.dress_item_id = di.id 
            WHERE si.id IS NOT NULL AND di.id IS NULL
        ")->fetch(PDO::FETCH_ASSOC);
        echo "   Returns with missing dress_items: {$brokenDressItems['count']}\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

?>
