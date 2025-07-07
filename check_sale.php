<?php
require 'vendor/autoload.php';

// Use curl to get the sale data
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/sales/1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$saleData = json_decode($response, true);

echo "Sale Data from API:\n";
print_r($saleData);

curl_close($ch);
