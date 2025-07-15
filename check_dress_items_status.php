<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Check current status values in dress_items
    $statusCounts = DB::table('dress_items')
        ->select('status', DB::raw('COUNT(*) as count'))
        ->groupBy('status')
        ->get();
        
    echo "Current status values in dress_items:\n";
    foreach ($statusCounts as $status) {
        echo "- {$status->status}: {$status->count}\n";
    }
    
    // Check if there are any NULL status values
    $nullCount = DB::table('dress_items')->whereNull('status')->count();
    echo "\nNULL status values: {$nullCount}\n";
    
    // Check if there are any empty string status values
    $emptyCount = DB::table('dress_items')->where('status', '')->count();
    echo "Empty string status values: {$emptyCount}\n";
    
    // Get total count
    $totalCount = DB::table('dress_items')->count();
    echo "\nTotal dress_items: {$totalCount}\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
