<?php

require_once 'vendor/autoload.php';

// Load environment configuration
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== DATABASE SEED VERIFICATION ===\n\n";

// Get collections with their dress counts
$collections = \App\Models\Collection::withCount(['dresses', 'dresses as dress_items_count' => function($query) {
    $query->join('dress_items', 'dresses.id', '=', 'dress_items.dress_id');
}])->get();

echo "COLLECTIONS CREATED:\n";
foreach ($collections as $collection) {
    echo "- {$collection->name}: {$collection->dresses_count} dresses, {$collection->dress_items_count} dress items\n";
}

echo "\nSTATISTICS:\n";
echo "- Total Collections: " . \App\Models\Collection::count() . "\n";
echo "- Total Dresses: " . \App\Models\Dress::count() . "\n";
echo "- Total Dress Items: " . \App\Models\DressItem::count() . "\n";
echo "- Users Preserved: " . \App\Models\User::count() . "\n";

echo "\nSTRUCTURE VERIFICATION:\n";
echo "- Expected: 8 collections × 16 dresses × 7 sizes = 896 dresses\n";
echo "- Expected: 896 dresses × 2 pieces = 1,792 dress items\n";
echo "- Actual: " . \App\Models\Dress::count() . " dresses, " . \App\Models\DressItem::count() . " dress items\n";

$isCorrect = (\App\Models\Collection::count() == 8) && 
             (\App\Models\Dress::count() == 896) && 
             (\App\Models\DressItem::count() == 1792);

echo "\nRESULT: " . ($isCorrect ? "✅ SUCCESS - All data created correctly!" : "❌ ERROR - Data counts don't match expectations") . "\n";

// Show some sample data
echo "\nSAMPLE DATA:\n";
$sampleDresses = \App\Models\Dress::with('collection')->take(3)->get();
foreach ($sampleDresses as $dress) {
    echo "- {$dress->collection->name}: {$dress->name} (SKU: {$dress->sku})\n";
}

echo "\nBARCODE RANGE:\n";
$firstBarcode = \App\Models\DressItem::min('barcode');
$lastBarcode = \App\Models\DressItem::max('barcode');
echo "- First Barcode: {$firstBarcode}\n";
echo "- Last Barcode: {$lastBarcode}\n";

echo "\n=== VERIFICATION COMPLETE ===\n";
