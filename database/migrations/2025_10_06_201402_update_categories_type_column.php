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
        // Update Food parent and subcategories
        DB::table('categories')->where('id', 1)->update(['type' => 'food']);
        DB::table('categories')->where('parent_id', 1)->update(['type' => 'food']);

        // Update Drink parent and subcategories
        DB::table('categories')->where('id', 3)->update(['type' => 'drink']);
        DB::table('categories')->where('parent_id', 3)->update(['type' => 'drink']);

        // Update Desert/Set Meal parent and subcategories
        DB::table('categories')->where('id', 5)->update(['type' => 'set-meal', 'name' => 'Set Meal']);
        DB::table('categories')->where('parent_id', 5)->update(['type' => 'set-meal']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to main/sub types
        DB::table('categories')->whereNull('parent_id')->update(['type' => 'main']);
        DB::table('categories')->whereNotNull('parent_id')->update(['type' => 'sub']);
        DB::table('categories')->where('id', 5)->update(['name' => 'Desert']);
    }
};
