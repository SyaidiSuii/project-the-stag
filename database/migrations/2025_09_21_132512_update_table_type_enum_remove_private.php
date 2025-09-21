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
        // First, update any existing 'private' records to 'indoor' (or another appropriate value)
        DB::table('tables')->where('table_type', 'private')->update(['table_type' => 'indoor']);
        
        // Then modify the enum to remove 'private' option
        DB::statement("ALTER TABLE tables MODIFY COLUMN table_type ENUM('indoor', 'outdoor', 'vip') DEFAULT 'indoor'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original enum with 'private' option
        DB::statement("ALTER TABLE tables MODIFY COLUMN table_type ENUM('indoor', 'outdoor', 'vip', 'private') DEFAULT 'indoor'");
    }
};
