<?php
/**
 * Test specific API endpoints to see detailed responses
 */

echo "=== API Response Details ===\n";

// Test Dresses API with detailed output
echo "\n--- Dresses API Response ---\n";
$dressesUrl = 'http://localhost:8000/api/dresses?limit=3';
$dressesResponse = file_get_contents($dressesUrl);

if ($dressesResponse) {
    $dressesData = json_decode($dressesResponse, true);
    echo "Dresses API Response Structure:\n";
    echo json_encode($dressesData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
} else {
    echo "❌ Failed to get dresses data\n";
}

// Test Collections API with detailed output
echo "\n\n--- Collections API Response ---\n";
$collectionsUrl = 'http://localhost:8000/api/collections';
$collectionsResponse = file_get_contents($collectionsUrl);

if ($collectionsResponse) {
    $collectionsData = json_decode($collectionsResponse, true);
    echo "Found " . count($collectionsData) . " collections:\n";
    foreach ($collectionsData as $collection) {
        echo "- {$collection['name']}: {$collection['dresses_count']} dresses, {$collection['total_items']} items\n";
    }
} else {
    echo "❌ Failed to get collections data\n";
}
