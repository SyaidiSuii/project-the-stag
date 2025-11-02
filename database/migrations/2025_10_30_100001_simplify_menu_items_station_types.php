<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Convert columns to VARCHAR first
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN station_type VARCHAR(50) NULL");
        DB::statement("ALTER TABLE categories MODIFY COLUMN default_station_type VARCHAR(50) NULL");
        
        // Step 2: Convert existing menu items to new simplified types
        DB::statement("UPDATE menu_items SET station_type = 'general_kitchen' WHERE station_type IN ('hot_kitchen', 'cold_kitchen', 'grill', 'bakery', 'salad_bar', 'pastry')");
        
        // Step 3: Update categories default_station_type
        DB::statement("UPDATE categories SET default_station_type = 'general_kitchen' WHERE default_station_type IN ('hot_kitchen', 'cold_kitchen', 'grill', 'bakery', 'salad_bar', 'pastry')");
        
        // Step 4: Change to new enum
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN station_type ENUM('general_kitchen', 'drinks', 'desserts') NULL");
        DB::statement("ALTER TABLE categories MODIFY COLUMN default_station_type ENUM('general_kitchen', 'drinks', 'desserts') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert menu_items
        DB::statement("ALTER TABLE menu_items MODIFY COLUMN station_type ENUM('hot_kitchen', 'cold_kitchen', 'drinks', 'desserts', 'grill', 'bakery', 'salad_bar', 'pastry') NULL");
        
        // Revert categories
        DB::statement("ALTER TABLE categories MODIFY COLUMN default_station_type ENUM('hot_kitchen', 'cold_kitchen', 'drinks', 'desserts', 'grill', 'bakery', 'salad_bar', 'pastry') NULL");
        
        // Note: Data conversion is lossy - all 'general_kitchen' reverts to 'hot_kitchen'
        DB::statement("UPDATE menu_items SET station_type = 'hot_kitchen' WHERE station_type = 'general_kitchen'");
        DB::statement("UPDATE categories SET default_station_type = 'hot_kitchen' WHERE default_station_type = 'general_kitchen'");
    }
};
