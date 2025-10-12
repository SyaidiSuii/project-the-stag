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
        Schema::create('promotion_items', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('promotion_id')->constrained('promotions')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');

            // Item configuration
            $table->integer('quantity')->default(1)
                ->comment('How many of this item in the promotion');

            $table->boolean('is_free')->default(false)
                ->comment('Is this item free in the promotion (for Buy X Free Y)');

            $table->boolean('is_required')->default(true)
                ->comment('Must customer take this item or is it optional');

            $table->boolean('is_customizable')->default(false)
                ->comment('Can customer customize this item');

            $table->decimal('custom_price', 10, 2)->nullable()
                ->comment('Override item price for this promotion');

            $table->json('item_options')->nullable()
                ->comment('Alternative items customer can choose (for flexible combos)');

            $table->integer('sort_order')->default(0)
                ->comment('Display order in the combo');

            $table->timestamps();

            // Indexes
            $table->index(['promotion_id', 'menu_item_id']);
            $table->index('is_required');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_items');
    }
};
