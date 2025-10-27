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
        Schema::table('categories', function (Blueprint $table) {
            $table->enum('default_station_type', ['hot_kitchen', 'cold_kitchen', 'drinks', 'desserts'])->nullable()->after('parent_id');
            $table->decimal('default_load_factor', 3, 2)->nullable()->default(1.0)->after('default_station_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['default_station_type', 'default_load_factor']);
        });
    }
};
