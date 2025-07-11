<?php
// Test the authentication by trying to login first and then search
$loginUrl = 'http://localhost:8000/api/login';

// Login first
$loginData = json_encode([
    'email' => 'admin@tspos.com',
    'password' => 'password'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $loginData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'Content-Type: application/json'
]);

$loginResponse = curl_exec($ch);
$loginHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login HTTP Code: " . $loginHttpCode . "\n";
echo "Login Response: " . $loginResponse . "\n";

if ($loginHttpCode === 200) {
    $loginData = json_decode($loginResponse, true);
    $token = $loginData['token'] ?? null;
    
    if ($token) {
        echo "\nNow testing search with token...\n";
        
        // Now try the search with token
        $searchUrl = 'http://localhost:8000/api/sales/search-items?query=2503071';
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $searchUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);
        
        $searchResponse = curl_exec($ch);
        $searchHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "Search HTTP Code: " . $searchHttpCode . "\n";
        echo "Search Response: " . $searchResponse . "\n";
    }
}
?>
