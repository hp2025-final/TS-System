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
        // Get all dresses
        $dresses = \App\Models\Dress::all();
        
        $barcodeCounter = 2500001; // Starting barcode number
        
        foreach ($dresses as $dress) {
            // Create 2 pieces for each dress
            for ($pieceNumber = 1; $pieceNumber <= 2; $pieceNumber++) {
                $dressItem = [
                    'dress_id' => $dress->id,
                    'barcode' => (string)$barcodeCounter,
                    'size_discount_percentage' => rand(0, 15), // Random size discount 0-15%
                    'size_discount_active' => rand(0, 1) == 1, // Random true/false
                    'status' => 'available'
                ];

                \App\Models\DressItem::create($dressItem);
                $barcodeCounter++;
            }
        }
        
        $totalItems = $dresses->count() * 2;
        $this->command->info("Successfully created {$totalItems} dress items (2 pieces per dress)");
    }
}
