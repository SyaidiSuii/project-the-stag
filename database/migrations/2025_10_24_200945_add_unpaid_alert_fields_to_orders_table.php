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
            $table->timestamp('unpaid_alert_sent_at')->nullable()->after('actual_completion_time');
            $table->boolean('is_flagged_unpaid')->default(false)->after('unpaid_alert_sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['unpaid_alert_sent_at', 'is_flagged_unpaid']);
        });
    }
};
