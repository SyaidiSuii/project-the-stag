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
        // Change enum to add new station types
        DB::statement("ALTER TABLE kitchen_stations MODIFY COLUMN station_type ENUM('hot_kitchen', 'cold_kitchen', 'drinks', 'desserts', 'grill', 'bakery', 'salad_bar', 'pastry') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE kitchen_stations MODIFY COLUMN station_type ENUM('hot_kitchen', 'cold_kitchen', 'drinks', 'desserts') NOT NULL");
    }
};
