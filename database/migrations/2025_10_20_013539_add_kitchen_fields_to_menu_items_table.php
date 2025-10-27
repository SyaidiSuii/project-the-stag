<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->enum('station_type', ['hot_kitchen', 'cold_kitchen', 'drinks', 'desserts'])->nullable()->after('category_id');
            $table->decimal('kitchen_load_factor', 3, 2)->nullable()->after('station_type')->comment('Complexity: 0.3-2.0, higher = more complex');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn(['station_type', 'kitchen_load_factor']);
        });
    }
};
