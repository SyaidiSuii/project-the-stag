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
        Schema::table('staff_profiles', function (Blueprint $table) {
            // Add staff_id column after id
            $table->string('staff_id', 30)->nullable()->unique()->after('id');

            // Add ic_number column for ID generation
            $table->string('ic_number', 14)->nullable()->after('user_id');

            // Add index for faster lookups
            $table->index('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropIndex(['staff_id']);
            $table->dropColumn(['staff_id', 'ic_number']);
        });
    }
};
