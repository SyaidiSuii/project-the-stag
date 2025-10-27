<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KitchenStation;
use App\Models\Category;

class KitchenStationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default kitchen stations
        $stations = [
            [
                'name' => 'Hot Cooking Station',
                'station_type' => 'hot_kitchen',
                'max_capacity' => 10,
                'current_load' => 0,
                'is_active' => true,
                'operating_hours' => ['start' => '10:00', 'end' => '22:00'],
                'description' => 'Main hot food preparation - stir-fry, grilling, frying',
                'sort_order' => 1,
            ],
            [
                'name' => 'Cold Prep & Salads',
                'station_type' => 'cold_kitchen',
                'max_capacity' => 8,
                'current_load' => 0,
                'is_active' => true,
                'operating_hours' => ['start' => '10:00', 'end' => '22:00'],
                'description' => 'Salads, cold appetizers, and fresh preparations',
                'sort_order' => 2,
            ],
            [
                'name' => 'Beverages & Drinks',
                'station_type' => 'drinks',
                'max_capacity' => 15,
                'current_load' => 0,
                'is_active' => true,
                'operating_hours' => ['start' => '10:00', 'end' => '23:00'],
                'description' => 'Juices, smoothies, hot and cold beverages',
                'sort_order' => 3,
            ],
            [
                'name' => 'Dessert Bar',
                'station_type' => 'desserts',
                'max_capacity' => 6,
                'current_load' => 0,
                'is_active' => true,
                'operating_hours' => ['start' => '12:00', 'end' => '22:00'],
                'description' => 'Desserts, pastries, and sweet items',
                'sort_order' => 4,
            ],
        ];

        foreach ($stations as $stationData) {
            KitchenStation::firstOrCreate(
                ['station_type' => $stationData['station_type']],
                $stationData
            );
        }

        $this->command->info('Kitchen stations created successfully!');

        // Set default station types for existing categories
        $this->setDefaultCategoryStations();
    }

    /**
     * Set default station types for existing categories
     */
    protected function setDefaultCategoryStations()
    {
        // Food categories → Hot Kitchen
        Category::where('type', 'food')
            ->whereNull('default_station_type')
            ->update([
                'default_station_type' => 'hot_kitchen',
                'default_load_factor' => 1.0,
            ]);

        // Drink categories → Beverages
        Category::where('type', 'drink')
            ->whereNull('default_station_type')
            ->update([
                'default_station_type' => 'drinks',
                'default_load_factor' => 0.3,
            ]);

        // Set meal categories → Hot Kitchen with higher load
        Category::where('type', 'set-meal')
            ->whereNull('default_station_type')
            ->update([
                'default_station_type' => 'hot_kitchen',
                'default_load_factor' => 2.0,
            ]);

        $this->command->info('Category defaults set successfully!');
    }
}
