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
        Schema::create('station_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('station_id')->constrained('kitchen_stations')->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained('order_items')->onDelete('cascade');
            $table->integer('assignment_priority')->default(1); // 1 = highest priority
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->enum('status', ['assigned', 'started', 'completed', 'cancelled'])->default('assigned');
            $table->timestamps();

            $table->index(['order_id', 'station_id']);
            $table->index(['station_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('station_assignments');
    }
};
