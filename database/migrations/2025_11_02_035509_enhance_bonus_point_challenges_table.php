<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Enhance bonus_point_challenges table with detailed requirements tracking:
     * - condition_type: Type of challenge (orders, spending, visits, etc.)
     * - min_requirement: Minimum value to complete challenge
     * - max_claims_per_user: How many times each user can claim
     * - max_claims_total: Total claims limit across all users
     * - current_claims: Track total claims
     */
    public function up(): void
    {
        Schema::table('bonus_point_challenges', function (Blueprint $table) {
            // Challenge type: orders, spending, visits, checkin_streak, referrals
            $table->enum('condition_type', ['orders', 'spending', 'visits', 'checkin_streak', 'referrals', 'custom'])
                  ->default('orders')
                  ->after('condition');

            // Minimum requirement (e.g., 1 order, RM50 spending, 5 visits)
            $table->integer('min_requirement')->default(1)->after('condition_type');

            // Per-user claim limit (0 = unlimited)
            $table->integer('max_claims_per_user')->default(1)->after('bonus_points');

            // Total claims limit across all users (0 = unlimited)
            $table->integer('max_claims_total')->default(0)->after('max_claims_per_user');

            // Track current total claims
            $table->integer('current_claims')->default(0)->after('max_claims_total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bonus_point_challenges', function (Blueprint $table) {
            $table->dropColumn([
                'condition_type',
                'min_requirement',
                'max_claims_per_user',
                'max_claims_total',
                'current_claims'
            ]);
        });
    }
};
