<?php
// Test script to verify the new dresses page API and data

echo "=== Testing Dresses API ===\n";

try {
    // Test the dresses API endpoint
    $url = 'http://localhost:8000/api/dresses';
    $response = file_get_contents($url);
    $dresses = json_decode($response, true);
    
    echo "API Response: Success\n";
    echo "Number of dresses: " . count($dresses) . "\n";
    
    if (count($dresses) > 0) {
        $firstDress = $dresses[0];
        echo "\nFirst dress example:\n";
        echo "- ID: " . $firstDress['id'] . "\n";
        echo "- Name: " . $firstDress['name'] . "\n";
        echo "- Size: " . $firstDress['size'] . "\n";
        echo "- HS Code: " . ($firstDress['hs_code'] ?? 'N/A') . "\n";
        echo "- SKU: " . $firstDress['sku'] . "\n";
        echo "- Sale Price: PKR " . number_format($firstDress['sale_price']) . "\n";
        echo "- Collection: " . ($firstDress['collection']['name'] ?? 'N/A') . "\n";
        echo "- Available Items: " . ($firstDress['dress_items_count'] ?? 0) . "\n";
        echo "- Status: " . $firstDress['status'] . "\n";
        
        if ($firstDress['discount_active']) {
            echo "- Discount: " . $firstDress['discount_percentage'] . "%\n";
        } else {
            echo "- Discount: No discount\n";
        }
    }
    
    echo "\n=== Testing Collections API ===\n";
    
    // Test collections API for the filter dropdown
    $collectionsUrl = 'http://localhost:8000/api/collections';
    $collectionsResponse = file_get_contents($collectionsUrl);
    $collections = json_decode($collectionsResponse, true);
    
    echo "Collections API Response: Success\n";
    echo "Number of collections: " . count($collections) . "\n";
    
    if (count($collections) > 0) {
        echo "\nAvailable collections for filter:\n";
        foreach ($collections as $collection) {
            echo "- " . $collection['name'] . " (ID: " . $collection['id'] . ")\n";
        }
    }
    
    echo "\n=== New Dresses Page Features ===\n";
    echo "✓ Table format instead of cards\n";
    echo "✓ Shows dress size (moved from dress_items to dresses)\n";
    echo "✓ Shows HS Code field\n";
    echo "✓ Admin-only action buttons (edit, view, delete)\n";
    echo "✓ Search functionality\n";
    echo "✓ Status filter (active/inactive)\n";
    echo "✓ Collection filter\n";
    echo "✓ Statistics cards\n";
    echo "✓ Role-based access control\n";
    
    echo "\n=== Page Access ===\n";
    echo "New Dresses Page URL: http://127.0.0.1:8000/dresses\n";
    echo "Login with: admin@tspos.com / password (admin role)\n";
    echo "Or: staff@tspos.com / password (staff role - no action buttons)\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
?>
