<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FixCategoryStationAssignments extends Seeder
{
    public function run()
    {
        // Reassign food categories to Hot Cooking Station (ID: 1)
        // Cold Prep & Salads should only handle salads/cold items
        
        $foodCategories = ['Food', 'Hot Dishes', 'Set Meal', 'Western'];
        
        foreach ($foodCategories as $categoryName) {
            DB::table('categories')
                ->where('name', $categoryName)
                ->update([
                    'default_station_id' => 1, // Hot Cooking Station
                    'default_station_type' => 'general_kitchen'
                ]);
            
            $this->command->info("✅ {$categoryName} → Hot Cooking Station");
        }
        
        $this->command->info("\n✅ Category station assignments fixed!");
        $this->command->info("Hot food items will now go to Hot Cooking Station");
    }
}
