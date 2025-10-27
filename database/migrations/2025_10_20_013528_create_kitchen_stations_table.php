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
        Schema::create('kitchen_stations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Hot Cooking", "Beverages"
            $table->enum('station_type', ['hot_kitchen', 'cold_kitchen', 'drinks', 'desserts'])->unique();
            $table->integer('max_capacity')->default(10); // Max concurrent orders
            $table->integer('current_load')->default(0); // Current active orders
            $table->boolean('is_active')->default(true);
            $table->json('operating_hours')->nullable(); // {"start": "10:00", "end": "22:00"}
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_active', 'station_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kitchen_stations');
    }
};
