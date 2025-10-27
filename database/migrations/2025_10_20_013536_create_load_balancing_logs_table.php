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
        Schema::create('load_balancing_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->nullable()->constrained('orders')->onDelete('set null');
            $table->foreignId('station_id')->nullable()->constrained('kitchen_stations')->onDelete('set null');
            $table->enum('action_type', ['assignment', 'redistribution', 'completion', 'overload_alert', 'manual_intervention']);
            $table->integer('old_load')->nullable();
            $table->integer('new_load')->nullable();
            $table->text('reason')->nullable();
            $table->json('metadata')->nullable(); // Extra context data
            $table->timestamps();

            $table->index(['station_id', 'action_type']);
            $table->index(['order_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('load_balancing_logs');
    }
};
