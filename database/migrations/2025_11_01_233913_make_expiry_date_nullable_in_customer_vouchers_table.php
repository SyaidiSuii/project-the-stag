<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix: Make expiry_date nullable in customer_vouchers table
     * Some vouchers may not have expiry dates (permanent vouchers)
     */
    public function up(): void
    {
        Schema::table('customer_vouchers', function (Blueprint $table) {
            $table->date('expiry_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_vouchers', function (Blueprint $table) {
            $table->date('expiry_date')->nullable(false)->change();
        });
    }
};
