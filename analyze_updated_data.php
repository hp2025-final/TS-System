<?php

// Read and analyze the updated CSV file
$csvFile = 'Dress_Data.csv';
$handle = fopen($csvFile, 'r');

// Skip header row
fgetcsv($handle);

$collections = [];
$dresses = [];
$dressItems = [];

$totalItems = 0;

echo "=== ANALYZING UPDATED DRESS DATA ===\n\n";

while (($data = fgetcsv($handle)) !== FALSE) {
    $totalItems++;
    
    // Extract data from CSV
    $barcode = trim($data[0]);
    $productName = trim($data[1]);
    $sku = trim($data[2]);
    $collectionName = trim($data[3]);
    $size = trim($data[4]);
    $salePrice = str_replace([' ', ','], '', trim($data[5], '" '));
    $costPrice = str_replace([' ', ','], '', trim($data[6], '" '));
    $collectionDesc = trim($data[7]);
    $collectionDiscount = str_replace('%', '', trim($data[8]));
    $taxPercentage = str_replace('%', '', trim($data[9]));
    $status = trim($data[10]);
    
    // Store collection info
    if (!isset($collections[$collectionName])) {
        $collections[$collectionName] = [
            'name' => $collectionName,
            'description' => $collectionDesc,
            'discount_percentage' => $collectionDiscount,
            'items_count' => 0
        ];
    }
    $collections[$collectionName]['items_count']++;
    
    // Create unique dress key (name + size + sku)
    $dressKey = $productName . '|' . $size . '|' . $sku;
    if (!isset($dresses[$dressKey])) {
        $dresses[$dressKey] = [
            'name' => $productName,
            'sku' => $sku . '-' . $size, // Make SKU unique with size suffix
            'size' => $size,
            'description' => $collectionDesc,
            'cost_price' => $costPrice,
            'sale_price' => $salePrice,
            'collection' => $collectionName,
            'tax_percentage' => $taxPercentage,
            'items_count' => 0
        ];
    }
    $dresses[$dressKey]['items_count']++;
    
    // Store dress item
    $dressItems[] = [
        'barcode' => $barcode,
        'dress_key' => $dressKey,
        'status' => $status === 'ACTIVE' ? 'available' : 'unavailable'
    ];
}

fclose($handle);

echo "Data Analysis Results:\n";
echo "=====================\n";
echo "Total Items: " . $totalItems . "\n";
echo "Collections: " . count($collections) . "\n";
echo "Unique Dresses: " . count($dresses) . "\n\n";

echo "Collections Found:\n";
foreach ($collections as $collection) {
    echo "- {$collection['name']}: {$collection['items_count']} items ({$collection['description']})\n";
}

echo "\nBarcode Range:\n";
echo "First: " . $dressItems[0]['barcode'] . "\n";
echo "Last: " . end($dressItems)['barcode'] . "\n";

echo "\nSample Dress Items:\n";
$sampleCount = 0;
foreach ($dresses as $key => $dress) {
    if ($sampleCount >= 5) break;
    echo "- {$dress['name']} ({$dress['size']}) - {$dress['collection']} - PKR {$dress['sale_price']}\n";
    $sampleCount++;
}

echo "\n=== READY TO CREATE NEW DATA FILES ===\n";

?>
