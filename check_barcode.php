<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

use App\Models\DressItem;

echo "Checking barcode 2503082...\n";

$item = DressItem::where('barcode', '2503082')->with(['dress.collection'])->first();

if ($item) {
    echo "Found: " . $item->barcode . " - " . $item->dress->name . " - Status: " . $item->status . "\n";
    echo "Dress ID: " . $item->dress_id . "\n";
    echo "Size: " . $item->size . "\n";
    echo "Price: " . $item->dress->sale_price . "\n";
} else {
    echo "Not found in database\n";
}

// Check all items
echo "\nAll dress items:\n";
$items = DressItem::with(['dress.collection'])->limit(5)->get();
foreach ($items as $item) {
    echo "- " . $item->barcode . " (" . $item->dress->name . ")\n";
}
