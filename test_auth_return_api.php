<?php

// Test the new return search API endpoint with authentication

// First, get auth token
$loginUrl = 'http://127.0.0.1:8000/api/login';
$loginData = [
    'email' => 'admin@tspos.com',
    'password' => 'password'
];

$curl = curl_init();
curl_setopt_array($curl, [
    CURLOPT_URL => $loginUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
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
    echo "Login failed!\n";
    echo "HTTP Code: {$httpCode}\n";
    echo "Response: {$response}\n";
    exit;
}

$token = $result['token'];
echo "Login successful!\n\n";

// Now test the return search API
$barcode = "COL001-DRESS001-SIZE-S-001"; // Example barcode

$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "http://127.0.0.1:8000/api/returns/search-sold-item/{$barcode}",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Authorization: Bearer ' . $token,
    'Accept: application/json',
    'Content-Type: application/json'
  ),
));

$response = curl_exec($curl);
$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

echo "Testing return search API endpoint:\n";
echo "===================================\n";
echo "URL: http://127.0.0.1:8000/api/returns/search-sold-item/{$barcode}\n";
echo "HTTP Code: {$httpCode}\n";
echo "Response:\n";
echo $response . "\n";
