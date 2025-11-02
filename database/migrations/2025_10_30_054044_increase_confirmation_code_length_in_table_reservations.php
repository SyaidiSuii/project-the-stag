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
        Schema::table('table_reservations', function (Blueprint $table) {
            // Increase confirmation_code from varchar(10) to varchar(20) to accommodate format: BK-20251030-XXXX (16 chars)
            // Don't add unique constraint as it already exists
            $table->string('confirmation_code', 20)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_reservations', function (Blueprint $table) {
            // Revert back to varchar(10) if needed
            $table->string('confirmation_code', 10)->change();
        });
    }
};
