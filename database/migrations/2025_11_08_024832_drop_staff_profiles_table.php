<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Drop staff_profiles table as feature is not currently in use.
     * Code preserved in commented form for future reactivation.
     */
    public function up(): void
    {
        Schema::dropIfExists('staff_profiles');
    }

    /**
     * Reverse the migrations.
     * Restore staff_profiles table structure for reactivation.
     */
    public function down(): void
    {
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('roles')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('phone_number', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('position', 100);
            $table->string('experience')->nullable();
            $table->string('photo')->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->date('hire_date');
            $table->string('emergency_contact')->nullable();
            $table->string('emergency_phone', 20)->nullable();
            $table->string('staff_id', 30)->unique()->nullable();
            $table->string('ic_number', 14)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('staff_id');
        });
    }
};
