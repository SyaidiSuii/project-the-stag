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
        Schema::table('users', function (Blueprint $table) {
            // Drop existing unique constraint
            $table->dropUnique('users_email_unique');
            
            // For MySQL, create composite unique index with email and deleted_at
            // This allows same email if one has deleted_at = NULL and another has deleted_at = timestamp
            $table->unique(['email', 'deleted_at'], 'users_email_deleted_at_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop composite unique constraint
            $table->dropUnique('users_email_deleted_at_unique');
            
            // Recreate normal unique constraint
            $table->unique('email');
        });
    }
};
