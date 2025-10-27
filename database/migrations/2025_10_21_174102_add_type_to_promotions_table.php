<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            // Skip adding promotion_type as it already exists in table
            // Skip adding promotion_data as table already has promo_config
            // Skip adding banner_image as table already has image_path

            // Make promo_code nullable (not all types need it) - already nullable
            // Make discount fields nullable (not all types use them the same way) - already nullable in previous migration
        });

        // No need to update existing promotions as promotion_type already exists
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration doesn't actually add anything anymore, so nothing to rollback
        Schema::table('promotions', function (Blueprint $table) {
            // No changes to revert
        });
    }
};
