<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PHASE 1.1: Consolidate Points Storage
     * Remove redundant loyalty_points column from customer_profiles table.
     * Single source of truth is users.points_balance.
     */
    public function up(): void
    {
        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->dropColumn('loyalty_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_profiles', function (Blueprint $table) {
            $table->integer('loyalty_points')->default(0);
        });
    }
};
