<?php

// Test the days calculation fix

echo "Testing Days Calculation Fix\n";
echo "============================\n\n";

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
    exit;
}

$token = $result['token'];
echo "✅ Login successful!\n\n";

// Test with the invoice number from screenshot
$testInvoice = "25071422423";
$searchUrl = "http://127.0.0.1:8000/api/returns/search/" . urlencode($testInvoice);

echo "Testing search for invoice: {$testInvoice}\n";
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
$result = json_decode($response, true);
curl_close($curl);

echo "HTTP Code: {$httpCode}\n";

if ($httpCode === 200 && isset($result['items'])) {
    echo "✅ Items found: " . count($result['items']) . "\n\n";
    
    foreach ($result['items'] as $item) {
        echo "Item: {$item['dress_name']}\n";
        echo "Days Remaining: {$item['days_remaining']} (should be whole number)\n";
        echo "Amount: Rs. {$item['item_total']}\n";
        echo "---\n";
    }
} else {
    echo "Response: " . json_encode($result, JSON_PRETTY_PRINT) . "\n";
}

echo "\n✅ Testing completed!\n";
