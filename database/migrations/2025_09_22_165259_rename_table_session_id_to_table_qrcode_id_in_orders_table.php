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
            // Simply rename the column without touching foreign keys
            if (Schema::hasColumn('orders', 'table_session_id')) {
                $table->renameColumn('table_session_id', 'table_qrcode_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Simply rename the column back
            if (Schema::hasColumn('orders', 'table_qrcode_id')) {
                $table->renameColumn('table_qrcode_id', 'table_session_id');
            }
        });
    }
};
