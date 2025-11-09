<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * STOCK MANAGEMENT FEATURE - TEMPORARILY DISABLED
     * This migration drops all stock management related tables.
     *
     * Tables being dropped:
     * - recipe_ingredients (pivot table linking menu_items and stock_items)
     * - stock_usage_predictions
     * - stock_transactions
     * - purchase_order_items
     * - purchase_orders
     * - stock_items
     * - suppliers
     */
    public function up(): void
    {
        // Drop in reverse order of dependencies (foreign keys first)

        Schema::dropIfExists('recipe_ingredients');
        Schema::dropIfExists('stock_usage_predictions');
        Schema::dropIfExists('stock_transactions');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('stock_items');
        Schema::dropIfExists('suppliers');
    }

    /**
     * Reverse the migrations.
     *
     * NOTE: This migration is NOT reversible.
     * If you want to restore stock management functionality:
     * 1. Restore all .bak files (remove .bak extension)
     * 2. Uncomment routes in web.php
     * 3. Uncomment methods in MenuItem model
     * 4. Run composer dump-autoload
     * 5. Re-create the original migration files for these tables
     */
    public function down(): void
    {
        // This migration is not reversible
        // The original table structures should be recreated from the original migrations
        throw new \Exception('This migration cannot be reversed. Please restore original migration files.');
    }
};
