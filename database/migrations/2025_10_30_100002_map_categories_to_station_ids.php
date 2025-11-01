<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Map station types to actual station IDs
        $stationMapping = [];
        
        // Get stations and create mapping
        $stations = DB::table('kitchen_stations')->get();
        foreach ($stations as $station) {
            $stationMapping[$station->station_type] = $station->id;
        }

        // Update categories to use station_id based on their station_type
        $categories = DB::table('categories')->whereNotNull('default_station_type')->get();
        
        foreach ($categories as $category) {
            if (isset($stationMapping[$category->default_station_type])) {
                DB::table('categories')
                    ->where('id', $category->id)
                    ->update(['default_station_id' => $stationMapping[$category->default_station_type]]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set all default_station_id back to NULL
        DB::table('categories')->update(['default_station_id' => null]);
    }
};
