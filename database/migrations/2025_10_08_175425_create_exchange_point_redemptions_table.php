<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DEPRECATED: This table was removed in Phase 2.2
 *
 * exchange_point_redemptions table was 100% redundant with customer_rewards table.
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_point_redemptions');
    }
};
