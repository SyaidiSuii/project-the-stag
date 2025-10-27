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
            $table->foreignId('station_override_id')
                ->nullable()
                ->after('preparation_time')
                ->constrained('kitchen_stations')
                ->onDelete('set null')
                ->comment('Override station assignment (if different from category default)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropForeign(['station_override_id']);
            $table->dropColumn('station_override_id');
        });
    }
};
