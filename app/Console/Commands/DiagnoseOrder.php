<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\Category;
use App\Models\KitchenStation;

class DiagnoseOrder extends Command
{
    protected $signature = 'order:diagnose {order_id}';
    protected $description = 'Diagnose order items and station assignment issues';

    public function handle()
    {
        $orderId = $this->argument('order_id');
        $order = Order::with(['items.menuItem.category', 'stationAssignments.station'])->find($orderId);

        if (!$order) {
            $this->error("❌ Order #{$orderId} not found!");
            return 1;
        }

        $this->info("=== ORDER DIAGNOSIS FOR #{$order->id} ({$order->confirmation_code}) ===\n");

        $this->line("📦 Order Details:");
        $this->line("  Status: {$order->order_status}");
        $this->line("  Total: RM {$order->total_amount}");
        $this->line("  Items Count: {$order->items->count()}");

        $this->info("\n🍽️ ORDER ITEMS:");
        if ($order->items->isEmpty()) {
            $this->warn("  ⚠️  No items found! This is the problem.");
            return 0;
        }

        foreach ($order->items as $item) {
            $this->line("\n  ✓ {$item->menuItem->name}");
            $this->line("    - Quantity: {$item->quantity}");
            $this->line("    - Unit Price: RM {$item->unit_price}");
            $this->line("    - Total: RM {$item->total_price}");

            $category = $item->menuItem->category;
            $this->line("    - Category: " . ($category ? $category->name : 'N/A'));

            if ($category) {
                $stationId = $category->default_station_id;
                $this->line("    - Category Station ID: " . ($stationId ?? 'NULL'));

                if ($stationId) {
                    $station = KitchenStation::find($stationId);
                    if ($station) {
                        $this->line("    - Station: {$station->name} (Active: " . ($station->is_active ? 'Yes' : 'No') . ")");
                    } else {
                        $this->error("    - ❌ INVALID STATION ID! Station #{$stationId} does not exist");
                    }
                } else {
                    $this->warn("    - ⚠️  No default station assigned to category");
                }
            }

            $this->line("    - Item Override: " . ($item->menuItem->station_override_id ?? 'None'));
        }

        $this->info("\n🏪 STATION ASSIGNMENTS:");
        if ($order->stationAssignments->isEmpty()) {
            $this->warn("  ⚠️  No station assignments found!");
            $this->line("  This means the order was not distributed to kitchen.");
            $this->line("  Possible causes:");
            $this->line("    1. Categories have invalid or NULL default_station_id");
            $this->line("    2. Distribution service failed to run");
            $this->line("    3. All assigned stations are inactive");
        } else {
            foreach ($order->stationAssignments->groupBy('station_id') as $stationId => $assignments) {
                $station = $assignments->first()->station;
                $this->line("\n  📍 {$station->name} ({$assignments->count()} items):");
                foreach ($assignments as $assignment) {
                    $item = $assignment->orderItem;
                    if ($item) {
                        $this->line("    - {$item->menuItem->name} x{$item->quantity} [{$assignment->status}]");
                    }
                }
            }
        }

        $this->info("\n💡 RECOMMENDATIONS:");

        $itemsWithoutStation = $order->items->filter(function ($item) {
            $category = $item->menuItem->category;
            return !$category || !$category->default_station_id;
        });

        if ($itemsWithoutStation->count() > 0) {
            $this->warn("  ⚠️  {$itemsWithoutStation->count()} items have categories without default stations:");
            foreach ($itemsWithoutStation as $item) {
                $categoryName = $item->menuItem->category ? $item->menuItem->category->name : 'N/A';
                $this->line("    - {$item->menuItem->name} (Category: {$categoryName})");
            }
            $this->line("  Fix: Assign default stations to these categories in admin panel");
        }

        if ($order->stationAssignments->isEmpty() && $order->items->count() > 0) {
            $this->info("  💡 Run redistribution: php artisan kitchen:distribute-existing-orders --force");
        }

        return 0;
    }
}
