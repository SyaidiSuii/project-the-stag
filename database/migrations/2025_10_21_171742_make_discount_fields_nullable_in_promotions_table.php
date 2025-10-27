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
        Schema::table('promotions', function (Blueprint $table) {
            // Make discount fields nullable since not all promotion types need them
            // (e.g., combo_deal uses combo_price instead)
            $table->string('discount_type', 255)->nullable()->change();
            $table->decimal('discount_value', 10, 2)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('promotions', function (Blueprint $table) {
            // Revert to original NOT NULL constraints
            $table->enum('discount_type', ['percentage', 'fixed'])->change();
            $table->decimal('discount_value', 10, 2)->change();
        });
    }
};
