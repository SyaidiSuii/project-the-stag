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
        // Step 1: Drop the unique constraint first
        try {
            DB::statement("ALTER TABLE kitchen_stations DROP INDEX station_type");
        } catch (\Exception $e) {
            // Constraint might not exist, continue
        }
        
        // Step 2: Convert to VARCHAR to allow data conversion
        DB::statement("ALTER TABLE kitchen_stations MODIFY COLUMN station_type VARCHAR(50) NOT NULL");
        
        // Step 3: Convert existing data to new simplified types
        DB::statement("UPDATE kitchen_stations SET station_type = 'general_kitchen' WHERE station_type IN ('hot_kitchen', 'cold_kitchen', 'grill', 'bakery', 'salad_bar', 'pastry')");
        
        // Step 4: Change to new enum (without unique constraint since we have duplicates now)
        DB::statement("ALTER TABLE kitchen_stations MODIFY COLUMN station_type ENUM('general_kitchen', 'drinks', 'desserts') NOT NULL");
        
        // Note: We DO NOT add back the unique constraint because multiple stations
        // can now have the same type (e.g., multiple 'general_kitchen' stations)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old enum values
        DB::statement("ALTER TABLE kitchen_stations MODIFY COLUMN station_type VARCHAR(50)");
        DB::statement("ALTER TABLE kitchen_stations MODIFY COLUMN station_type ENUM('hot_kitchen', 'cold_kitchen', 'drinks', 'desserts', 'grill', 'bakery', 'salad_bar', 'pastry') NOT NULL");
        
        // Note: Data cannot be perfectly restored as we lost granularity
        // All 'general_kitchen' will revert to 'hot_kitchen'
        DB::statement("UPDATE kitchen_stations SET station_type = 'hot_kitchen' WHERE station_type = 'general_kitchen'");
        
        // Add back unique constraint
        try {
            DB::statement("ALTER TABLE kitchen_stations ADD UNIQUE KEY station_type (station_type)");
        } catch (\Exception $e) {
            // Constraint might already exist
        }
    }
};
