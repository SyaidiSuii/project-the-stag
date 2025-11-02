<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignMissingStationsSeeder extends Seeder
{
    public function run()
    {
        // Update Western category to use Hot Cooking Station
        DB::table('categories')
            ->where('name', 'Western')
            ->update([
                'default_station_type' => 'general_kitchen',
                'default_station_id' => 1 // Hot Cooking Station
            ]);

        // Update Community Water to use Drinks Station
        DB::table('categories')
            ->where('name', 'Community Water')
            ->update([
                'default_station_type' => 'drinks',
                'default_station_id' => 3 // Beverages & Drinks
            ]);

        $this->command->info('✅ Missing station assignments added to categories');
    }
}
