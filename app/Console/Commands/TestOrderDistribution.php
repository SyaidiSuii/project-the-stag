<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\MenuItem;
use App\Services\Kitchen\OrderDistributionService;

class TestOrderDistribution extends Command
{
    protected $signature = 'test:order-distribution';
    protected $description = 'Test order distribution to kitchen stations';

    public function handle()
    {
        $this->info('🧪 Testing Order Distribution...');

        // Get first user
        $user = User::first();
        if (!$user) {
            $this->error('No users found!');
            return 1;
        }

        // Create test order
        $order = Order::create([
            'user_id' => $user->id,
            'order_type' => 'dine_in',
            'order_source' => 'counter',
            'order_status' => 'pending',
            'order_time' => now(),
            'total_amount' => 50.00,
            'payment_status' => 'unpaid',
        ]);

        $this->info("✅ Order #{$order->confirmation_code} created");

        // Add menu items (1 food, 1 drink)
        $foodItem = MenuItem::where('category_id', 10)->first(); // Hot Dishes
        $drinkItem = MenuItem::where('category_id', 12)->first(); // Community Water

        if ($foodItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $foodItem->id,
                'quantity' => 1,
                'unit_price' => $foodItem->price,
                'total_price' => $foodItem->price,
            ]);
            $this->line("  📦 Added: {$foodItem->name} (Category: {$foodItem->category->name})");
        }

        if ($drinkItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'menu_item_id' => $drinkItem->id,
                'quantity' => 1,
                'unit_price' => $drinkItem->price,
                'total_price' => $drinkItem->price,
            ]);
            $this->line("  📦 Added: {$drinkItem->name} (Category: {$drinkItem->category->name})");
        }

        // Test distribution
        $this->info("\n🔄 Distributing order...");
        
        try {
            $distributionService = app(OrderDistributionService::class);
            $distributionService->distributeOrder($order->fresh()->load('items.menuItem.category.defaultStation'));

            $this->info("✅ Distribution successful!");

            // Check results
            $order = $order->fresh();
            $this->info("\n📊 Results:");
            $this->line("  Station Assignments: " . $order->stationAssignments()->count());
            $this->line("  Kitchen Loads: " . $order->kitchenLoads()->count());

            // Show details
            foreach ($order->stationAssignments as $assignment) {
                $item = $assignment->orderItem;
                $station = $assignment->station;
                $this->line("  ✓ {$item->menuItem->name} → {$station->name}");
            }

        } catch (\Exception $e) {
            $this->error("❌ Distribution failed: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }

        $this->info("\n🎉 Test completed successfully!");
        return 0;
    }
}
