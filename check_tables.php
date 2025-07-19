<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Sales table columns:\n";
$sales_columns = DB::select('DESCRIBE sales');
foreach ($sales_columns as $column) {
    echo $column->Field . "\n";
}

echo "\nSale_items table columns:\n";
$sale_items_columns = DB::select('DESCRIBE sale_items');
foreach ($sale_items_columns as $column) {
    echo $column->Field . "\n";
}

echo "\nReturn-related tables:\n";
$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $table_name = array_values((array)$table)[0];
    if (strpos($table_name, 'return') !== false) {
        echo $table_name . "\n";
        // Show columns for return tables
        $return_columns = DB::select("DESCRIBE $table_name");
        foreach ($return_columns as $column) {
            echo "  " . $column->Field . "\n";
        }
    }
}
