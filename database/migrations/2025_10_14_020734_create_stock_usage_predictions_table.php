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
        Schema::create('stock_usage_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('stock_items')->onDelete('cascade');
            $table->date('prediction_date');
            $table->decimal('predicted_usage', 10, 2);
            $table->decimal('actual_usage', 10, 2)->nullable();
            $table->decimal('accuracy_score', 5, 2)->nullable(); // Percentage accuracy
            $table->json('metadata')->nullable(); // Store AI model info, factors, etc.
            $table->timestamps();

            // Unique constraint to prevent duplicate predictions
            $table->unique(['stock_item_id', 'prediction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_usage_predictions');
    }
};
