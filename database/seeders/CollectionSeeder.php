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
                'name' => 'Royal Heritage',
                'description' => 'Premium collection with elegant traditional designs',
                'discount_percentage' => 5.00,
                'discount_active' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Modern Fusion',
                'description' => 'Contemporary and traditional mix for modern women',
                'discount_percentage' => 8.00,
                'discount_active' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Casual Comfort',
                'description' => 'Comfortable daily wear collection',
                'discount_percentage' => 10.00,
                'discount_active' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Luxury Elite',
                'description' => 'Luxury formal wear collection with premium fabrics',
                'discount_percentage' => 15.00,
                'discount_active' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Trendy Vibes',
                'description' => 'Modern trendy designs for fashion forward women',
                'discount_percentage' => 12.00,
                'discount_active' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Bridal Dreams',
                'description' => 'Bridal and party wear with heavy embellishments',
                'discount_percentage' => 7.00,
                'discount_active' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Office Chic',
                'description' => 'Professional wear for working women',
                'discount_percentage' => 6.00,
                'discount_active' => true,
                'status' => 'active'
            ],
            [
                'name' => 'Summer Breeze',
                'description' => 'Light and airy collection for summer season',
                'discount_percentage' => 18.00,
                'discount_active' => true,
                'status' => 'active'
            ]
        ];

        foreach ($collections as $collection) {
            \App\Models\Collection::create($collection);
            $this->command->info("Created collection: {$collection['name']}");
        }
    }
}
