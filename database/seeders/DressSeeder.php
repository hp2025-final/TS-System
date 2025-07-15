<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collections = [
            1 => ['name' => 'Royal Heritage', 'prefix' => 'RH'],
            2 => ['name' => 'Modern Fusion', 'prefix' => 'MF'],
            3 => ['name' => 'Casual Comfort', 'prefix' => 'CC'],
            4 => ['name' => 'Luxury Elite', 'prefix' => 'LE'],
            5 => ['name' => 'Trendy Vibes', 'prefix' => 'TV'],
            6 => ['name' => 'Bridal Dreams', 'prefix' => 'BD'],
            7 => ['name' => 'Office Chic', 'prefix' => 'OC'],
            8 => ['name' => 'Summer Breeze', 'prefix' => 'SB']
        ];

        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'unstitched'];
        
        $dressNames = [
            'Aisha', 'Fatima', 'Khadija', 'Maryam', 'Zainab', 'Ruqayyah', 'Umm Kulthum', 'Safiyyah',
            'Hafsa', 'Juwayriyya', 'Maymunah', 'Zaynab', 'Sawdah', 'Ramlah', 'Asma', 'Ayesha'
        ];

        $hsCodeOptions = [
            '6204.42.00', '6204.43.00', '6204.44.00', '6204.49.00', '6204.52.00',
            '6204.59.00', '6204.61.00', '6204.62.00', '6204.63.00', '6204.64.00',
            '6204.69.00', '6204.31.00', '6204.32.00', '6204.41.00', '6204.51.00'
        ];

        $priceRanges = [
            1 => ['cost' => [4000, 6000], 'sale' => [6000, 9000]], // Royal Heritage
            2 => ['cost' => [3500, 5500], 'sale' => [5500, 8500]], // Modern Fusion
            3 => ['cost' => [2500, 4000], 'sale' => [4000, 6000]], // Casual Comfort
            4 => ['cost' => [7000, 10000], 'sale' => [10000, 15000]], // Luxury Elite
            5 => ['cost' => [3000, 4500], 'sale' => [4500, 7000]], // Trendy Vibes
            6 => ['cost' => [8000, 12000], 'sale' => [12000, 18000]], // Bridal Dreams
            7 => ['cost' => [3500, 5000], 'sale' => [5500, 7500]], // Office Chic
            8 => ['cost' => [2800, 4200], 'sale' => [4200, 6300]] // Summer Breeze
        ];

        foreach ($collections as $collectionId => $collectionData) {
            foreach ($dressNames as $index => $dressName) {
                $dressNumber = $index + 1;
                
                foreach ($sizes as $size) {
                    $sku = strtolower($collectionData['prefix']) . sprintf('%03d', $dressNumber) . strtolower($size);
                    
                    // Generate random price within collection range
                    $costPrice = rand($priceRanges[$collectionId]['cost'][0], $priceRanges[$collectionId]['cost'][1]);
                    $salePrice = rand($priceRanges[$collectionId]['sale'][0], $priceRanges[$collectionId]['sale'][1]);
                    
                    // Random discount (0-20%)
                    $discountPercentage = rand(0, 20);
                    $discountActive = $discountPercentage > 0;
                    
                    $dress = [
                        'collection_id' => $collectionId,
                        'name' => $dressName . ' ' . $size,
                        'sku' => $sku,
                        'description' => $collectionData['name'] . ' collection - ' . $dressName . ' design in ' . $size . ' size',
                        'size' => $size,
                        'hs_code' => $hsCodeOptions[array_rand($hsCodeOptions)],
                        'cost_price' => $costPrice,
                        'sale_price' => $salePrice,
                        'discount_percentage' => $discountPercentage,
                        'discount_active' => $discountActive,
                        'tax_percentage' => 18.00,
                        'status' => 'active'
                    ];

                    \App\Models\Dress::create($dress);
                }
                
                $this->command->info("Created {$dressName} dress in all sizes for {$collectionData['name']} collection");
            }
        }
        
        $this->command->info("Successfully created 896 dresses (8 collections × 16 dresses × 7 sizes)");
    }
}
