<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add free_item support to voucher templates:
     * - Modify discount_type enum to include 'free_item'
     * - Add applicable_menu_item_ids JSON column for specifying which items can be free
     */
    public function up(): void
    {
        // Modify enum to add 'free_item' type
        DB::statement("ALTER TABLE voucher_templates MODIFY COLUMN discount_type ENUM('percentage', 'fixed', 'free_item') NOT NULL");

        Schema::table('voucher_templates', function (Blueprint $table) {
            // JSON array of menu item IDs that can be claimed as free
            // For free_item type vouchers only
            $table->json('applicable_menu_item_ids')->nullable()->after('discount_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('voucher_templates', function (Blueprint $table) {
            $table->dropColumn('applicable_menu_item_ids');
        });

        // Revert enum back to original values
        DB::statement("ALTER TABLE voucher_templates MODIFY COLUMN discount_type ENUM('percentage', 'fixed') NOT NULL");
    }
};
