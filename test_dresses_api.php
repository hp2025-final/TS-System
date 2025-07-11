<?php
// Test the dresses API endpoint
$url = "http://localhost:8000/api/dresses";

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
echo "Response: " . $response . "\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    echo "Number of dresses: " . count($data) . "\n";
    
    if (count($data) > 0) {
        echo "First dress:\n";
        print_r($data[0]);
    }
}
?>
