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
        Schema::create('kitchen_loads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('station_id')->constrained('kitchen_stations')->onDelete('cascade');
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->decimal('load_points', 5, 2)->default(1.0); // Complexity score
            $table->timestamp('estimated_completion_time')->nullable();
            $table->timestamp('actual_completion_time')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->timestamps();

            $table->index(['station_id', 'status']);
            $table->index(['order_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_loads');
    }
};
