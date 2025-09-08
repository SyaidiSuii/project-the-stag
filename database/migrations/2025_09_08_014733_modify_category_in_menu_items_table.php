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
        Schema::table('menu_items', function (Blueprint $table) {
            // 1. Add the new foreign key column first
            $table->foreignId('category_id')
                  ->nullable()
                  ->after('price') // Place it after the price column
                  ->constrained('categories')
                  ->onDelete('set null');

            // 2. Drop the old enum column
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_items', function (Blueprint $table) {
            // 1. Add back the old enum column
            $table->enum('category', ['western', 'local', 'drink', 'dessert', 'appetizer'])
                  ->after('price');

            // 2. Drop the foreign key constraint and the column
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
