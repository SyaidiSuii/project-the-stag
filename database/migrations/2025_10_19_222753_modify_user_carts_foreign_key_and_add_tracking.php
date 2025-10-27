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
        Schema::table('user_carts', function (Blueprint $table) {
            // Drop existing foreign key with CASCADE delete
            // Laravel default naming: {table}_{column}_foreign
            $table->dropForeign('user_carts_menu_item_id_foreign');

            // Re-add foreign key with RESTRICT to prevent cascade deletion
            // This keeps cart items even when menu item is soft deleted
            // Admin won't be able to force delete if cart items exist (RESTRICT)
            $table->foreign('menu_item_id')
                ->references('id')
                ->on('menu_items')
                ->onDelete('restrict');

            // Add tracking timestamps
            $table->timestamp('last_checked_at')->nullable()->after('special_notes')
                ->comment('Last time item availability was checked');

            $table->timestamp('unavailable_since')->nullable()->after('last_checked_at')
                ->comment('When the menu item became unavailable/deleted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_carts', function (Blueprint $table) {
            // Remove tracking timestamps
            $table->dropColumn(['last_checked_at', 'unavailable_since']);

            // Restore original CASCADE delete behavior
            $table->dropForeign('user_carts_menu_item_id_foreign');

            $table->foreign('menu_item_id')
                ->references('id')
                ->on('menu_items')
                ->onDelete('cascade');
        });
    }
};
