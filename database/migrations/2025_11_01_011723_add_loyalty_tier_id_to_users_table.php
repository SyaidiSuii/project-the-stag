<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PHASE 7: Add loyalty_tier_id to users table
     *
     * This migration adds the loyalty_tier_id foreign key to users table
     * to track which loyalty tier each customer belongs to.
     *
     * Note: This column should have been added earlier but was missed.
     * Adding it now to complete Phase 7 tier system implementation.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add loyalty_tier_id foreign key
            $table->unsignedBigInteger('loyalty_tier_id')->nullable()->after('points_balance');

            // Foreign key constraint with SET NULL on delete
            // (if tier deleted, user doesn't lose their account, just loses tier assignment)
            $table->foreign('loyalty_tier_id')
                  ->references('id')
                  ->on('loyalty_tiers')
                  ->onDelete('set null');

            // Index for faster queries
            $table->index('loyalty_tier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['loyalty_tier_id']);

            // Drop the column
            $table->dropColumn('loyalty_tier_id');
        });
    }
};
