<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DebugLastOrder extends Command
{
    protected $signature = 'debug:last-order';
    protected $description = 'Debug the last created order distribution';

    public function handle()
    {
        $this->info('=== DEBUGGING LAST ORDER ===');
        
        // Get latest order
        $order = Order::with(['items.menuItem.category', 'stationAssignments.station'])
            ->latest()
            ->first();

        if (!$order) {
            $this->error('No orders found!');
            return 1;
        }

        $this->info("\n📦 ORDER DETAILS:");
        $this->line("  ID: {$order->id}");
        $this->line("  Code: {$order->confirmation_code}");
        $this->line("  Status: {$order->order_status}");
        $this->line("  Total: RM {$order->total_amount}");
        $this->line("  Items Count: {$order->items->count()}");

        $this->info("\n🍽️ ORDER ITEMS:");
        foreach ($order->items as $item) {
            $categoryName = $item->menuItem->category->name ?? 'N/A';
            $categoryStationId = $item->menuItem->category->default_station_id ?? 'NULL';
            
            $this->line("  ✓ {$item->menuItem->name}");
            $this->line("    - Category: {$categoryName}");
            $this->line("    - Category Station ID: {$categoryStationId}");
            $this->line("    - Item Override ID: " . ($item->menuItem->station_override_id ?? 'NULL'));
            
            // Check effective station
            $effectiveStation = $item->menuItem->getEffectiveStation();
            if ($effectiveStation) {
                $this->line("    - ✅ Effective Station: {$effectiveStation->name} (ID: {$effectiveStation->id})");
            } else {
                $this->error("    - ❌ NO EFFECTIVE STATION!");
            }
            $this->line("");
        }

        $this->info("\n🏪 STATION ASSIGNMENTS:");
        if ($order->stationAssignments->count() > 0) {
            foreach ($order->stationAssignments as $assignment) {
                $itemName = $assignment->orderItem->menuItem->name ?? 'N/A';
                $stationName = $assignment->station->name ?? 'N/A';
                $this->line("  ✓ {$itemName} → {$stationName}");
            }
        } else {
            $this->error("  ❌ NO STATION ASSIGNMENTS FOUND!");
        }

        $this->info("\n📊 KITCHEN LOADS:");
        $loads = DB::table('kitchen_loads')
            ->where('order_id', $order->id)
            ->join('kitchen_stations', 'kitchen_loads.station_id', '=', 'kitchen_stations.id')
            ->select('kitchen_stations.name as station_name', 'kitchen_loads.*')
            ->get();

        if ($loads->count() > 0) {
            foreach ($loads as $load) {
                $this->line("  ✓ Station: {$load->station_name}");
                $this->line("    - Load Points: {$load->load_points}");
                $this->line("    - Status: {$load->status}");
            }
        } else {
            $this->error("  ❌ NO KITCHEN LOADS FOUND!");
        }

        $this->info("\n🔍 CHECKING LOGS:");
        $logFile = storage_path('logs/laravel-' . date('Y-m-d') . '.log');
        if (file_exists($logFile)) {
            $logs = file_get_contents($logFile);
            if (str_contains($logs, $order->confirmation_code)) {
                $this->line("  ✓ Order mentioned in logs");
                
                // Extract relevant log lines
                $lines = explode("\n", $logs);
                $relevantLines = array_filter($lines, function($line) use ($order) {
                    return str_contains($line, $order->confirmation_code) || 
                           str_contains($line, "order_id: {$order->id}");
                });
                
                if (!empty($relevantLines)) {
                    $this->info("\n  📝 Relevant Log Entries:");
                    foreach (array_slice($relevantLines, -5) as $line) {
                        $this->line("    " . trim($line));
                    }
                }
            } else {
                $this->warn("  ⚠️  Order not found in today's logs");
            }
        }

        return 0;
    }
}
