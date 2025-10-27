<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\KitchenStation;
use App\Models\StationAssignment;

class RecalculateKitchenLoads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kitchen:recalculate-loads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate kitchen station loads based on active orders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recalculating kitchen station loads based on item quantities...');

        $stations = KitchenStation::all();

        foreach ($stations as $station) {
            // Calculate total item quantities for active assignments
            $activeLoad = StationAssignment::where('station_id', $station->id)
                ->whereIn('status', ['assigned', 'started'])
                ->whereHas('order', function ($query) {
                    $query->whereIn('order_status', ['pending', 'confirmed', 'preparing', 'ready']);
                })
                ->with('orderItem')
                ->get()
                ->sum(function ($assignment) {
                    // Sum the quantity of each order item
                    return $assignment->orderItem ? $assignment->orderItem->quantity : 1;
                });

            // Update the station's current load
            $oldLoad = $station->current_load;
            $station->current_load = $activeLoad;
            $station->save();

            $this->line("Station: {$station->name} - Old Load: {$oldLoad} items → New Load: {$activeLoad} items");
        }

        $this->info('✅ Kitchen loads recalculated successfully based on item quantities!');

        return 0;
    }
}
