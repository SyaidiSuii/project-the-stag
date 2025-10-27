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
        Schema::table('customer_vouchers', function (Blueprint $table) {
            $table->enum('source', ['collection', 'reward'])->default('collection')->after('voucher_template_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_vouchers', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
