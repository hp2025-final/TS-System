<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DressItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dressItems = [
            // Ghazal Collection - Naz Dress (ID: 1)
            [
                'dress_id' => 1,
                'barcode' => '2503071',
                'size' => 'XS',
                'size_discount_percentage' => 2.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            [
                'dress_id' => 1,
                'barcode' => '2503072',
                'size' => 'S',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 1,
                'barcode' => '2503073',
                'size' => 'M',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 1,
                'barcode' => '2503074',
                'size' => 'L',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            
            // Ghazal Collection - Zara Dress (ID: 2)
            [
                'dress_id' => 2,
                'barcode' => '2503075',
                'size' => 'XS',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 2,
                'barcode' => '2503076',
                'size' => 'S',
                'size_discount_percentage' => 1.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            [
                'dress_id' => 2,
                'barcode' => '2503077',
                'size' => 'M',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            
            // Sultana Collection - Meera Dress (ID: 3)
            [
                'dress_id' => 3,
                'barcode' => '2503078',
                'size' => 'S',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 3,
                'barcode' => '2503079',
                'size' => 'M',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 3,
                'barcode' => '2503080',
                'size' => 'L',
                'size_discount_percentage' => 3.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            
            // Laila Collection - Sara Dress (ID: 4)
            [
                'dress_id' => 4,
                'barcode' => '2503081',
                'size' => 'XS',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 4,
                'barcode' => '2503082',
                'size' => 'S',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 4,
                'barcode' => '2503083',
                'size' => 'M',
                'size_discount_percentage' => 2.50,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            
            // Rania Collection - Fatima Dress (ID: 5)
            [
                'dress_id' => 5,
                'barcode' => '2503084',
                'size' => 'S',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 5,
                'barcode' => '2503085',
                'size' => 'M',
                'size_discount_percentage' => 1.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            [
                'dress_id' => 5,
                'barcode' => '2503086',
                'size' => 'L',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 5,
                'barcode' => '2503087',
                'size' => 'XL',
                'size_discount_percentage' => 2.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            
            // Rania Collection - Khadija Dress (ID: 6)
            [
                'dress_id' => 6,
                'barcode' => '2503088',
                'size' => 'XS',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 6,
                'barcode' => '2503089',
                'size' => 'S',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 6,
                'barcode' => '2503090',
                'size' => 'M',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            
            // Zara Collection - Aliya Dress (ID: 7)
            [
                'dress_id' => 7,
                'barcode' => '2503091',
                'size' => 'XS',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 7,
                'barcode' => '2503092',
                'size' => 'S',
                'size_discount_percentage' => 1.50,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            [
                'dress_id' => 7,
                'barcode' => '2503093',
                'size' => 'M',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 7,
                'barcode' => '2503094',
                'size' => 'L',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            
            // Zara Collection - Hiba Dress (ID: 8)
            [
                'dress_id' => 8,
                'barcode' => '2503095',
                'size' => 'S',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 8,
                'barcode' => '2503096',
                'size' => 'M',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 8,
                'barcode' => '2503097',
                'size' => 'L',
                'size_discount_percentage' => 3.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            
            // Farah Collection - Layla Dress (ID: 9)
            [
                'dress_id' => 9,
                'barcode' => '2503098',
                'size' => 'S',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 9,
                'barcode' => '2503099',
                'size' => 'M',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 9,
                'barcode' => '2503100',
                'size' => 'L',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            
            // Farah Collection - Nadia Dress (ID: 10)
            [
                'dress_id' => 10,
                'barcode' => '2503101',
                'size' => 'XS',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 10,
                'barcode' => '2503102',
                'size' => 'S',
                'size_discount_percentage' => 1.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            [
                'dress_id' => 10,
                'barcode' => '2503103',
                'size' => 'M',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 10,
                'barcode' => '2503104',
                'size' => 'L',
                'size_discount_percentage' => 2.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            
            // Ayesha Collection - Rabia Dress (ID: 11)
            [
                'dress_id' => 11,
                'barcode' => '2503105',
                'size' => 'XS',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 11,
                'barcode' => '2503106',
                'size' => 'S',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 11,
                'barcode' => '2503107',
                'size' => 'M',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            
            // Ayesha Collection - Sana Dress (ID: 12)
            [
                'dress_id' => 12,
                'barcode' => '2503108',
                'size' => 'S',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 12,
                'barcode' => '2503109',
                'size' => 'M',
                'size_discount_percentage' => 1.50,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            [
                'dress_id' => 12,
                'barcode' => '2503110',
                'size' => 'L',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            
            // Mariam Collection - Amna Dress (ID: 13)
            [
                'dress_id' => 13,
                'barcode' => '2503111',
                'size' => 'XS',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 13,
                'barcode' => '2503112',
                'size' => 'S',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 13,
                'barcode' => '2503113',
                'size' => 'M',
                'size_discount_percentage' => 2.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            [
                'dress_id' => 13,
                'barcode' => '2503114',
                'size' => 'L',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            
            // Mariam Collection - Bushra Dress (ID: 14)
            [
                'dress_id' => 14,
                'barcode' => '2503115',
                'size' => 'XS',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 14,
                'barcode' => '2503116',
                'size' => 'S',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 14,
                'barcode' => '2503117',
                'size' => 'M',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            [
                'dress_id' => 14,
                'barcode' => '2503118',
                'size' => 'L',
                'size_discount_percentage' => 1.00,
                'size_discount_active' => true,
                'status' => 'available'
            ]
        ];

        foreach ($dressItems as $item) {
            $existing = \App\Models\DressItem::where('barcode', $item['barcode'])->first();
            if (!$existing) {
                \App\Models\DressItem::create($item);
                $this->command->info("Created dress item: {$item['barcode']} - Size {$item['size']}");
            } else {
                $this->command->info("Dress item already exists: {$item['barcode']}");
            }
        }
    }
}
