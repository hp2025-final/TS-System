<?php
// Test the collections API to see if unstitched data is present
$url = 'http://localhost:8000/api/collections';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: " . $httpCode . "\n";
echo "Response: " . $response . "\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "\n=== Collections with Size Breakdown ===\n";
    foreach ($data as $collection) {
        echo "Collection: " . $collection['name'] . "\n";
        echo "Size Breakdown:\n";
        foreach ($collection['size_breakdown'] as $size => $count) {
            echo "  $size: $count\n";
        }
        echo "\n";
    }
}
?>
