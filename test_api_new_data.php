<?php
/**
 * Test API endpoints with new data
 */

echo "=== API Testing ===\n";

// Test Collections API
echo "\n--- Testing Collections API ---\n";
$collectionsUrl = 'http://localhost:8000/api/collections';
$collectionsResponse = @file_get_contents($collectionsUrl);

if ($collectionsResponse === false) {
    echo "❌ Collections API failed\n";
} else {
    $collections = json_decode($collectionsResponse, true);
    echo "✓ Collections API working\n";
    echo "Collections count: " . count($collections) . "\n";
    if (count($collections) > 0) {
        echo "First collection: " . $collections[0]['name'] . "\n";
    }
}

// Test Dresses API
echo "\n--- Testing Dresses API ---\n";
$dressesUrl = 'http://localhost:8000/api/dresses?limit=10';
$dressesResponse = @file_get_contents($dressesUrl);

if ($dressesResponse === false) {
    echo "❌ Dresses API failed\n";
} else {
    $dressesData = json_decode($dressesResponse, true);
    echo "✓ Dresses API working\n";
    if (isset($dressesData['data'])) {
        echo "Dresses found: " . count($dressesData['data']) . "\n";
        if (count($dressesData['data']) > 0) {
            $firstDress = $dressesData['data'][0];
            echo "First dress: " . $firstDress['name'] . " (" . $firstDress['sku'] . ")\n";
            echo "Collection: " . $firstDress['collection']['name'] . "\n";
        }
    }
}

// Test Dress Items API
echo "\n--- Testing Dress Items API ---\n";
$itemsUrl = 'http://localhost:8000/api/dress-items?limit=10';
$itemsResponse = @file_get_contents($itemsUrl);

if ($itemsResponse === false) {
    echo "❌ Dress Items API failed\n";
} else {
    $itemsData = json_decode($itemsResponse, true);
    echo "✓ Dress Items API working\n";
    if (is_array($itemsData) && count($itemsData) > 0) {
        echo "Items found: " . count($itemsData) . "\n";
        $firstItem = $itemsData[0];
        echo "First item barcode: " . $firstItem['barcode'] . "\n";
        echo "Status: " . $firstItem['status'] . "\n";
    }
}

echo "\n🎉 API testing completed!\n";
