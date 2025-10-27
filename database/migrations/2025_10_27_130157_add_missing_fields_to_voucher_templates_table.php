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
        Schema::table('voucher_templates', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('title')->nullable()->after('description');
            $table->decimal('minimum_spend', 10, 2)->default(0)->after('discount_value');
            $table->decimal('max_discount', 10, 2)->nullable()->after('minimum_spend');
            $table->text('terms_conditions')->nullable()->after('max_discount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_templates', function (Blueprint $table) {
            $table->dropColumn(['description', 'title', 'minimum_spend', 'max_discount', 'terms_conditions']);
        });
    }
};
