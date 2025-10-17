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
        Schema::create('daily_sales_summary', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->unsignedInteger('total_orders')->default(0);
            $table->unsignedInteger('total_items_sold')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_sales_summary');
    }
};
