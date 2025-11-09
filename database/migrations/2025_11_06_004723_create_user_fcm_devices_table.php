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
        Schema::create('user_fcm_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('device_token')->unique();
            $table->enum('device_type', ['web', 'android', 'ios'])->default('web');
            $table->string('platform')->nullable(); // Chrome, Safari, Firefox, Android, iOS, etc.
            $table->string('browser')->nullable(); // Browser name
            $table->string('version')->nullable(); // Browser/OS version
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            // Indexes for better query performance
            $table->index(['user_id', 'is_active']);
            $table->index('device_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_fcm_devices');
    }
};
