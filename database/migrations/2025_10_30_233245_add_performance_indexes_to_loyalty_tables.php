<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PHASE 1.3: Add Critical Database Indexes
     * These indexes will improve query performance by 10-100x
     */
    public function up(): void
    {
        // Rewards table indexes
        Schema::table('rewards', function (Blueprint $table) {
            $table->index(['is_active', 'points_required'], 'idx_rewards_active_points');
        });

        // Customer rewards indexes
        Schema::table('customer_rewards', function (Blueprint $table) {
            $table->index(['customer_profile_id', 'status'], 'idx_customer_rewards_profile_status');
            $table->index(['expires_at', 'status'], 'idx_customer_rewards_expiry');
        });

        // Customer vouchers indexes
        Schema::table('customer_vouchers', function (Blueprint $table) {
            $table->index(['customer_profile_id', 'status'], 'idx_customer_vouchers_profile_status');
            $table->index(['expiry_date', 'status'], 'idx_customer_vouchers_expiry');
        });

        // Loyalty transactions indexes
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            $table->index(['customer_profile_id', 'created_at'], 'idx_loyalty_transactions_profile_date');
        });

        // Users points balance index (for leaderboards)
        Schema::table('users', function (Blueprint $table) {
            $table->index('points_balance', 'idx_users_points_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop rewards indexes
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropIndex('idx_rewards_active_points');
        });

        // Drop customer rewards indexes
        Schema::table('customer_rewards', function (Blueprint $table) {
            $table->dropIndex('idx_customer_rewards_profile_status');
            $table->dropIndex('idx_customer_rewards_expiry');
        });

        // Drop customer vouchers indexes
        Schema::table('customer_vouchers', function (Blueprint $table) {
            $table->dropIndex('idx_customer_vouchers_profile_status');
            $table->dropIndex('idx_customer_vouchers_expiry');
        });

        // Drop loyalty transactions indexes
        Schema::table('loyalty_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_loyalty_transactions_profile_date');
        });

        // Drop users index
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_points_balance');
        });
    }
};
