<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PHASE 1.2: Fix Rewards Table Schema
     * Add missing columns that are used in Reward model but don't exist in database.
     */
    public function up(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            // Add missing columns
            $table->string('name')->nullable()->after('id')->comment('Alias for title');
            $table->decimal('reward_value', 10, 2)->nullable()->after('reward_type')->comment('Monetary value of reward');
            $table->decimal('minimum_order', 10, 2)->nullable()->after('reward_value')->comment('Minimum order amount required');
            $table->integer('usage_limit')->nullable()->after('expiry_days')->comment('Max redemptions per user');
            $table->integer('max_redemptions')->nullable()->after('usage_limit')->comment('Total redemptions allowed globally');
            $table->string('redemption_method')->nullable()->after('max_redemptions')->comment('How to redeem: qr_code, counter, auto');
            $table->text('terms_conditions')->nullable()->after('redemption_method')->comment('Terms and conditions');
            $table->timestamp('expires_at')->nullable()->after('terms_conditions')->comment('Absolute expiry datetime');

            // Add soft deletes and audit columns
            $table->softDeletes()->after('updated_at');
            $table->unsignedBigInteger('created_by')->nullable()->after('deleted_at');
            $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');

            // Add foreign keys for audit
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);

            // Drop columns
            $table->dropColumn([
                'name',
                'reward_value',
                'minimum_order',
                'usage_limit',
                'max_redemptions',
                'redemption_method',
                'terms_conditions',
                'expires_at',
                'deleted_at',
                'created_by',
                'updated_by'
            ]);
        });
    }
};
