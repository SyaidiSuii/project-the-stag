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
        Schema::table('table_reservations', function (Blueprint $table) {
            // Rename columns to match new naming convention
            $table->renameColumn('reservation_date', 'booking_date');
            $table->renameColumn('reservation_time', 'booking_time');
            $table->renameColumn('guest_phone', 'phone');
            $table->renameColumn('number_of_guests', 'party_size');
            
            // Only drop auto_release_time, keep guest fields for guest bookings
            $table->dropColumn('auto_release_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('table_reservations', function (Blueprint $table) {
            // Reverse the column renames
            $table->renameColumn('booking_date', 'booking_date');
            $table->renameColumn('booking_time', 'reservation_time');
            $table->renameColumn('phone', 'guest_phone');
            $table->renameColumn('party_size', 'number_of_guests');
            
            // Add back only the dropped column
            $table->timestamp('auto_release_time')->nullable()->after('notes');
        });
    }
};
