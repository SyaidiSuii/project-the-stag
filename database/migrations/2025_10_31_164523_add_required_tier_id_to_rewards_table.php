<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PHASE 7: Add tier-exclusive rewards support
     * Adds required_tier_id to enable tier-exclusive rewards
     * (e.g., Gold-only rewards, Platinum-only perks)
     */
    public function up(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            // Add required_tier_id column for tier-exclusive rewards
            $table->unsignedBigInteger('required_tier_id')->nullable()->after('voucher_template_id')
                ->comment('Minimum tier required to redeem this reward');

            // Foreign key to loyalty_tiers table
            $table->foreign('required_tier_id')
                ->references('id')
                ->on('loyalty_tiers')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Add index for better query performance
            $table->index('required_tier_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['required_tier_id']);

            // Drop index
            $table->dropIndex(['required_tier_id']);

            // Drop column
            $table->dropColumn('required_tier_id');
        });
    }
};
