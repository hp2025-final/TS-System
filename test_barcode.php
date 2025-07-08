<?php

// Test script to check barcode API response
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DressItem;

$item = DressItem::with(['dress.collection'])->where('barcode', '2507071001')->first();

if ($item) {
    echo "Item found:\n";
    echo "ID: " . $item->id . "\n";
    echo "Barcode: " . $item->barcode . "\n";
    echo "Size: " . $item->size . "\n";
    echo "Status: " . $item->status . "\n";
    echo "Has dress: " . ($item->dress ? 'Yes' : 'No') . "\n";
    if ($item->dress) {
        echo "Dress name: " . $item->dress->name . "\n";
        echo "Dress price: " . $item->dress->sale_price . "\n";
        echo "Has collection: " . ($item->dress->collection ? 'Yes' : 'No') . "\n";
        if ($item->dress->collection) {
            echo "Collection name: " . $item->dress->collection->name . "\n";
        }
    }
} else {
    echo "Item not found\n";
}
