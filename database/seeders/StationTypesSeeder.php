<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\StationType;
use Illuminate\Support\Facades\DB;

class StationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stationTypes = [
            ['station_type' => 'hot_kitchen', 'icon' => '&#x1F525;'],
            ['station_type' => 'cold_kitchen', 'icon' => '&#x1F957;'],
            ['station_type' => 'drinks', 'icon' => '&#x1F379;'],
            ['station_type' => 'desserts', 'icon' => '&#x1F370;'],
            ['station_type' => 'grill', 'icon' => '&#x1F969;'],
            ['station_type' => 'bakery', 'icon' => '&#x1F956;'],
            ['station_type' => 'salad_bar', 'icon' => '&#x1F96D;'],
            ['station_type' => 'pastry', 'icon' => '&#x1F9C1;'],
        ];

        foreach ($stationTypes as $type) {
            StationType::firstOrCreate(
                ['station_type' => $type['station_type']],
                ['icon' => $type['icon']]
            );
        }

        // Update existing kitchen_stations to link with station_types
        $this->updateExistingStations();
    }

    /**
     * Update existing kitchen stations to use the new foreign key relationship
     */
    private function updateExistingStations()
    {
        $stations = DB::table('kitchen_stations')->whereNull('station_type_id')->get();

        foreach ($stations as $station) {
            $stationType = StationType::where('station_type', $station->station_type)->first();

            if ($stationType) {
                DB::table('kitchen_stations')
                    ->where('id', $station->id)
                    ->update(['station_type_id' => $stationType->id]);
            }
        }
    }
}
