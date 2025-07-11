<?php
// Test the returns API endpoint

// First, login to get token
$loginUrl = 'http://localhost:8000/api/login';
$loginData = [
    'email' => 'admin@tspos.com',
    'password' => 'password'
];

echo "=== Logging in ===\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);

$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login HTTP Code: $loginHttpCode\n";

if ($loginHttpCode === 200) {
    $loginData = json_decode($loginResponse, true);
    $token = $loginData['token'];
    echo "Login successful, token obtained\n\n";
    
    // First, get a sold item to test with
    echo "=== Getting sold items ===\n";
    $searchUrl = 'http://localhost:8000/api/sales/search-items?query=2503071';
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $searchUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Accept: application/json',
        'Authorization: Bearer ' . $token
    ]);
    
    $searchResponse = curl_exec($ch);
    $searchHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    echo "Search HTTP Code: $searchHttpCode\n";
    echo "Search Response: $searchResponse\n\n";
    
    if ($searchHttpCode === 200) {
        $searchData = json_decode($searchResponse, true);
        if (!empty($searchData['data'])) {
            $soldItem = $searchData['data'][0];
            
            // Now test creating a return
            echo "=== Testing Return Creation ===\n";
            $returnUrl = 'http://localhost:8000/api/returns';
            $returnData = [
                'sale_item_id' => $soldItem['id'],
                'dress_item_id' => $soldItem['dress_item_id'],
                'return_type' => 'exchange',
                'return_reason' => 'wrong_size',
                'refund_amount' => 2800.00,
                'exchange_item_id' => 25, // The item we found earlier (2503095)
                'notes' => 'Test exchange'
            ];
            
            echo "Payload: " . json_encode($returnData, JSON_PRETTY_PRINT) . "\n";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $returnUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($returnData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Bearer ' . $token
            ]);
            
            $returnResponse = curl_exec($ch);
            $returnHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            echo "Return HTTP Code: $returnHttpCode\n";
            echo "Return Response: $returnResponse\n";
        } else {
            echo "No sold items found to test with\n";
        }
    }
} else {
    echo "Login failed:\n";
    echo $loginResponse . "\n";
}
