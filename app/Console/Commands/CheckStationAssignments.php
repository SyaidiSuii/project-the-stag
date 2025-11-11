<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckStationAssignments extends Command
{
    protected $signature = 'check:stations';
    protected $description = 'Check station assignments for categories and menu items';

    public function handle()
    {
        $this->info('=== KITCHEN STATIONS ===');
        $stations = DB::table('kitchen_stations')->select('id', 'name', 'icon')->get();
        foreach ($stations as $station) {
            $this->line("ID: {$station->id} | Name: {$station->name} | Icon: {$station->icon}");
        }

        $this->info("\n=== CATEGORIES ===");
        $categories = DB::table('categories')->select('id', 'name', 'default_station_id')->get();
        foreach ($categories as $cat) {
            $stationId = $cat->default_station_id ?? 'NULL';
            $this->line("ID: {$cat->id} | Name: {$cat->name} | StationID: {$stationId}");
        }

        $this->info("\n=== MENU ITEMS (First 10) ===");
        $items = DB::table('menu_items')->select('id', 'name', 'category_id', 'station_type', 'station_override_id')->limit(10)->get();
        foreach ($items as $item) {
            $type = $item->station_type ?? 'NULL';
            $override = $item->station_override_id ?? 'NULL';
            $this->line("ID: {$item->id} | Name: {$item->name} | CategoryID: {$item->category_id} | Type: {$type} | Override: {$override}");
        }

        return 0;
    }
}
