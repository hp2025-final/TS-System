<?php

require 'vendor/autoload.php';

use App\Models\Sale;
use App\Models\SaleItem;

// Get some sales to test returns
$sales = Sale::with(['saleItems.dressItem.dress.collection'])->take(3)->get();

echo "Sales data for testing returns:\n";
echo "=================================\n\n";

foreach($sales as $sale) {
    echo "Sale ID: {$sale->id} - Invoice: {$sale->invoice_number}\n";
    echo "Date: {$sale->sale_date}\n";
    echo "Items:\n";
    
    foreach($sale->saleItems as $item) {
        $dressItem = $item->dressItem;
        $dress = $dressItem->dress;
        $collection = $dress->collection;
        
        echo "  - Barcode: {$dressItem->barcode}\n";
        echo "    Name: {$dress->name}\n";
        echo "    Collection: {$collection->name}\n";
        echo "    Size: {$dress->size}\n";
        echo "    Item Total: Rs. {$item->item_total}\n";
        echo "    Status: {$dressItem->status}\n";
        echo "\n";
    }
    echo "---\n\n";
}
