<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DEPRECATED: This table was removed in Phase 2.2
 *
 * exchange_points table was 100% redundant with rewards table.
 * Dropped by migration: 2025_10_31_002431_drop_redundant_exchange_points_tables.php
 *
 * This migration file is kept for historical reference only.
 * DO NOT run this migration on new installations.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_points');
    }
};
