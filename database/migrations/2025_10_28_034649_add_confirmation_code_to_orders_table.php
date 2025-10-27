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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('confirmation_code')->nullable()->unique();
            $table->string('session_token')->nullable();
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['confirmation_code', 'session_token', 'guest_name', 'guest_phone']);
        });
    }
};
