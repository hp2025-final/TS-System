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
        $dresses = [
            [
                'collection_id' => 1, // Ghazal
                'name' => 'Naz',
                'sku' => 'gz12345',
                'description' => 'Elegant dress with intricate embroidery',
                'cost_price' => 5000.00,
                'sale_price' => 7000.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 1, // Ghazal
                'name' => 'Zara',
                'sku' => 'gz12346',
                'description' => 'Contemporary design with traditional touch',
                'cost_price' => 4500.00,
                'sale_price' => 6500.00,
                'discount_percentage' => 3.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 2, // Sultana
                'name' => 'Meera',
                'sku' => 'sl12347',
                'description' => 'Royal collection piece',
                'cost_price' => 6000.00,
                'sale_price' => 8500.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 3, // Laila
                'name' => 'Sara',
                'sku' => 'la12348',
                'description' => 'Casual everyday wear',
                'cost_price' => 3000.00,
                'sale_price' => 4500.00,
                'discount_percentage' => 5.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 4, // Rania
                'name' => 'Fatima',
                'sku' => 'rn12349',
                'description' => 'Luxury formal wear with gold work',
                'cost_price' => 7500.00,
                'sale_price' => 11000.00,
                'discount_percentage' => 8.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 4, // Rania
                'name' => 'Khadija',
                'sku' => 'rn12350',
                'description' => 'Premium silk formal dress',
                'cost_price' => 8000.00,
                'sale_price' => 12500.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 5, // Zara
                'name' => 'Aliya',
                'sku' => 'zr12351',
                'description' => 'Modern trendy casual wear',
                'cost_price' => 3500.00,
                'sale_price' => 5200.00,
                'discount_percentage' => 10.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 5, // Zara
                'name' => 'Hiba',
                'sku' => 'zr12352',
                'description' => 'Stylish everyday wear',
                'cost_price' => 2800.00,
                'sale_price' => 4200.00,
                'discount_percentage' => 5.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 6, // Farah
                'name' => 'Layla',
                'sku' => 'fr12353',
                'description' => 'Bridal wear with heavy embellishments',
                'cost_price' => 12000.00,
                'sale_price' => 18000.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 6, // Farah
                'name' => 'Nadia',
                'sku' => 'fr12354',
                'description' => 'Party wear with sequins',
                'cost_price' => 9000.00,
                'sale_price' => 14000.00,
                'discount_percentage' => 7.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 7, // Ayesha
                'name' => 'Rabia',
                'sku' => 'ay12355',
                'description' => 'Comfortable daily wear',
                'cost_price' => 2500.00,
                'sale_price' => 3800.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 7, // Ayesha
                'name' => 'Sana',
                'sku' => 'ay12356',
                'description' => 'Cotton comfort wear',
                'cost_price' => 2200.00,
                'sale_price' => 3500.00,
                'discount_percentage' => 12.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 8, // Mariam
                'name' => 'Amna',
                'sku' => 'mr12357',
                'description' => 'Spring summer lightweight dress',
                'cost_price' => 3200.00,
                'sale_price' => 4800.00,
                'discount_percentage' => 15.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 8, // Mariam
                'name' => 'Bushra',
                'sku' => 'mr12358',
                'description' => 'Vibrant summer collection',
                'cost_price' => 2900.00,
                'sale_price' => 4400.00,
                'discount_percentage' => 10.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ]
        ];

        foreach ($dresses as $dress) {
            $existing = \App\Models\Dress::where('name', $dress['name'])->first();
            if (!$existing) {
                \App\Models\Dress::create($dress);
                $this->command->info("Created dress: {$dress['name']}");
            } else {
                $this->command->info("Dress already exists: {$dress['name']}");
            }
        }
    }
}
