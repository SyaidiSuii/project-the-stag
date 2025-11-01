<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add menu_item_id for "product" type rewards
     * Links reward to specific menu item that will be given for free
     */
    public function up(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->foreignId('menu_item_id')
                ->nullable()
                ->after('voucher_template_id')
                ->constrained('menu_items')
                ->onDelete('set null')
                ->comment('For product type - which menu item is given free');

            $table->index('menu_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropForeign(['menu_item_id']);
            $table->dropColumn('menu_item_id');
        });
    }
};
