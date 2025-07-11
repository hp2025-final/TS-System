<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Dress;
use App\Models\DressItem;
use Illuminate\Support\Facades\Schema;

// Initialize Laravel application
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DATABASE STRUCTURE TEST ===\n\n";

// Test dress with size and HS code
$dress = Dress::first();
echo "Sample Dress:\n";
echo "- Name: " . $dress->name . "\n";
echo "- Size: " . $dress->size . "\n";
echo "- HS Code: " . $dress->hs_code . "\n";
echo "- SKU: " . $dress->sku . "\n\n";

// Test dress item without size column
$dressItem = DressItem::first();
echo "Sample Dress Item:\n";
echo "- Barcode: " . $dressItem->barcode . "\n";
echo "- Dress ID: " . $dressItem->dress_id . "\n";
echo "- Size Discount: " . $dressItem->size_discount_percentage . "%\n\n";

// Check sales table structure
echo "Sales Table Columns:\n";
$salesColumns = Schema::getColumnListing('sales');
foreach ($salesColumns as $column) {
    echo "- " . $column . "\n";
}

echo "\n=== STRUCTURE VERIFICATION COMPLETE ===\n";
