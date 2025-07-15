<?php

// Test the new modern return API endpoints

echo "Testing Modern Return API Endpoints\n";
echo "====================================\n\n";

// Login first
$loginUrl = 'http://127.0.0.1:8000/api/login';
$loginData = [
    'email' => 'admin@tspos.com',
    'password' => 'password'
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $loginUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($loginData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json'
    ],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
$result = json_decode($response, true);
curl_close($curl);

if ($httpCode !== 200 || !isset($result['token'])) {
    echo "❌ Login failed!\n";
    echo "HTTP Code: {$httpCode}\n";
    echo "Response: {$response}\n";
    exit;
}

$token = $result['token'];
echo "✅ Login successful!\n\n";

// Test 1: Search for returns
$testBarcode = "COL001-DRESS001-SIZE-S-001";
$searchUrl = "http://127.0.0.1:8000/api/returns/search/" . urlencode($testBarcode);

echo "Test 1: Search for return item\n";
echo "URL: {$searchUrl}\n";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $searchUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "HTTP Code: {$httpCode}\n";
echo "Response: " . json_encode(json_decode($response, true), JSON_PRETTY_PRINT) . "\n\n";

// Test 2: Get stats
$statsUrl = "http://127.0.0.1:8000/api/returns/stats";

echo "Test 2: Get return statistics\n";
echo "URL: {$statsUrl}\n";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $statsUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "HTTP Code: {$httpCode}\n";
echo "Response: " . json_encode(json_decode($response, true), JSON_PRETTY_PRINT) . "\n\n";

// Test 3: Get recent returns
$recentUrl = "http://127.0.0.1:8000/api/returns/recent";

echo "Test 3: Get recent returns\n";
echo "URL: {$recentUrl}\n";

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $recentUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Bearer ' . $token,
        'Accept: application/json'
    ],
]);

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "HTTP Code: {$httpCode}\n";
echo "Response: " . json_encode(json_decode($response, true), JSON_PRETTY_PRINT) . "\n\n";

echo "✅ API Testing completed!\n";
