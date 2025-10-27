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
        Schema::table('kitchen_stations', function (Blueprint $table) {
            // Add new foreign key column
            $table->foreignId('station_type_id')->nullable()->after('name')->constrained('station_types')->onDelete('restrict');
        });

        // Drop the unique constraint on station_type enum
        DB::statement('ALTER TABLE kitchen_stations DROP INDEX kitchen_stations_station_type_unique');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kitchen_stations', function (Blueprint $table) {
            $table->dropForeign(['station_type_id']);
            $table->dropColumn('station_type_id');
        });

        // Re-add unique constraint
        DB::statement('ALTER TABLE kitchen_stations ADD UNIQUE KEY kitchen_stations_station_type_unique (station_type)');
    }
};
