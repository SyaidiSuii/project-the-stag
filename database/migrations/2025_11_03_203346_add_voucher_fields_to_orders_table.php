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
        Schema::table('orders', function (Blueprint $table) {
            // Add voucher discount tracking fields
            $table->foreignId('customer_voucher_id')
                ->nullable()
                ->constrained('customer_vouchers')
                ->onDelete('set null')
                ->comment('Voucher used for this order');

            $table->decimal('voucher_discount', 10, 2)
                ->default(0.00)
                ->comment('Discount amount from voucher');

            $table->string('voucher_code', 50)
                ->nullable()
                ->comment('Voucher code applied');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['customer_voucher_id']);
            $table->dropColumn(['customer_voucher_id', 'voucher_discount', 'voucher_code']);
        });
    }
};
