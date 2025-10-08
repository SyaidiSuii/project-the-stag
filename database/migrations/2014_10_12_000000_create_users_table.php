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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username', 255)->unique()->nullable();
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->date('dob')->nullable();
            $table->rememberToken();
            $table->string('phone_number', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('points_balance')->default(0);
            $table->boolean('is_super_admin')->default(false);
            $table->date('last_checkin_date')->nullable();
            $table->integer('checkin_streak')->default(0);
            $table->softDeletes();
            $table->timestamps();
            
            // Unique constraint for email with soft delete
            $table->unique(['email', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
