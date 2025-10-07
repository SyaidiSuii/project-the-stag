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
        Schema::table('rewards', function (Blueprint $table) {
            $table->decimal('reward_value', 10, 2)->nullable()->after('reward_type')->comment('RM value of reward');
            $table->decimal('minimum_order', 10, 2)->nullable()->after('reward_value')->comment('minimum spend to use reward');
            $table->integer('usage_limit')->nullable()->after('expiry_days')->comment('total usage limit for all customers');
            $table->integer('max_redemptions')->nullable()->after('usage_limit')->comment('max redemptions per customer');
            $table->enum('redemption_method', ['show_to_staff', 'bring_voucher', 'qr_code_scan', 'auto_applied', 'phone_verification'])->default('show_to_staff')->after('max_redemptions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropColumn(['reward_value', 'minimum_order', 'usage_limit', 'max_redemptions', 'redemption_method']);
        });
    }
};
