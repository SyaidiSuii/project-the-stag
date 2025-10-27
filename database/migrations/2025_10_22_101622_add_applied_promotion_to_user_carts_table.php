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
        Schema::table('user_carts', function (Blueprint $table) {
            // Store applied promo code for this cart session
            $table->string('applied_promo_code', 50)->nullable()
                ->after('unavailable_since')
                ->comment('Promo code applied to this cart');

            // Cache the discount amount to avoid recalculation
            $table->decimal('promo_discount_amount', 10, 2)->default(0)
                ->after('applied_promo_code')
                ->comment('Cached discount amount from promotion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_carts', function (Blueprint $table) {
            $table->dropColumn(['applied_promo_code', 'promo_discount_amount']);
        });
    }
};
