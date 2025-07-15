<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Check if migration record exists
$migrationName = '2025_07_15_012303_add_return_fields_to_sale_items_table';
$exists = DB::table('migrations')->where('migration', $migrationName)->exists();

if (!$exists) {
    // Get the current batch number
    $batch = DB::table('migrations')->max('batch') + 1;
    
    // Insert migration record
    DB::table('migrations')->insert([
        'migration' => $migrationName,
        'batch' => $batch
    ]);
    
    echo "Migration record inserted for: $migrationName\n";
} else {
    echo "Migration record already exists for: $migrationName\n";
}

echo "Migration status updated successfully!\n";
