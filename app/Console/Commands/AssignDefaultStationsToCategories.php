<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\KitchenStation;

class AssignDefaultStationsToCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kitchen:assign-stations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign default kitchen stations to categories that don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Assigning default stations to categories...');

        // Get all active stations
        $stations = KitchenStation::where('is_active', true)->get();

        if ($stations->isEmpty()) {
            $this->error('❌ No active kitchen stations found! Please create stations first.');
            return 1;
        }

        // Create a mapping of station types to station IDs
        $stationMap = [];
        foreach ($stations as $station) {
            $stationMap[$station->station_type] = $station->id;
        }

        // Display available stations
        $this->info("\n📋 Available Stations:");
        foreach ($stations as $station) {
            $this->line("  - {$station->name} ({$station->station_type})");
        }

        // Get categories without default stations
        $categories = Category::whereNull('default_station_id')->get();

        if ($categories->isEmpty()) {
            $this->info("\n✅ All categories already have default stations assigned!");
            return 0;
        }

        $this->info("\n🔍 Found {$categories->count()} categories without default stations");

        $updated = 0;
        $skipped = 0;

        foreach ($categories as $category) {
            // Try to match category name/type to station type
            $stationId = $this->guessStationForCategory($category, $stationMap, $stations->first()->id);

            if ($stationId) {
                $category->default_station_id = $stationId;
                $category->save();

                $station = $stations->firstWhere('id', $stationId);
                $this->line("  ✓ {$category->name} → {$station->name}");
                $updated++;
            } else {
                $this->line("  ⊘ {$category->name} → Skipped (no matching station)");
                $skipped++;
            }
        }

        $this->newLine();
        $this->info("✨ Done!");
        $this->info("  ✓ Updated: {$updated} categories");
        if ($skipped > 0) {
            $this->warn("  ⊘ Skipped: {$skipped} categories");
        }

        return 0;
    }

    /**
     * Guess the appropriate station for a category based on its name/type
     */
    protected function guessStationForCategory($category, $stationMap, $fallbackStationId)
    {
        $name = strtolower($category->name);

        // Check for drinks/beverages
        if (preg_match('/(drink|beverage|juice|coffee|tea|smoothie|shake|soda)/i', $name)) {
            return $stationMap['drinks'] ?? $fallbackStationId;
        }

        // Check for desserts/sweets
        if (preg_match('/(dessert|sweet|ice cream|cake|pastry|pudding)/i', $name)) {
            return $stationMap['desserts'] ?? $stationMap['pastry'] ?? $fallbackStationId;
        }

        // Check for salads/cold items
        if (preg_match('/(salad|cold|fresh)/i', $name)) {
            return $stationMap['salad_bar'] ?? $stationMap['cold_kitchen'] ?? $fallbackStationId;
        }

        // Check for grilled items
        if (preg_match('/(grill|bbq|barbecue|steak|burger)/i', $name)) {
            return $stationMap['grill'] ?? $fallbackStationId;
        }

        // Check for baked items
        if (preg_match('/(bake|bread|pizza|pastry)/i', $name)) {
            return $stationMap['bakery'] ?? $stationMap['pastry'] ?? $fallbackStationId;
        }

        // Default to hot kitchen for most food items
        return $stationMap['hot_kitchen'] ?? $fallbackStationId;
    }
}
