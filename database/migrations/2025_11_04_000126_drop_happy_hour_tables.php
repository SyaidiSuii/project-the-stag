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
        // Drop tables in correct order (pivot table first, then main table)
        Schema::dropIfExists('happy_hour_deal_items');
        Schema::dropIfExists('happy_hour_deals');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate tables in reverse order if needed
        Schema::create('happy_hour_deals', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('discount_percentage', 5, 2);
            $table->time('start_time');
            $table->time('end_time');
            $table->json('days_of_week');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('happy_hour_deal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained('happy_hour_deals')->onDelete('cascade');
            $table->foreignId('menu_item_id')->constrained('menu_items')->onDelete('cascade');
        });
    }
};
