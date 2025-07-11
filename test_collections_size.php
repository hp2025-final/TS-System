<?php
// Test collections API with size data
$url = "http://localhost:8000/api/collections";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: " . $httpCode . "\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "Number of collections: " . count($data) . "\n";
    
    if (count($data) > 0) {
        echo "First collection size breakdown:\n";
        print_r($data[0]['size_breakdown']);
    }
}
?>
