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
        Schema::create('menu_item_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')
                ->constrained('menu_items')
                ->onDelete('cascade');
            $table->string('name'); // e.g., "More Rice", "Extra Spicy", "Add Egg"
            $table->decimal('price', 8, 2)->default(0.00); // Additional price for this addon
            $table->boolean('is_available')->default(true);
            $table->integer('sort_order')->default(0); // For ordering display
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menu_item_addons');
    }
};
