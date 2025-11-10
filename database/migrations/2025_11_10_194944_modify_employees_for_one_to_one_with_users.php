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
        Schema::table('employees', function (Blueprint $table) {
            // Drop columns that will be retrieved from the users table
            $table->dropColumn(['first_name', 'last_name', 'email']);

            // Make the user_id unique to enforce a one-to-one relationship
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Re-add the columns
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->string('email')->unique()->after('last_name');

            // Drop the unique constraint
            // The index name is typically table_column_unique
            $table->dropUnique('employees_user_id_unique');
        });
    }
};
