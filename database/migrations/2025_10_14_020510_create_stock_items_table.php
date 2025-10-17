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
        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique()->nullable(); // Stock Keeping Unit
            $table->text('description')->nullable();
            $table->string('category')->nullable(); // e.g., 'vegetables', 'meat', 'dairy', 'spices'
            $table->string('unit_of_measure'); // e.g., 'kg', 'liters', 'pieces', 'bottles'
            $table->decimal('current_quantity', 10, 2)->default(0); // Current stock level
            $table->decimal('minimum_threshold', 10, 2); // Minimum stock before alert
            $table->decimal('reorder_point', 10, 2); // When to trigger auto-reorder
            $table->decimal('reorder_quantity', 10, 2); // How much to order
            $table->decimal('unit_price', 10, 2)->default(0); // Cost per unit
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->onDelete('set null');
            $table->string('storage_location')->nullable(); // e.g., 'Cold Storage', 'Dry Storage'
            $table->boolean('is_active')->default(true);
            $table->date('last_restock_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_items');
    }
};
