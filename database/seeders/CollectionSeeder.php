<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collections = [
            [
                'name' => 'Ghazal',
                'description' => 'Premium collection with elegant designs',
                'discount_percentage' => 5.00,
                'discount_active' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Sultana',
                'description' => 'Traditional and contemporary mix',
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'status' => 'active'
            ],
            [
                'name' => 'Laila',
                'description' => 'Casual wear collection',
                'discount_percentage' => 10.00,
                'discount_active' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Rania',
                'description' => 'Luxury formal wear collection',
                'discount_percentage' => 15.00,
                'discount_active' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Zara',
                'description' => 'Modern trendy designs',
                'discount_percentage' => 8.00,
                'discount_active' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Farah',
                'description' => 'Bridal and party wear',
                'discount_percentage' => 12.00,
                'discount_active' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Ayesha',
                'description' => 'Everyday comfort wear',
                'discount_percentage' => 3.00,
                'discount_active' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Mariam',
                'description' => 'Spring summer collection',
                'discount_percentage' => 20.00,
                'discount_active' => true,
                'status' => 'active'
            ]
        ];

        foreach ($collections as $collection) {
            $existing = \App\Models\Collection::where('name', $collection['name'])->first();
            if (!$existing) {
                \App\Models\Collection::create($collection);
                $this->command->info("Created collection: {$collection['name']}");
            } else {
                $this->command->info("Collection already exists: {$collection['name']}");
            }
        }
    }
}
