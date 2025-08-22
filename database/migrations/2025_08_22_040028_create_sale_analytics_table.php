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
        Schema::create('sale_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique()->index();
            $table->decimal('total_sales', 12, 2);
            $table->integer('total_orders');
            $table->decimal('average_order_value', 10, 2);
            $table->json('peak_hours')->nullable(); // contoh: {"breakfast":8,"lunch":13,"dinner":19}
            $table->json('popular_items')->nullable();
            $table->integer('unique_customers');
            $table->integer('new_customers')->default(0);
            $table->integer('returning_customers')->default(0);
            $table->integer('dine_in_orders')->default(0);
            $table->integer('takeaway_orders')->default(0);
            $table->integer('delivery_orders')->default(0);
            $table->integer('mobile_orders')->default(0);
            $table->integer('qr_orders')->default(0);
            $table->decimal('total_revenue_dine_in', 10, 2)->default(0.00);
            $table->decimal('total_revenue_takeaway', 10, 2)->default(0.00);
            $table->decimal('total_revenue_delivery', 10, 2)->default(0.00);
            $table->decimal('average_preparation_time', 5, 2)->nullable();
            $table->decimal('customer_satisfaction_avg', 3, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sale_analytics');
    }
};
