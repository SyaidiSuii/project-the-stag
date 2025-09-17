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
        // First, update any NULL confirmation codes to unique temporary values
        $ordersWithNullCode = \DB::table('orders')->whereNull('confirmation_code')->get();
        foreach ($ordersWithNullCode as $order) {
            \DB::table('orders')
                ->where('id', $order->id)
                ->update(['confirmation_code' => 'TEMP-' . $order->id]);
        }
            
        $reservationsWithNullCode = \DB::table('table_reservations')->whereNull('confirmation_code')->get();
        foreach ($reservationsWithNullCode as $reservation) {
            \DB::table('table_reservations')
                ->where('id', $reservation->id)
                ->update(['confirmation_code' => 'TRES-' . $reservation->id]);
        }
        
        Schema::table('orders', function (Blueprint $table) {
            // Update confirmation_code column to allow longer format: STAG-20250917-7G3K
            $table->string('confirmation_code', 20)->change();
        });
        
        Schema::table('table_reservations', function (Blueprint $table) {
            // Also update table_reservations if they use similar format
            $table->string('confirmation_code', 20)->change();
        });
        
        // Generate proper confirmation codes for records that had NULL values
        $ordersWithTempCode = \DB::table('orders')->where('confirmation_code', 'like', 'TEMP-%')->get();
        foreach ($ordersWithTempCode as $order) {
            \DB::table('orders')
                ->where('id', $order->id)
                ->update(['confirmation_code' => \App\Models\Order::generateConfirmationCode()]);
        }
        
        $reservationsWithTempCode = \DB::table('table_reservations')->where('confirmation_code', 'like', 'TRES-%')->get();
        foreach ($reservationsWithTempCode as $reservation) {
            \DB::table('table_reservations')
                ->where('id', $reservation->id)
                ->update(['confirmation_code' => \App\Models\TableReservation::generateConfirmationCode()]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('confirmation_code', 10)->change();
        });
        
        Schema::table('table_reservations', function (Blueprint $table) {
            $table->string('confirmation_code', 10)->change();
        });
    }
};
