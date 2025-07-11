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
                'name' => 'Naz XS',
                'sku' => 'gz12345xs',
                'description' => 'Elegant dress with intricate embroidery - XS Size',
                'size' => 'XS',
                'hs_code' => '6204.42.00',
                'cost_price' => 5000.00,
                'sale_price' => 7000.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 1, // Ghazal
                'name' => 'Naz S',
                'sku' => 'gz12345s',
                'description' => 'Elegant dress with intricate embroidery - S Size',
                'size' => 'S',
                'hs_code' => '6204.42.00',
                'cost_price' => 5000.00,
                'sale_price' => 7000.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 1, // Ghazal
                'name' => 'Naz M',
                'sku' => 'gz12345m',
                'description' => 'Elegant dress with intricate embroidery - M Size',
                'size' => 'M',
                'hs_code' => '6204.42.00',
                'cost_price' => 5000.00,
                'sale_price' => 7000.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 1, // Ghazal
                'name' => 'Naz L',
                'sku' => 'gz12345l',
                'description' => 'Elegant dress with intricate embroidery - L Size',
                'size' => 'L',
                'hs_code' => '6204.42.00',
                'cost_price' => 5000.00,
                'sale_price' => 7000.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 1, // Ghazal
                'name' => 'Zara XS',
                'sku' => 'gz12346xs',
                'description' => 'Contemporary design with traditional touch - XS Size',
                'size' => 'XS',
                'hs_code' => '6204.43.00',
                'cost_price' => 4500.00,
                'sale_price' => 6500.00,
                'discount_percentage' => 3.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 1, // Ghazal
                'name' => 'Zara S',
                'sku' => 'gz12346s',
                'description' => 'Contemporary design with traditional touch - S Size',
                'size' => 'S',
                'hs_code' => '6204.43.00',
                'cost_price' => 4500.00,
                'sale_price' => 6500.00,
                'discount_percentage' => 3.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 1, // Ghazal
                'name' => 'Zara M',
                'sku' => 'gz12346m',
                'description' => 'Contemporary design with traditional touch - M Size',
                'size' => 'M',
                'hs_code' => '6204.43.00',
                'cost_price' => 4500.00,
                'sale_price' => 6500.00,
                'discount_percentage' => 3.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 2, // Sultana
                'name' => 'Meera S',
                'sku' => 'sl12347s',
                'description' => 'Royal collection piece - S Size',
                'size' => 'S',
                'hs_code' => '6204.44.00',
                'cost_price' => 6000.00,
                'sale_price' => 8500.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 2, // Sultana
                'name' => 'Meera M',
                'sku' => 'sl12347m',
                'description' => 'Royal collection piece - M Size',
                'size' => 'M',
                'hs_code' => '6204.44.00',
                'cost_price' => 6000.00,
                'sale_price' => 8500.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 2, // Sultana
                'name' => 'Meera L',
                'sku' => 'sl12347l',
                'description' => 'Royal collection piece - L Size',
                'size' => 'L',
                'hs_code' => '6204.44.00',
                'cost_price' => 6000.00,
                'sale_price' => 8500.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 3, // Laila
                'name' => 'Sara XS',
                'sku' => 'la12348xs',
                'description' => 'Casual everyday wear - XS Size',
                'size' => 'XS',
                'hs_code' => '6204.41.00',
                'cost_price' => 3000.00,
                'sale_price' => 4500.00,
                'discount_percentage' => 5.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 3, // Laila
                'name' => 'Sara S',
                'sku' => 'la12348s',
                'description' => 'Casual everyday wear - S Size',
                'size' => 'S',
                'hs_code' => '6204.41.00',
                'cost_price' => 3000.00,
                'sale_price' => 4500.00,
                'discount_percentage' => 5.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 3, // Laila
                'name' => 'Sara M',
                'sku' => 'la12348m',
                'description' => 'Casual everyday wear - M Size',
                'size' => 'M',
                'hs_code' => '6204.41.00',
                'cost_price' => 3000.00,
                'sale_price' => 4500.00,
                'discount_percentage' => 5.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 4, // Rania
                'name' => 'Fatima S',
                'sku' => 'rn12349s',
                'description' => 'Luxury formal wear with gold work - S Size',
                'size' => 'S',
                'hs_code' => '6204.49.00',
                'cost_price' => 7500.00,
                'sale_price' => 11000.00,
                'discount_percentage' => 8.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 4, // Rania
                'name' => 'Fatima M',
                'sku' => 'rn12349m',
                'description' => 'Luxury formal wear with gold work - M Size',
                'size' => 'M',
                'hs_code' => '6204.49.00',
                'cost_price' => 7500.00,
                'sale_price' => 11000.00,
                'discount_percentage' => 8.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 4, // Rania
                'name' => 'Fatima L',
                'sku' => 'rn12349l',
                'description' => 'Luxury formal wear with gold work - L Size',
                'size' => 'L',
                'hs_code' => '6204.49.00',
                'cost_price' => 7500.00,
                'sale_price' => 11000.00,
                'discount_percentage' => 8.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 4, // Rania
                'name' => 'Fatima XL',
                'sku' => 'rn12349xl',
                'description' => 'Luxury formal wear with gold work - XL Size',
                'size' => 'XL',
                'hs_code' => '6204.49.00',
                'cost_price' => 7500.00,
                'sale_price' => 11000.00,
                'discount_percentage' => 8.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 4, // Rania
                'name' => 'Khadija XS',
                'sku' => 'rn12350xs',
                'description' => 'Premium silk formal dress - XS Size',
                'size' => 'XS',
                'hs_code' => '6204.52.00',
                'cost_price' => 8000.00,
                'sale_price' => 12500.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 4, // Rania
                'name' => 'Khadija S',
                'sku' => 'rn12350s',
                'description' => 'Premium silk formal dress - S Size',
                'size' => 'S',
                'hs_code' => '6204.52.00',
                'cost_price' => 8000.00,
                'sale_price' => 12500.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 4, // Rania
                'name' => 'Khadija M',
                'sku' => 'rn12350m',
                'description' => 'Premium silk formal dress - M Size',
                'size' => 'M',
                'hs_code' => '6204.52.00',
                'cost_price' => 8000.00,
                'sale_price' => 12500.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 5, // Zara
                'name' => 'Aliya XS',
                'sku' => 'zr12351xs',
                'description' => 'Modern trendy casual wear - XS Size',
                'size' => 'XS',
                'hs_code' => '6204.61.00',
                'cost_price' => 3500.00,
                'sale_price' => 5200.00,
                'discount_percentage' => 10.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 5, // Zara
                'name' => 'Aliya S',
                'sku' => 'zr12351s',
                'description' => 'Modern trendy casual wear - S Size',
                'size' => 'S',
                'hs_code' => '6204.61.00',
                'cost_price' => 3500.00,
                'sale_price' => 5200.00,
                'discount_percentage' => 10.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 5, // Zara
                'name' => 'Aliya M',
                'sku' => 'zr12351m',
                'description' => 'Modern trendy casual wear - M Size',
                'size' => 'M',
                'hs_code' => '6204.61.00',
                'cost_price' => 3500.00,
                'sale_price' => 5200.00,
                'discount_percentage' => 10.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 5, // Zara
                'name' => 'Aliya L',
                'sku' => 'zr12351l',
                'description' => 'Modern trendy casual wear - L Size',
                'size' => 'L',
                'hs_code' => '6204.61.00',
                'cost_price' => 3500.00,
                'sale_price' => 5200.00,
                'discount_percentage' => 10.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 5, // Zara
                'name' => 'Hiba S',
                'sku' => 'zr12352s',
                'description' => 'Stylish everyday wear - S Size',
                'size' => 'S',
                'hs_code' => '6204.62.00',
                'cost_price' => 2800.00,
                'sale_price' => 4200.00,
                'discount_percentage' => 5.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 5, // Zara
                'name' => 'Hiba M',
                'sku' => 'zr12352m',
                'description' => 'Stylish everyday wear - M Size',
                'size' => 'M',
                'hs_code' => '6204.62.00',
                'cost_price' => 2800.00,
                'sale_price' => 4200.00,
                'discount_percentage' => 5.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 5, // Zara
                'name' => 'Hiba L',
                'sku' => 'zr12352l',
                'description' => 'Stylish everyday wear - L Size',
                'size' => 'L',
                'hs_code' => '6204.62.00',
                'cost_price' => 2800.00,
                'sale_price' => 4200.00,
                'discount_percentage' => 5.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 6, // Farah
                'name' => 'Layla S',
                'sku' => 'fr12353s',
                'description' => 'Bridal wear with heavy embellishments - S Size',
                'size' => 'S',
                'hs_code' => '6204.63.00',
                'cost_price' => 12000.00,
                'sale_price' => 18000.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 6, // Farah
                'name' => 'Layla M',
                'sku' => 'fr12353m',
                'description' => 'Bridal wear with heavy embellishments - M Size',
                'size' => 'M',
                'hs_code' => '6204.63.00',
                'cost_price' => 12000.00,
                'sale_price' => 18000.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 6, // Farah
                'name' => 'Layla L',
                'sku' => 'fr12353l',
                'description' => 'Bridal wear with heavy embellishments - L Size',
                'size' => 'L',
                'hs_code' => '6204.63.00',
                'cost_price' => 12000.00,
                'sale_price' => 18000.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 6, // Farah
                'name' => 'Nadia XS',
                'sku' => 'fr12354xs',
                'description' => 'Party wear with sequins - XS Size',
                'size' => 'XS',
                'hs_code' => '6204.64.00',
                'cost_price' => 9000.00,
                'sale_price' => 14000.00,
                'discount_percentage' => 7.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 6, // Farah
                'name' => 'Nadia S',
                'sku' => 'fr12354s',
                'description' => 'Party wear with sequins - S Size',
                'size' => 'S',
                'hs_code' => '6204.64.00',
                'cost_price' => 9000.00,
                'sale_price' => 14000.00,
                'discount_percentage' => 7.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 6, // Farah
                'name' => 'Nadia M',
                'sku' => 'fr12354m',
                'description' => 'Party wear with sequins - M Size',
                'size' => 'M',
                'hs_code' => '6204.64.00',
                'cost_price' => 9000.00,
                'sale_price' => 14000.00,
                'discount_percentage' => 7.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 6, // Farah
                'name' => 'Nadia L',
                'sku' => 'fr12354l',
                'description' => 'Party wear with sequins - L Size',
                'size' => 'L',
                'hs_code' => '6204.64.00',
                'cost_price' => 9000.00,
                'sale_price' => 14000.00,
                'discount_percentage' => 7.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 7, // Ayesha
                'name' => 'Rabia XS',
                'sku' => 'ay12355xs',
                'description' => 'Comfortable daily wear - XS Size',
                'size' => 'XS',
                'hs_code' => '6204.69.00',
                'cost_price' => 2500.00,
                'sale_price' => 3800.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 7, // Ayesha
                'name' => 'Rabia S',
                'sku' => 'ay12355s',
                'description' => 'Comfortable daily wear - S Size',
                'size' => 'S',
                'hs_code' => '6204.69.00',
                'cost_price' => 2500.00,
                'sale_price' => 3800.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 7, // Ayesha
                'name' => 'Rabia M',
                'sku' => 'ay12355m',
                'description' => 'Comfortable daily wear - M Size',
                'size' => 'M',
                'hs_code' => '6204.69.00',
                'cost_price' => 2500.00,
                'sale_price' => 3800.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 7, // Ayesha
                'name' => 'Sana S',
                'sku' => 'ay12356s',
                'description' => 'Cotton comfort wear - S Size',
                'size' => 'S',
                'hs_code' => '6204.59.00',
                'cost_price' => 2200.00,
                'sale_price' => 3500.00,
                'discount_percentage' => 12.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 7, // Ayesha
                'name' => 'Sana M',
                'sku' => 'ay12356m',
                'description' => 'Cotton comfort wear - M Size',
                'size' => 'M',
                'hs_code' => '6204.59.00',
                'cost_price' => 2200.00,
                'sale_price' => 3500.00,
                'discount_percentage' => 12.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 7, // Ayesha
                'name' => 'Sana L',
                'sku' => 'ay12356l',
                'description' => 'Cotton comfort wear - L Size',
                'size' => 'L',
                'hs_code' => '6204.59.00',
                'cost_price' => 2200.00,
                'sale_price' => 3500.00,
                'discount_percentage' => 12.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 8, // Mariam
                'name' => 'Amna XS',
                'sku' => 'mr12357xs',
                'description' => 'Spring summer lightweight dress - XS Size',
                'size' => 'XS',
                'hs_code' => '6204.31.00',
                'cost_price' => 3200.00,
                'sale_price' => 4800.00,
                'discount_percentage' => 15.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 8, // Mariam
                'name' => 'Amna S',
                'sku' => 'mr12357s',
                'description' => 'Spring summer lightweight dress - S Size',
                'size' => 'S',
                'hs_code' => '6204.31.00',
                'cost_price' => 3200.00,
                'sale_price' => 4800.00,
                'discount_percentage' => 15.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 8, // Mariam
                'name' => 'Amna M',
                'sku' => 'mr12357m',
                'description' => 'Spring summer lightweight dress - M Size',
                'size' => 'M',
                'hs_code' => '6204.31.00',
                'cost_price' => 3200.00,
                'sale_price' => 4800.00,
                'discount_percentage' => 15.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 8, // Mariam
                'name' => 'Amna L',
                'sku' => 'mr12357l',
                'description' => 'Spring summer lightweight dress - L Size',
                'size' => 'L',
                'hs_code' => '6204.31.00',
                'cost_price' => 3200.00,
                'sale_price' => 4800.00,
                'discount_percentage' => 15.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 8, // Mariam
                'name' => 'Bushra XS',
                'sku' => 'mr12358xs',
                'description' => 'Vibrant summer collection - XS Size',
                'size' => 'XS',
                'hs_code' => '6204.32.00',
                'cost_price' => 2900.00,
                'sale_price' => 4400.00,
                'discount_percentage' => 10.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 8, // Mariam
                'name' => 'Bushra S',
                'sku' => 'mr12358s',
                'description' => 'Vibrant summer collection - S Size',
                'size' => 'S',
                'hs_code' => '6204.32.00',
                'cost_price' => 2900.00,
                'sale_price' => 4400.00,
                'discount_percentage' => 10.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 8, // Mariam
                'name' => 'Bushra M',
                'sku' => 'mr12358m',
                'description' => 'Vibrant summer collection - M Size',
                'size' => 'M',
                'hs_code' => '6204.32.00',
                'cost_price' => 2900.00,
                'sale_price' => 4400.00,
                'discount_percentage' => 10.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 8, // Mariam
                'name' => 'Bushra L',
                'sku' => 'mr12358l',
                'description' => 'Vibrant summer collection - L Size',
                'size' => 'L',
                'hs_code' => '6204.32.00',
                'cost_price' => 2900.00,
                'sale_price' => 4400.00,
                'discount_percentage' => 10.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            // Unstitched dresses
            [
                'collection_id' => 1, // Ghazal
                'name' => 'Naz Unstitched',
                'sku' => 'gz12345un',
                'description' => 'Elegant unstitched fabric with intricate embroidery',
                'size' => 'unstitched',
                'hs_code' => '6204.42.00',
                'cost_price' => 4000.00,
                'sale_price' => 6000.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 2, // Sultana
                'name' => 'Meera Unstitched',
                'sku' => 'sl12347un',
                'description' => 'Royal collection unstitched fabric',
                'size' => 'unstitched',
                'hs_code' => '6204.44.00',
                'cost_price' => 5500.00,
                'sale_price' => 7500.00,
                'discount_percentage' => 0.00,
                'discount_active' => false,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 3, // Laila
                'name' => 'Sara Unstitched',
                'sku' => 'la12348un',
                'description' => 'Casual unstitched fabric',
                'size' => 'unstitched',
                'hs_code' => '6204.41.00',
                'cost_price' => 2500.00,
                'sale_price' => 3800.00,
                'discount_percentage' => 5.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 4, // Rania
                'name' => 'Fatima Unstitched',
                'sku' => 'rn12349un',
                'description' => 'Luxury unstitched fabric with gold work',
                'size' => 'unstitched',
                'hs_code' => '6204.49.00',
                'cost_price' => 7000.00,
                'sale_price' => 10000.00,
                'discount_percentage' => 8.00,
                'discount_active' => true,
                'tax_percentage' => 18.00,
                'status' => 'active'
            ],
            [
                'collection_id' => 5, // Zara
                'name' => 'Aliya Unstitched',
                'sku' => 'zr12351un',
                'description' => 'Modern trendy unstitched fabric',
                'size' => 'unstitched',
                'hs_code' => '6204.61.00',
                'cost_price' => 3000.00,
                'sale_price' => 4500.00,
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
