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
            $table->foreignId('default_station_id')
                ->nullable()
                ->after('default_load_factor')
                ->constrained('kitchen_stations')
                ->onDelete('set null')
                ->comment('Default kitchen station for items in this category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['default_station_id']);
            $table->dropColumn('default_station_id');
        });
    }
};
