<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking sale_items table structure:\n";
$columns = DB::select('DESCRIBE sale_items');

foreach ($columns as $column) {
    if (in_array($column->Field, ['exchange_return_id', 'is_exchange_item'])) {
        echo $column->Field . ' - ' . $column->Type . ' - Null: ' . $column->Null . ' - Default: ' . $column->Default . "\n";
    }
}

echo "\nAll columns in sale_items:\n";
foreach ($columns as $column) {
    echo $column->Field . "\n";
}
