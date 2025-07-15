<?php

// Test the new return search API endpoint

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
