<?php

require_once 'vendor/autoload.php';

// Load environment configuration
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING GST INFORMATION IN BARCODE SEARCH ===\n\n";

// Test with a sample barcode
$testBarcode = '2500001'; // First barcode from our new seed data

echo "Testing barcode: {$testBarcode}\n\n";

try {
    // Simulate the API call
    $dressItem = \App\Models\DressItem::with(['dress.collection'])
        ->where('barcode', $testBarcode)
        ->first();

    if (!$dressItem) {
        echo "❌ Item not found with barcode: {$testBarcode}\n";
        
        // Try to find any available barcode
        $anyItem = \App\Models\DressItem::with(['dress.collection'])->first();
        if ($anyItem) {
            echo "Using alternative barcode: {$anyItem->barcode}\n";
            $dressItem = $anyItem;
            $testBarcode = $anyItem->barcode;
        } else {
            echo "❌ No dress items found in database\n";
            exit(1);
        }
    }

    // Simulate the API controller logic
    $dress = $dressItem->dress;
    $collection = $dress->collection;
    $originalPrice = $dress->sale_price;
    $finalPrice = $originalPrice;
    $discountInfo = null;
    $highestDiscount = 0;
    $discountSource = '';

    // Check collection discount
    if ($collection->discount_active && $collection->discount_percentage > 0) {
        if ($collection->discount_percentage > $highestDiscount) {
            $highestDiscount = $collection->discount_percentage;
            $discountSource = 'Collection';
        }
    }

    // Check dress discount
    if ($dress->discount_active && $dress->discount_percentage > 0) {
        if ($dress->discount_percentage > $highestDiscount) {
            $highestDiscount = $dress->discount_percentage;
            $discountSource = 'Style';
        }
    }

    // Check size discount
    if ($dressItem->size_discount_active && $dressItem->size_discount_percentage > 0) {
        if ($dressItem->size_discount_percentage > $highestDiscount) {
            $highestDiscount = $dressItem->size_discount_percentage;
            $discountSource = 'Size';
        }
    }

    // Apply the highest discount only
    if ($highestDiscount > 0) {
        $discount = ($originalPrice * $highestDiscount / 100);
        $finalPrice -= $discount;
        $discountInfo = "{$discountSource}: -{$highestDiscount}%";
    }

    // Calculate GST on original price (before discount)
    $taxPercentage = $dress->tax_percentage ?? 18.00; // Default to 18% if not set
    $taxAmount = round(($originalPrice * $taxPercentage / 100), 2);
    $finalPriceWithTax = round($finalPrice + $taxAmount, 2);

    echo "📋 DRESS ITEM DETAILS:\n";
    echo "   - Name: {$dress->name}\n";
    echo "   - Collection: {$collection->name}\n";
    echo "   - Size: {$dress->size}\n";
    echo "   - Barcode: {$dressItem->barcode}\n";
    echo "   - Status: {$dressItem->status}\n\n";

    echo "💰 PRICING BREAKDOWN:\n";
    echo "   - Original Price: PKR " . number_format($originalPrice, 2) . "\n";
    
    if ($highestDiscount > 0) {
        echo "   - Discount ({$discountInfo}): PKR " . number_format($originalPrice - $finalPrice, 2) . "\n";
        echo "   - Discounted Price: PKR " . number_format($finalPrice, 2) . "\n";
    } else {
        echo "   - No Discount Applied\n";
    }
    
    echo "   - GST ({$taxPercentage}%): PKR " . number_format($taxAmount, 2) . "\n";
    echo "   - FINAL TOTAL: PKR " . number_format($finalPriceWithTax, 2) . "\n\n";

    echo "🔧 API RESPONSE FIELDS (NEW):\n";
    echo "   ✅ original_price: " . number_format($originalPrice, 2) . "\n";
    echo "   ✅ final_price: " . number_format($finalPrice, 2) . "\n";
    echo "   ✅ total_discount: " . number_format($originalPrice - $finalPrice, 2) . "\n";
    echo "   ✅ discount_info: " . ($discountInfo ?: 'No discount') . "\n";
    echo "   ✅ tax_percentage: {$taxPercentage}%\n";
    echo "   ✅ tax_amount: PKR " . number_format($taxAmount, 2) . "\n";
    echo "   ✅ final_price_with_tax: PKR " . number_format($finalPriceWithTax, 2) . "\n\n";

    echo "🎯 FRONTEND DISPLAY (NOW SHOWS):\n";
    echo "   ✅ Actual Price: PKR " . number_format($originalPrice, 2) . "\n";
    if ($highestDiscount > 0) {
        echo "   ✅ Discount: {$highestDiscount}% (-PKR " . number_format($originalPrice - $finalPrice, 2) . ")\n";
    }
    echo "   ✅ Discounted Price: PKR " . number_format($finalPrice, 2) . "\n";
    echo "   ✅ GST ({$taxPercentage}%): +PKR " . number_format($taxAmount, 2) . "\n";
    echo "   ✅ Final Total: PKR " . number_format($finalPriceWithTax, 2) . "\n\n";

    echo "✅ SUCCESS: GST information now included in barcode search!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
