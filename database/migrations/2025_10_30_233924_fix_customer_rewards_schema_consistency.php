<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PHASE 1.4: Fix Customer Rewards Schema Consistency
     * - Remove legacy expiry_date (DATE) field
     * - Keep only expires_at (TIMESTAMP) field for consistency
     * - Model and queries already use expires_at
     */
    public function up(): void
    {
        Schema::table('customer_rewards', function (Blueprint $table) {
            // Remove legacy expiry_date field
            $table->dropColumn('expiry_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_rewards', function (Blueprint $table) {
            // Restore expiry_date field if rolled back
            $table->date('expiry_date')->nullable()->after('status');
        });
    }
};
