<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * PHASE 2.1: Unify Voucher Structures
     *
     * Problem: VoucherTemplate and VoucherCollection serve the same purpose
     * Solution: Extend voucher_templates with fields from voucher_collections
     *
     * This migration adds:
     * - source_type: Distinguish between 'reward', 'collection', 'promotion'
     * - spending_requirement: Minimum spend to unlock (for collections)
     * - valid_until: Absolute expiry date (alternative to expiry_days)
     * - is_active: Status flag (from collections)
     */
    public function up(): void
    {
        Schema::table('voucher_templates', function (Blueprint $table) {
            // Add source type to distinguish different voucher origins
            $table->enum('source_type', ['reward', 'collection', 'promotion', 'manual'])
                ->default('manual')
                ->after('name')
                ->comment('Where this voucher template comes from');

            // Add spending requirement (for collection-type vouchers)
            $table->decimal('spending_requirement', 10, 2)
                ->nullable()
                ->after('minimum_spend')
                ->comment('Total spending required to unlock this voucher (for collections)');

            // Add absolute expiry date (alternative to relative expiry_days)
            $table->date('valid_until')
                ->nullable()
                ->after('expiry_days')
                ->comment('Absolute expiry date (alternative to expiry_days)');

            // Add is_active status flag
            $table->boolean('is_active')
                ->default(true)
                ->after('valid_until')
                ->comment('Whether this template is active');

            // Add usage tracking
            $table->integer('max_uses_per_user')
                ->nullable()
                ->after('is_active')
                ->comment('Maximum uses per user');

            $table->integer('total_uses_limit')
                ->nullable()
                ->after('max_uses_per_user')
                ->comment('Global usage limit across all users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_templates', function (Blueprint $table) {
            $table->dropColumn([
                'source_type',
                'spending_requirement',
                'valid_until',
                'is_active',
                'max_uses_per_user',
                'total_uses_limit'
            ]);
        });
    }
};
