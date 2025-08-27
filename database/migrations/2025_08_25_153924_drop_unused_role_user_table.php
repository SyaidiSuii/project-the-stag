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
        // Drop custom role_user table since Spatie uses model_has_roles
        if (Schema::hasTable('role_user')) {
            Schema::dropIfExists('role_user');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate role_user table if needed
        if (!Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('role_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }
    }
};
