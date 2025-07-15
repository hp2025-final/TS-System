<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Check if dress_items table exists and get its structure
    if (Schema::hasTable('dress_items')) {
        echo "✅ dress_items table exists\n";
        
        // Get column info
        $columns = DB::select("SHOW COLUMNS FROM dress_items");
        
        echo "\nColumns in dress_items table:\n";
        foreach ($columns as $column) {
            echo "- {$column->Field}: {$column->Type}\n";
            if ($column->Field === 'status') {
                echo "  Status column type: {$column->Type}\n";
            }
        }
        
        // Check for any enum values in status column
        $enumInfo = DB::select("SHOW COLUMNS FROM dress_items WHERE Field = 'status'");
        if (!empty($enumInfo)) {
            echo "\nStatus column details:\n";
            echo "Type: " . $enumInfo[0]->Type . "\n";
        }
        
    } else {
        echo "❌ dress_items table does not exist\n";
    }
    
    // Check returns table
    if (Schema::hasTable('returns')) {
        echo "\n✅ returns table exists\n";
        
        $returnCount = DB::table('returns')->count();
        echo "Returns count: {$returnCount}\n";
        
        if ($returnCount > 0) {
            $reasons = DB::table('returns')
                ->select('return_reason', DB::raw('COUNT(*) as count'))
                ->groupBy('return_reason')
                ->get();
                
            echo "\nReturn reasons:\n";
            foreach ($reasons as $reason) {
                echo "- {$reason->return_reason}: {$reason->count}\n";
            }
        }
    } else {
        echo "\n❌ returns table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nDone!\n";
