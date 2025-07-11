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
            // Ghazal Collection - Naz XS Dress (ID: 1)
            [
                'dress_id' => 1,
                'barcode' => '2503071',
                'size_discount_percentage' => 2.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            // Ghazal Collection - Naz S Dress (ID: 2)
            [
                'dress_id' => 2,
                'barcode' => '2503072',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Ghazal Collection - Naz M Dress (ID: 3)
            [
                'dress_id' => 3,
                'barcode' => '2503073',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Ghazal Collection - Naz L Dress (ID: 4)
            [
                'dress_id' => 4,
                'barcode' => '2503074',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Ghazal Collection - Zara XS Dress (ID: 5)
            [
                'dress_id' => 5,
                'barcode' => '2503075',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Ghazal Collection - Zara S Dress (ID: 6)
            [
                'dress_id' => 6,
                'barcode' => '2503076',
                'size_discount_percentage' => 1.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            // Ghazal Collection - Zara M Dress (ID: 7)
            [
                'dress_id' => 7,
                'barcode' => '2503077',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Sultana Collection - Meera S Dress (ID: 8)
            [
                'dress_id' => 8,
                'barcode' => '2503078',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Sultana Collection - Meera M Dress (ID: 9)
            [
                'dress_id' => 9,
                'barcode' => '2503079',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Sultana Collection - Meera L Dress (ID: 10)
            [
                'dress_id' => 10,
                'barcode' => '2503080',
                'size_discount_percentage' => 3.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            // Laila Collection - Sara XS Dress (ID: 11)
            [
                'dress_id' => 11,
                'barcode' => '2503081',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Laila Collection - Sara S Dress (ID: 12)
            [
                'dress_id' => 12,
                'barcode' => '2503082',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Laila Collection - Sara M Dress (ID: 13)
            [
                'dress_id' => 13,
                'barcode' => '2503083',
                'size_discount_percentage' => 2.50,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            // Rania Collection - Fatima S Dress (ID: 14)
            [
                'dress_id' => 14,
                'barcode' => '2503084',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Rania Collection - Fatima M Dress (ID: 15)
            [
                'dress_id' => 15,
                'barcode' => '2503085',
                'size_discount_percentage' => 1.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            // Rania Collection - Fatima L Dress (ID: 16)
            [
                'dress_id' => 16,
                'barcode' => '2503086',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Rania Collection - Fatima XL Dress (ID: 17)
            [
                'dress_id' => 17,
                'barcode' => '2503087',
                'size_discount_percentage' => 2.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            // Rania Collection - Khadija XS Dress (ID: 18)
            [
                'dress_id' => 18,
                'barcode' => '2503088',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Rania Collection - Khadija S Dress (ID: 19)
            [
                'dress_id' => 19,
                'barcode' => '2503089',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Rania Collection - Khadija M Dress (ID: 20)
            [
                'dress_id' => 20,
                'barcode' => '2503090',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Zara Collection - Aliya XS Dress (ID: 21)
            [
                'dress_id' => 21,
                'barcode' => '2503091',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Zara Collection - Aliya S Dress (ID: 22)
            [
                'dress_id' => 22,
                'barcode' => '2503092',
                'size_discount_percentage' => 1.50,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            // Zara Collection - Aliya M Dress (ID: 23)
            [
                'dress_id' => 23,
                'barcode' => '2503093',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Zara Collection - Aliya L Dress (ID: 24)
            [
                'dress_id' => 24,
                'barcode' => '2503094',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Zara Collection - Hiba S Dress (ID: 25)
            [
                'dress_id' => 25,
                'barcode' => '2503095',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Zara Collection - Hiba M Dress (ID: 26)
            [
                'dress_id' => 26,
                'barcode' => '2503096',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Zara Collection - Hiba L Dress (ID: 27)
            [
                'dress_id' => 27,
                'barcode' => '2503097',
                'size_discount_percentage' => 3.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            // Farah Collection - Layla S Dress (ID: 28)
            [
                'dress_id' => 28,
                'barcode' => '2503098',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Farah Collection - Layla M Dress (ID: 29)
            [
                'dress_id' => 29,
                'barcode' => '2503099',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Farah Collection - Layla L Dress (ID: 30)
            [
                'dress_id' => 30,
                'barcode' => '2503100',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Farah Collection - Nadia XS Dress (ID: 31)
            [
                'dress_id' => 31,
                'barcode' => '2503101',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Farah Collection - Nadia S Dress (ID: 32)
            [
                'dress_id' => 32,
                'barcode' => '2503102',
                'size_discount_percentage' => 1.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            // Farah Collection - Nadia M Dress (ID: 33)
            [
                'dress_id' => 33,
                'barcode' => '2503103',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Farah Collection - Nadia L Dress (ID: 34)
            [
                'dress_id' => 34,
                'barcode' => '2503104',
                'size_discount_percentage' => 2.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            // Ayesha Collection - Rabia XS Dress (ID: 35)
            [
                'dress_id' => 35,
                'barcode' => '2503105',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Ayesha Collection - Rabia S Dress (ID: 36)
            [
                'dress_id' => 36,
                'barcode' => '2503106',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Ayesha Collection - Rabia M Dress (ID: 37)
            [
                'dress_id' => 37,
                'barcode' => '2503107',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Ayesha Collection - Sana S Dress (ID: 38)
            [
                'dress_id' => 38,
                'barcode' => '2503108',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Ayesha Collection - Sana M Dress (ID: 39)
            [
                'dress_id' => 39,
                'barcode' => '2503109',
                'size_discount_percentage' => 1.50,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            // Ayesha Collection - Sana L Dress (ID: 40)
            [
                'dress_id' => 40,
                'barcode' => '2503110',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Mariam Collection - Amna XS Dress (ID: 41)
            [
                'dress_id' => 41,
                'barcode' => '2503111',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Mariam Collection - Amna S Dress (ID: 42)
            [
                'dress_id' => 42,
                'barcode' => '2503112',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Mariam Collection - Amna M Dress (ID: 43)
            [
                'dress_id' => 43,
                'barcode' => '2503113',
                'size_discount_percentage' => 2.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            // Mariam Collection - Amna L Dress (ID: 44)
            [
                'dress_id' => 44,
                'barcode' => '2503114',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Mariam Collection - Bushra XS Dress (ID: 45)
            [
                'dress_id' => 45,
                'barcode' => '2503115',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Mariam Collection - Bushra S Dress (ID: 46)
            [
                'dress_id' => 46,
                'barcode' => '2503116',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Mariam Collection - Bushra M Dress (ID: 47)
            [
                'dress_id' => 47,
                'barcode' => '2503117',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Mariam Collection - Bushra L Dress (ID: 48)
            [
                'dress_id' => 48,
                'barcode' => '2503118',
                'size_discount_percentage' => 1.00,
                'size_discount_active' => true,
                'status' => 'available'
            ],
            // Ghazal Collection - Naz Unstitched Dress (ID: 49)
            [
                'dress_id' => 49,
                'barcode' => '2503119',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Sultana Collection - Meera Unstitched Dress (ID: 50)
            [
                'dress_id' => 50,
                'barcode' => '2503120',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Laila Collection - Sara Unstitched Dress (ID: 51)
            [
                'dress_id' => 51,
                'barcode' => '2503121',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Rania Collection - Fatima Unstitched Dress (ID: 52)
            [
                'dress_id' => 52,
                'barcode' => '2503122',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ],
            // Zara Collection - Aliya Unstitched Dress (ID: 53)
            [
                'dress_id' => 53,
                'barcode' => '2503123',
                'size_discount_percentage' => 0.00,
                'size_discount_active' => false,
                'status' => 'available'
            ]
        ];

        foreach ($dressItems as $item) {
            $existing = \App\Models\DressItem::where('barcode', $item['barcode'])->first();
            if (!$existing) {
                \App\Models\DressItem::create($item);
                $this->command->info("Created dress item: {$item['barcode']}");
            } else {
                $this->command->info("Dress item already exists: {$item['barcode']}");
            }
        }
    }
}
