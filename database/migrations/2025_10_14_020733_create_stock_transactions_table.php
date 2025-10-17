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
        Schema::create('stock_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('stock_items')->onDelete('cascade');
            $table->enum('transaction_type', ['usage', 'restock', 'adjustment', 'wastage', 'initial']);
            $table->decimal('quantity', 10, 2); // Positive for restock, negative for usage
            $table->decimal('previous_quantity', 10, 2);
            $table->decimal('new_quantity', 10, 2);
            $table->string('reference_type')->nullable(); // e.g., 'Order', 'PurchaseOrder', 'Manual'
            $table->unsignedBigInteger('reference_id')->nullable(); // ID of related order/PO
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_transactions');
    }
};
