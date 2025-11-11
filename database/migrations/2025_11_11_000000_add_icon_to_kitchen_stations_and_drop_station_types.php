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
        // Add icon column to kitchen_stations
        Schema::table('kitchen_stations', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('station_type')->comment('Station icon/emoji (e.g., 🔥, 🥗, 🍰)');
        });

        // Drop foreign key constraint if it exists
        Schema::table('kitchen_stations', function (Blueprint $table) {
            try {
                $table->dropForeign(['station_type_id']);
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
        });

        // Drop station_type_id column
        Schema::table('kitchen_stations', function (Blueprint $table) {
            if (Schema::hasColumn('kitchen_stations', 'station_type_id')) {
                $table->dropColumn('station_type_id');
            }
        });

        // Drop station_types table
        if (Schema::hasTable('station_types')) {
            Schema::drop('station_types');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a destructive migration; we won't provide a reverse path
        // As this removes the entire station_types system
    }
};
