<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * PHASE 2.4: Clean Up Redundant Loyalty Fields
 *
 * Redundancies Identified:
 * 1. rewards.name - Nullable, unused, duplicates 'title'
 * 2. customer_vouchers.redeemed_at vs used_at - Both track same timestamp
 * 3. customer_vouchers.source - Redundant with voucher_templates.source_type
 *
 * Actions:
 * 1. Migrate rewards.name data to title (if name exists but title doesn't)
 * 2. Drop rewards.name column
 * 3. Migrate customer_vouchers.redeemed_at to used_at for consistency
 * 4. Drop customer_vouchers.redeemed_at
 * 5. Keep customer_vouchers.source for now (used in existing code, will deprecate in Phase 3)
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Migrate rewards.name to title where title is null but name exists
        DB::statement("
            UPDATE rewards
            SET title = name
            WHERE title IS NULL AND name IS NOT NULL
        ");

        // 2. Drop rewards.name column (now redundant)
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn('name');
        });

        // 3. Migrate customer_vouchers.redeemed_at to used_at for consistency
        // Copy redeemed_at to used_at where used_at is null
        DB::statement("
            UPDATE customer_vouchers
            SET used_at = redeemed_at
            WHERE redeemed_at IS NOT NULL AND used_at IS NULL
        ");

        // 4. Drop customer_vouchers.redeemed_at (use used_at instead)
        Schema::table('customer_vouchers', function (Blueprint $table) {
            $table->dropColumn('redeemed_at');
        });

        // Note: customer_vouchers.source kept for backward compatibility (Phase 3 will handle)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore rewards.name column
        Schema::table('rewards', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });

        // Restore customer_vouchers.redeemed_at column
        Schema::table('customer_vouchers', function (Blueprint $table) {
            $table->timestamp('redeemed_at')->nullable()->after('expiry_date');
        });

        // Copy used_at back to redeemed_at
        DB::statement("
            UPDATE customer_vouchers
            SET redeemed_at = used_at
            WHERE used_at IS NOT NULL
        ");
    }
};
