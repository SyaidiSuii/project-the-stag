<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add free_product_id to track when cart item is from free product reward redemption.
     */
    public function up(): void
    {
        Schema::table('user_carts', function (Blueprint $table) {
            $table->foreignId('free_product_id')->nullable()->after('is_free_item')->constrained('customer_free_products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_carts', function (Blueprint $table) {
            $table->dropForeign(['free_product_id']);
            $table->dropColumn('free_product_id');
        });
    }
};
