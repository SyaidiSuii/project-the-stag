<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * PHASE 2.3: Standardize Loyalty Status Enums
 *
 * Current State Analysis:
 * - customer_rewards.status: enum('active','redeemed','expired')
 * - customer_vouchers.status: enum('active','redeemed','expired')
 * - bonus_point_challenges.status: enum('active','inactive')
 * - VoucherTemplate model references 'used' status (not in DB enum)
 *
 * Issues Found:
 * 1. Inconsistent naming: 'active'/'inactive' vs 'active'/'redeemed'/'expired'
 * 2. VoucherTemplate.hasReachedLimit() checks for 'used' which doesn't exist
 * 3. No 'cancelled' or 'pending' states for workflow management
 *
 * Standardization Plan:
 * 1. customer_rewards: Add 'pending', 'cancelled' for better workflow
 * 2. customer_vouchers: Add 'used' (alias for redeemed), 'cancelled'
 * 3. Keep existing values for backward compatibility
 * 4. Document standard status lifecycle
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Standardize customer_rewards.status
        // Add 'pending' and 'cancelled' for workflow management
        DB::statement("ALTER TABLE customer_rewards MODIFY COLUMN status ENUM('pending', 'active', 'redeemed', 'expired', 'cancelled') DEFAULT 'pending'");

        // 2. Standardize customer_vouchers.status
        // Add 'used' (same as redeemed for clarity) and 'cancelled'
        DB::statement("ALTER TABLE customer_vouchers MODIFY COLUMN status ENUM('active', 'used', 'redeemed', 'expired', 'cancelled') DEFAULT 'active'");

        // Note: 'used' and 'redeemed' are functionally equivalent
        // Both kept for backward compatibility and code clarity
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enums
        DB::statement("ALTER TABLE customer_rewards MODIFY COLUMN status ENUM('active', 'redeemed', 'expired') DEFAULT 'active'");
        DB::statement("ALTER TABLE customer_vouchers MODIFY COLUMN status ENUM('active', 'redeemed', 'expired') DEFAULT 'active'");
    }
};
