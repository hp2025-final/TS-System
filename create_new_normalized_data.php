<?php

// Create new normalized data files from updated CSV
$csvFile = 'Dress_Data.csv';
$handle = fopen($csvFile, 'r');

// Skip header row
fgetcsv($handle);

$collections = [];
$dresses = [];
$dressItems = [];

echo "=== CREATING NEW NORMALIZED DATA FILES ===\n\n";

// Process all rows
while (($data = fgetcsv($handle)) !== FALSE) {
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
            'discount_active' => $collectionDiscount > 0 ? 'true' : 'false',
            'status' => 'active'
        ];
    }
    
    // Create unique dress key (name + size + sku)
    $dressKey = $productName . '|' . $size . '|' . $sku;
    if (!isset($dresses[$dressKey])) {
        $dresses[$dressKey] = [
            'collection' => $collectionName,
            'name' => $productName,
            'sku' => $sku . '-' . $size, // Make SKU unique with size suffix
            'description' => $collectionDesc,
            'size' => $size,
            'cost_price' => $costPrice,
            'sale_price' => $salePrice,
            'discount_percentage' => '0.00',
            'discount_active' => 'false',
            'tax_percentage' => $taxPercentage,
            'status' => 'active'
        ];
    }
    
    // Store dress item
    $dressItems[] = [
        'dress_key' => $dressKey,
        'barcode' => $barcode,
        'status' => $status === 'ACTIVE' ? 'available' : 'unavailable',
        'size_discount_percentage' => '0.00',
        'size_discount_active' => 'false',
        'created_at' => '2024-12-31 19:00:00',
        'updated_at' => '2024-12-31 19:00:00'
    ];
}

fclose($handle);

// 1. Create Collections CSV
echo "Creating collections_data_new.csv...\n";
$collectionsFile = fopen('collections_data_new.csv', 'w');
fputcsv($collectionsFile, ['name', 'description', 'discount_percentage', 'discount_active', 'status']);

foreach ($collections as $collection) {
    fputcsv($collectionsFile, [
        $collection['name'],
        $collection['description'],
        $collection['discount_percentage'],
        $collection['discount_active'],
        $collection['status']
    ]);
}
fclose($collectionsFile);

// 2. Create Dresses CSV
echo "Creating dresses_data_new.csv...\n";
$dressesFile = fopen('dresses_data_new.csv', 'w');
fputcsv($dressesFile, [
    'collection_name', 'name', 'sku', 'description', 'size', 'cost_price', 
    'sale_price', 'discount_percentage', 'discount_active', 'tax_percentage', 'status'
]);

foreach ($dresses as $dress) {
    fputcsv($dressesFile, [
        $dress['collection'],
        $dress['name'],
        $dress['sku'],
        $dress['description'],
        $dress['size'],
        $dress['cost_price'],
        $dress['sale_price'],
        $dress['discount_percentage'],
        $dress['discount_active'],
        $dress['tax_percentage'],
        $dress['status']
    ]);
}
fclose($dressesFile);

// 3. Create Dress Items CSV
echo "Creating dress_items_data_new.csv...\n";
$itemsFile = fopen('dress_items_data_new.csv', 'w');
fputcsv($itemsFile, [
    'dress_sku', 'barcode', 'status', 'size_discount_percentage', 
    'size_discount_active', 'created_at', 'updated_at'
]);

foreach ($dressItems as $item) {
    $dressSku = $dresses[$item['dress_key']]['sku'];
    fputcsv($itemsFile, [
        $dressSku,
        $item['barcode'],
        $item['status'],
        $item['size_discount_percentage'],
        $item['size_discount_active'],
        $item['created_at'],
        $item['updated_at']
    ]);
}
fclose($itemsFile);

echo "\n=== DATA FILES CREATED SUCCESSFULLY ===\n";
echo "Collections: " . count($collections) . "\n";
echo "Dresses: " . count($dresses) . "\n";
echo "Dress Items: " . count($dressItems) . "\n";
echo "\nFiles created:\n";
echo "- collections_data_new.csv\n";
echo "- dresses_data_new.csv\n";
echo "- dress_items_data_new.csv\n";

?>
