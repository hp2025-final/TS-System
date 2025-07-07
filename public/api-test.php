<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, X-CSRF-TOKEN');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Simple API test to verify Laravel is working
try {
    require_once __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    
    // Get the current request URI
    $requestUri = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Handle basic API test endpoints
    if ($requestUri === '/api/test') {
        echo json_encode([
            'status' => 'success',
            'message' => 'API is working',
            'time' => date('Y-m-d H:i:s')
        ]);
        exit();
    }
    
    // If it's an API call, try to handle it through Laravel
    if (strpos($requestUri, '/api/') === 0) {
        // Bootstrap Laravel for API requests
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        $request = Illuminate\Http\Request::capture();
        $response = $kernel->handle($request);
        $response->send();
        $kernel->terminate($request, $response);
        exit();
    }
    
    // For non-API requests, show status
    echo json_encode([
        'error' => 'Not an API endpoint',
        'request_uri' => $requestUri,
        'method' => $method,
        'available_endpoints' => [
            '/api/test',
            '/api/login',
            '/api/collections',
            '/api/dresses',
            '/api/dress-items'
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
