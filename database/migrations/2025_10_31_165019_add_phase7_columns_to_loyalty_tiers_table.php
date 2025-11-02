<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PHASE 7: Add required columns for tier system
     * - `order`: Tier hierarchy (1=Bronze, 2=Silver, 3=Gold, 4=Platinum)
     * - `points_threshold`: Points required to reach this tier
     * - `points_multiplier`: Earning multiplier (1.2x - 3.0x)
     */
    public function up(): void
    {
        Schema::table('loyalty_tiers', function (Blueprint $table) {
            // Add 'order' column for tier hierarchy (replaces sort_order for tier logic)
            $table->integer('order')->default(0)->after('id')
                ->comment('Tier hierarchy order (1=lowest, 4=highest)');

            // Add 'points_threshold' for points-based tier qualification
            $table->integer('points_threshold')->default(0)->after('minimum_spending')
                ->comment('Points required to reach this tier');

            // Add 'points_multiplier' for tier earning bonuses
            $table->decimal('points_multiplier', 5, 2)->default(1.00)->after('points_threshold')
                ->comment('Points earning multiplier (e.g., 1.5 = 50% bonus)');

            // Add indexes for better query performance
            $table->index('order');
            $table->index('points_threshold');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loyalty_tiers', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['order']);
            $table->dropIndex(['points_threshold']);

            // Drop columns
            $table->dropColumn(['order', 'points_threshold', 'points_multiplier']);
        });
    }
};
