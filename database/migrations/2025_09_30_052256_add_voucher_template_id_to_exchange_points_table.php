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
        Schema::table('exchange_points', function (Blueprint $table) {
            $table->foreignId('voucher_template_id')->nullable()->after('id')->constrained('voucher_templates')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exchange_points', function (Blueprint $table) {
            $table->dropForeign(['voucher_template_id']);
            $table->dropColumn('voucher_template_id');
        });
    }
};
