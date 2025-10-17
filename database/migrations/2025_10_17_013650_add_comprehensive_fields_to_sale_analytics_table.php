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
        Schema::table('sale_analytics', function (Blueprint $table) {
            // QR Code Analytics
            $table->integer('qr_session_count')->default(0)->after('qr_orders');
            $table->decimal('qr_revenue', 10, 2)->default(0)->after('qr_session_count');

            // Table Booking Analytics
            $table->integer('table_booking_count')->default(0)->after('qr_revenue');
            $table->decimal('table_utilization_rate', 5, 2)->default(0)->after('table_booking_count');

            // Promotion & Rewards Analytics
            $table->integer('promotion_usage_count')->default(0)->after('table_utilization_rate');
            $table->decimal('promotion_discount_total', 10, 2)->default(0)->after('promotion_usage_count');
            $table->integer('rewards_redeemed_count')->default(0)->after('promotion_discount_total');

            // Stock & Cost Analytics (for future enhancement)
            $table->decimal('cogs_total', 10, 2)->nullable()->after('rewards_redeemed_count');
            $table->decimal('gross_profit', 10, 2)->nullable()->after('cogs_total');
            $table->decimal('profit_margin', 5, 2)->nullable()->after('gross_profit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sale_analytics', function (Blueprint $table) {
            $table->dropColumn([
                'qr_session_count',
                'qr_revenue',
                'table_booking_count',
                'table_utilization_rate',
                'promotion_usage_count',
                'promotion_discount_total',
                'rewards_redeemed_count',
                'cogs_total',
                'gross_profit',
                'profit_margin',
            ]);
        });
    }
};
