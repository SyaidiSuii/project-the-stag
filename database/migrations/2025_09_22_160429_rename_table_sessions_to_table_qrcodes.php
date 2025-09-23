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
        // Rename table_sessions to table_qrcodes
        Schema::rename('table_sessions', 'table_qrcodes');
        
        // Update foreign key constraint in orders table
        Schema::table('orders', function (Blueprint $table) {
            // Drop existing foreign key constraint
            $table->dropForeign(['table_session_id']);
            
            // Drop existing index
            $table->dropIndex(['table_session_id', 'order_status']);
            
            // Rename the column
            $table->renameColumn('table_session_id', 'table_qrcode_id');
        });
        
        // Re-add the foreign key constraint with new table name
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('table_qrcode_id')
                  ->references('id')
                  ->on('table_qrcodes')
                  ->nullOnDelete();
                  
            // Re-add index with new column name
            $table->index(['table_qrcode_id', 'order_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop foreign key constraint
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['table_qrcode_id']);
            $table->dropIndex(['table_qrcode_id', 'order_status']);
            
            // Rename column back
            $table->renameColumn('table_qrcode_id', 'table_session_id');
        });
        
        // Re-add original foreign key
        Schema::table('orders', function (Blueprint $table) {
            $table->foreign('table_session_id')
                  ->references('id')
                  ->on('table_sessions')
                  ->nullOnDelete();
                  
            $table->index(['table_session_id', 'order_status']);
        });
        
        // Rename table back to original name
        Schema::rename('table_qrcodes', 'table_sessions');
    }
};