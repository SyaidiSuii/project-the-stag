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
        // Update all 'indoor' table_type to 'outdoor'
        DB::table('tables')
            ->where('table_type', 'indoor')
            ->update(['table_type' => 'outdoor']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to 'indoor' if needed
        // Note: This won't restore the exact original state
        // as we don't know which tables were originally 'outdoor'
    }
};
