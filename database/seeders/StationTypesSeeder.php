<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StationTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map station types to their icons for seeding existing stations
        $iconMap = [
            'hot_kitchen' => '🔥',
            'cold_kitchen' => '🥗',
            'drinks' => '🍹',
            'desserts' => '🍰',
            'grill' => '🥩',
            'bakery' => '🥖',
            'salad_bar' => '🥗',
            'pastry' => '🧀',
        ];

        // Update existing kitchen_stations to have icons if they don't already
        foreach ($iconMap as $type => $icon) {
            DB::table('kitchen_stations')
                ->where('station_type', $type)
                ->whereNull('icon')
                ->update(['icon' => $icon]);
        }
    }
}
