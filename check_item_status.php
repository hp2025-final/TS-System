<?php
require 'vendor/autoload.php';

// Check specific dress item that was sold
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/dress-items/1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
$itemData = json_decode($response, true);

echo "Dress Item Status:\n";
echo "ID: " . $itemData['id'] . "\n";
echo "Barcode: " . $itemData['barcode'] . "\n";
echo "Status: " . $itemData['status'] . "\n";
echo "Sold At: " . $itemData['sold_at'] . "\n";

curl_close($ch);
