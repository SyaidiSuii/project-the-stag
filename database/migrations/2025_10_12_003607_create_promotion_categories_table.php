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
        Schema::create('promotion_categories', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('promotion_id')->constrained('promotions')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');

            // Category-specific discount (for item_discount promotion type)
            $table->decimal('discount_percentage', 5, 2)->nullable()
                ->comment('Discount % for items in this category');

            $table->decimal('discount_amount', 10, 2)->nullable()
                ->comment('Fixed discount amount for items in this category');

            $table->timestamps();

            // Indexes
            $table->index(['promotion_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotion_categories');
    }
};
