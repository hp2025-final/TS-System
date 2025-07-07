<?php
require 'vendor/autoload.php';

// Test login first
$loginData = [
    'email' => 'admin@tspos.com',
    'password' => 'password'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$loginResponse = curl_exec($ch);
$loginData = json_decode($loginResponse, true);

if (isset($loginData['token'])) {
    echo "Login successful! Token: " . substr($loginData['token'], 0, 20) . "...\n";
    
    // Test checkout
    $checkoutData = [
        'items' => [
            ['dress_item_id' => 1]
        ],
        'payment_method' => 'cash',
        'customer_name' => 'Test Customer',
        'customer_phone' => '1234567890',
        'subtotal' => 100.00,
        'tax_amount' => 10.00,
        'total_amount' => 110.00
    ];
    
    curl_setopt($ch, CURLOPT_URL, 'http://localhost:8000/api/sales');
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($checkoutData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $loginData['token']
    ]);
    
    $checkoutResponse = curl_exec($ch);
    $checkoutData = json_decode($checkoutResponse, true);
    
    echo "Checkout response:\n";
    print_r($checkoutData);
    
} else {
    echo "Login failed:\n";
    print_r($loginData);
}

curl_close($ch);
