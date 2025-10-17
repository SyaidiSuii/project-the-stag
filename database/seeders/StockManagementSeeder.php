<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StockManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Suppliers
        $supplier1 = \App\Models\Supplier::create([
            'name' => 'Fresh Foods Malaysia',
            'contact_person' => 'Ahmad bin Ali',
            'phone' => '012-3456789',
            'email' => 'ahmad@freshfoods.com.my',
            'address' => 'No 123, Jalan Merdeka, Kuala Lumpur',
            'payment_terms' => 'Net 30',
            'is_active' => true,
        ]);

        $supplier2 = \App\Models\Supplier::create([
            'name' => 'Seafood Delights Sdn Bhd',
            'contact_person' => 'Lee Mei Ling',
            'phone' => '016-7890123',
            'email' => 'meiling@seafooddelights.com',
            'address' => 'No 456, Jalan Pantai, Penang',
            'payment_terms' => 'COD',
            'is_active' => true,
        ]);

        $supplier3 = \App\Models\Supplier::create([
            'name' => 'Spice Kingdom',
            'contact_person' => 'Kumar s/o Raman',
            'phone' => '019-2345678',
            'email' => 'kumar@spicekingdom.com',
            'address' => 'No 789, Jalan Rempah, Johor Bahru',
            'payment_terms' => 'Net 15',
            'is_active' => true,
        ]);

        // Create Stock Items with varying stock levels

        // LOW STOCK - Will trigger auto-reorder
        \App\Models\StockItem::create([
            'name' => 'White Rice',
            'sku' => 'RICE-001',
            'description' => 'Premium Thai white rice',
            'category' => 'Grains',
            'unit_of_measure' => 'kg',
            'current_quantity' => 15.00, // LOW - below reorder point
            'minimum_threshold' => 10.00,
            'reorder_point' => 20.00,
            'reorder_quantity' => 50.00,
            'unit_price' => 4.50,
            'supplier_id' => $supplier1->id,
            'storage_location' => 'Dry Storage A1',
            'is_active' => true,
        ]);

        // CRITICAL STOCK - Urgent reorder
        \App\Models\StockItem::create([
            'name' => 'Fresh Chicken Breast',
            'sku' => 'CHKN-001',
            'description' => 'Fresh boneless chicken breast',
            'category' => 'Meat',
            'unit_of_measure' => 'kg',
            'current_quantity' => 3.00, // CRITICAL - below minimum
            'minimum_threshold' => 5.00,
            'reorder_point' => 10.00,
            'reorder_quantity' => 25.00,
            'unit_price' => 12.00,
            'supplier_id' => $supplier1->id,
            'storage_location' => 'Cold Storage B1',
            'is_active' => true,
        ]);

        // GOOD STOCK - No reorder needed
        \App\Models\StockItem::create([
            'name' => 'Cooking Oil',
            'sku' => 'OIL-001',
            'description' => 'Pure palm cooking oil',
            'category' => 'Oils',
            'unit_of_measure' => 'liters',
            'current_quantity' => 45.00, // GOOD STOCK
            'minimum_threshold' => 10.00,
            'reorder_point' => 20.00,
            'reorder_quantity' => 40.00,
            'unit_price' => 8.50,
            'supplier_id' => $supplier1->id,
            'storage_location' => 'Dry Storage A2',
            'is_active' => true,
        ]);

        // LOW STOCK - Seafood
        \App\Models\StockItem::create([
            'name' => 'Fresh Prawns',
            'sku' => 'PRWN-001',
            'description' => 'Large fresh prawns',
            'category' => 'Seafood',
            'unit_of_measure' => 'kg',
            'current_quantity' => 4.00, // LOW
            'minimum_threshold' => 3.00,
            'reorder_point' => 8.00,
            'reorder_quantity' => 15.00,
            'unit_price' => 35.00,
            'supplier_id' => $supplier2->id,
            'storage_location' => 'Cold Storage B2',
            'is_active' => true,
        ]);

        // CRITICAL STOCK - Spices
        \App\Models\StockItem::create([
            'name' => 'Black Pepper',
            'sku' => 'SPICE-001',
            'description' => 'Premium black pepper powder',
            'category' => 'Spices',
            'unit_of_measure' => 'kg',
            'current_quantity' => 0.50, // CRITICAL
            'minimum_threshold' => 1.00,
            'reorder_point' => 2.00,
            'reorder_quantity' => 5.00,
            'unit_price' => 45.00,
            'supplier_id' => $supplier3->id,
            'storage_location' => 'Dry Storage A3',
            'is_active' => true,
        ]);

        // LOW STOCK - Vegetables
        \App\Models\StockItem::create([
            'name' => 'Tomatoes',
            'sku' => 'VEG-001',
            'description' => 'Fresh tomatoes',
            'category' => 'Vegetables',
            'unit_of_measure' => 'kg',
            'current_quantity' => 6.00, // LOW
            'minimum_threshold' => 5.00,
            'reorder_point' => 10.00,
            'reorder_quantity' => 20.00,
            'unit_price' => 5.00,
            'supplier_id' => $supplier1->id,
            'storage_location' => 'Cold Storage B3',
            'is_active' => true,
        ]);

        // GOOD STOCK - Dairy
        \App\Models\StockItem::create([
            'name' => 'Fresh Milk',
            'sku' => 'DAIRY-001',
            'description' => 'Fresh full cream milk',
            'category' => 'Dairy',
            'unit_of_measure' => 'liters',
            'current_quantity' => 30.00, // GOOD
            'minimum_threshold' => 10.00,
            'reorder_point' => 15.00,
            'reorder_quantity' => 40.00,
            'unit_price' => 6.00,
            'supplier_id' => $supplier1->id,
            'storage_location' => 'Cold Storage B4',
            'is_active' => true,
        ]);

        // CRITICAL STOCK - Sauces
        \App\Models\StockItem::create([
            'name' => 'Soy Sauce',
            'sku' => 'SAUCE-001',
            'description' => 'Premium dark soy sauce',
            'category' => 'Sauces',
            'unit_of_measure' => 'liters',
            'current_quantity' => 1.50, // CRITICAL
            'minimum_threshold' => 2.00,
            'reorder_point' => 5.00,
            'reorder_quantity' => 10.00,
            'unit_price' => 12.00,
            'supplier_id' => $supplier3->id,
            'storage_location' => 'Dry Storage A4',
            'is_active' => true,
        ]);

        $this->command->info('✅ Stock Management test data created successfully!');
        $this->command->info('   - 3 Suppliers');
        $this->command->info('   - 8 Stock Items (3 Critical, 3 Low, 2 Good)');
    }
}
