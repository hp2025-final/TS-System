<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking dress_items table for returned_at column:\n";
$columns = DB::select('DESCRIBE dress_items');

foreach ($columns as $column) {
    if ($column->Field === 'returned_at') {
        echo $column->Field . ' - ' . $column->Type . ' - Null: ' . $column->Null . ' - Default: ' . $column->Default . "\n";
        break;
    }
}

// Also check for status column
foreach ($columns as $column) {
    if ($column->Field === 'status') {
        echo $column->Field . ' - ' . $column->Type . ' - Null: ' . $column->Null . ' - Default: ' . $column->Default . "\n";
        break;
    }
}
