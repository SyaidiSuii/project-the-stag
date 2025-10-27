<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('user_carts', function (Blueprint $table) {
            // Track which promotion this cart item belongs to (for combo/bundle/buy-x-free-y)
            $table->foreignId('promotion_id')->nullable()
                ->after('promo_discount_amount')
                ->constrained('promotions')
                ->nullOnDelete()
                ->comment('Promotion ID if this item is part of a combo/bundle');

            // Group identifier for items added together as part of a promotion
            $table->string('promotion_group_id', 50)->nullable()
                ->after('promotion_id')
                ->comment('UUID to group items added together from same combo/bundle');

            // Flag to indicate if this item is free (buy-x-free-y)
            $table->boolean('is_free_item')->default(false)
                ->after('promotion_group_id')
                ->comment('True if this is a free item from buy-x-free-y promotion');

            // Add index for faster queries
            $table->index(['promotion_id', 'promotion_group_id'], 'idx_promotion_tracking');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_carts', function (Blueprint $table) {
            // Drop foreign key first before dropping index
            $table->dropForeign(['promotion_id']);
            $table->dropIndex('idx_promotion_tracking');
            $table->dropColumn(['promotion_id', 'promotion_group_id', 'is_free_item']);
        });
    }
};
