<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * PHASE 2.2: Drop Redundant Exchange Points Tables
 *
 * Analysis shows exchange_points and exchange_point_redemptions are 100% redundant
 * with the existing rewards and customer_rewards tables.
 *
 * Similarities:
 * - exchange_points.points_required ≈ rewards.points_required
 * - exchange_points.reward_type ≈ rewards.reward_type
 * - exchange_points.reward_value ≈ rewards.reward_value (now in rewards table)
 * - exchange_points.redemption_method ≈ rewards.redemption_method (now in rewards table)
 * - exchange_point_redemptions ≈ customer_rewards
 *
 * Current State:
 * - 0 records in both tables
 * - No models created
 * - Minimal usage in views only
 *
 * Decision: Remove completely as rewards table handles all functionality
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop exchange_point_redemptions first (has foreign key to exchange_points)
        Schema::dropIfExists('exchange_point_redemptions');

        // Then drop exchange_points
        Schema::dropIfExists('exchange_points');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate exchange_points table
        Schema::create('exchange_points', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('points_required');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('reward_type')->nullable();
            $table->decimal('reward_value', 10, 2)->nullable();
            $table->integer('validity_days')->nullable();
            $table->integer('usage_limit')->nullable();
            $table->decimal('minimum_order', 10, 2)->nullable();
            $table->enum('redemption_method', [
                'show_to_staff',
                'qr_code_scan',
                'auto_applied',
                'bring_voucher',
                'phone_verification'
            ])->nullable();
            $table->boolean('transferable')->default(false);
            $table->timestamps();
        });

        // Recreate exchange_point_redemptions table
        Schema::create('exchange_point_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('exchange_point_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'redeemed', 'expired', 'cancelled'])->default('pending');
            $table->timestamp('redeemed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
};
