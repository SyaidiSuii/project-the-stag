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
        Schema::table('checkin_settings', function (Blueprint $table) {
            $table->json('streak_milestones')->nullable()->after('daily_points')->comment('Days that trigger fire animation e.g. [7,14,30]');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkin_settings', function (Blueprint $table) {
            $table->dropColumn('streak_milestones');
        });
    }
};
