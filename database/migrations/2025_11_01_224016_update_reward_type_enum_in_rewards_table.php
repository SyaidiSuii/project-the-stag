<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Update reward_type ENUM to include product and discount types,
     * and remove tier_upgrade which is not used.
     *
     * New valid values: voucher, discount, product, points
     */
    public function up(): void
    {
        // MySQL doesn't allow direct ENUM modification, so we need to:
        // 1. Change to VARCHAR temporarily
        // 2. Update any existing 'tier_upgrade' values
        // 3. Change back to ENUM with new values

        DB::statement("ALTER TABLE rewards MODIFY COLUMN reward_type VARCHAR(50)");

        // Update any existing tier_upgrade to points (closest match)
        DB::statement("UPDATE rewards SET reward_type = 'points' WHERE reward_type = 'tier_upgrade'");

        // Now set the new ENUM values
        DB::statement("ALTER TABLE rewards MODIFY COLUMN reward_type ENUM('voucher', 'discount', 'product', 'points') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original ENUM values
        DB::statement("ALTER TABLE rewards MODIFY COLUMN reward_type VARCHAR(50)");

        // Update product and discount back to closest match
        DB::statement("UPDATE rewards SET reward_type = 'points' WHERE reward_type IN ('product', 'discount')");

        // Restore original ENUM
        DB::statement("ALTER TABLE rewards MODIFY COLUMN reward_type ENUM('points', 'voucher', 'tier_upgrade') NOT NULL");
    }
};
