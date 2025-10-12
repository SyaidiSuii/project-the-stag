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
        Schema::table('order_items', function (Blueprint $table) {
            // Link to promotion if item was part of a promotion
            $table->foreignId('promotion_id')->nullable()
                ->after('menu_item_id')
                ->constrained('promotions')
                ->onDelete('set null')
                ->comment('Promotion applied to this item');

            // Track if item is part of combo/bundle
            $table->boolean('is_combo_item')->default(false)
                ->after('promotion_id')
                ->comment('Is this item part of a combo deal');

            $table->string('combo_group_id', 50)->nullable()
                ->after('is_combo_item')
                ->comment('Group ID to link items in same combo purchase');

            // Store original price before promotion
            $table->decimal('original_price', 10, 2)->nullable()
                ->after('unit_price')
                ->comment('Original price before discount');

            $table->decimal('discount_amount', 10, 2)->default(0)
                ->after('original_price')
                ->comment('Discount applied from promotion');

            // Store promotion snapshot for history
            $table->json('promotion_snapshot')->nullable()
                ->after('special_note')
                ->comment('Snapshot of promotion details at time of order');

            // Indexes
            $table->index('promotion_id');
            $table->index('combo_group_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['promotion_id']);
            $table->dropIndex(['order_items_promotion_id_index']);
            $table->dropIndex(['order_items_combo_group_id_index']);

            $table->dropColumn([
                'promotion_id',
                'is_combo_item',
                'combo_group_id',
                'original_price',
                'discount_amount',
                'promotion_snapshot'
            ]);
        });
    }
};
