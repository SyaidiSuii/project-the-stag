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
            // Add table session support
            $table->foreignId('table_session_id')
                  ->nullable()
                  ->after('table_id')
                  ->constrained('table_sessions')
                  ->nullOnDelete();
                  
            // Add QR order specific fields
            $table->string('guest_name')->nullable()->after('confirmation_code');
            $table->string('guest_phone', 20)->nullable()->after('guest_name');
            $table->string('session_token', 64)->nullable()->after('guest_phone');
            
            // Add index for session-based lookups
            $table->index(['table_session_id', 'order_status']);
            $table->index(['session_token']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['table_session_id']);
            $table->dropIndex(['table_session_id', 'order_status']);
            $table->dropIndex(['session_token']);
            
            $table->dropColumn([
                'table_session_id',
                'guest_name', 
                'guest_phone',
                'session_token'
            ]);
        });
    }
};
