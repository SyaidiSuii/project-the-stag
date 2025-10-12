<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Promotion;
use App\Models\MenuItem;
use App\Models\Category;
use Carbon\Carbon;

class PromotionTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some menu items for testing
        $menuItems = MenuItem::take(10)->get();
        $categories = Category::take(3)->get();

        if ($menuItems->count() < 3) {
            $this->command->warn('Not enough menu items found. Please seed menu items first.');
            return;
        }

        $this->command->info('Creating test promotions...');

        // 1. PROMO CODE - Percentage Discount
        $promo1 = Promotion::create([
            'name' => 'Welcome 10% Off',
            'promotion_type' => Promotion::TYPE_PROMO_CODE,
            'promo_code' => 'WELCOME10',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'max_discount_amount' => 20.00,
            'minimum_order_value' => 30.00,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(3),
            'badge_text' => 'NEW USER',
            'banner_color' => '#4CAF50',
            'is_active' => true,
            'is_featured' => true,
            'display_order' => 1,
            'promo_config' => ['first_order_only' => false],
            'terms_conditions' => 'Valid for all customers. Minimum order RM30. Max discount RM20.'
        ]);
        $this->command->info("✓ Created: {$promo1->name} ({$promo1->promo_code})");

        // 2. PROMO CODE - Fixed Discount
        $promo2 = Promotion::create([
            'name' => 'Save RM15',
            'promotion_type' => Promotion::TYPE_PROMO_CODE,
            'promo_code' => 'SAVE15',
            'discount_type' => 'fixed',
            'discount_value' => 15.00,
            'minimum_order_value' => 50.00,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addMonths(1),
            'badge_text' => 'HOT DEAL',
            'banner_color' => '#FF5722',
            'is_active' => true,
            'is_featured' => false,
            'usage_limit_per_customer' => 1,
            'total_usage_limit' => 100,
            'promo_config' => ['first_order_only' => false]
        ]);
        $this->command->info("✓ Created: {$promo2->name} ({$promo2->promo_code})");

        // 3. COMBO DEAL - Lunch Special
        if ($menuItems->count() >= 3) {
            $combo1 = Promotion::create([
                'name' => 'Lunch Special Combo',
                'promotion_type' => Promotion::TYPE_COMBO_DEAL,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(6),
                'applicable_days' => ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
                'applicable_start_time' => '11:00:00',
                'applicable_end_time' => '15:00:00',
                'badge_text' => 'LUNCH TIME',
                'banner_color' => '#FFC107',
                'is_active' => true,
                'is_featured' => true,
                'display_order' => 2,
                'promo_config' => [
                    'combo_price' => 25.00,
                    'original_price' => 35.00,
                    'allow_customization' => false
                ],
                'terms_conditions' => 'Available weekdays 11AM-3PM. Combo includes main + side + drink.'
            ]);

            // Attach 3 items to combo
            $combo1->menuItems()->attach([
                $menuItems[0]->id => ['quantity' => 1, 'is_required' => true, 'sort_order' => 1],
                $menuItems[1]->id => ['quantity' => 1, 'is_required' => true, 'sort_order' => 2],
                $menuItems[2]->id => ['quantity' => 1, 'is_required' => true, 'sort_order' => 3],
            ]);

            $this->command->info("✓ Created: {$combo1->name} (save RM10)");
        }

        // 4. ITEM DISCOUNT - Weekend Special
        if ($categories->count() > 0) {
            $discount1 = Promotion::create([
                'name' => '20% Off All Burgers',
                'promotion_type' => Promotion::TYPE_ITEM_DISCOUNT,
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'max_discount_amount' => 15.00,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addWeeks(2),
                'applicable_days' => ['saturday', 'sunday'],
                'badge_text' => 'WEEKEND',
                'banner_color' => '#9C27B0',
                'is_active' => true,
                'is_featured' => true,
                'display_order' => 3,
                'promo_config' => [
                    'apply_to' => 'categories',
                    'category_ids' => [$categories[0]->id]
                ],
                'terms_conditions' => 'Valid on weekends only. 20% off all items in selected category.'
            ]);

            $discount1->categories()->attach($categories[0]->id);
            $this->command->info("✓ Created: {$discount1->name} (20% off)");
        }

        // 5. BUY X FREE Y - Coffee Promotion
        if ($menuItems->count() >= 4) {
            $buyXFreeY = Promotion::create([
                'name' => 'Buy 1 Coffee Get 1 Free',
                'promotion_type' => Promotion::TYPE_BUY_X_FREE_Y,
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(1),
                'badge_text' => 'BOGO',
                'banner_color' => '#00BCD4',
                'is_active' => true,
                'is_featured' => false,
                'display_order' => 4,
                'promo_config' => [
                    'buy_quantity' => 1,
                    'buy_item_ids' => [$menuItems[3]->id],
                    'free_quantity' => 1,
                    'free_item_ids' => [$menuItems[3]->id],
                    'max_free_items' => 5,
                    'same_item' => true
                ],
                'terms_conditions' => 'Buy 1 get 1 free. Same item only. Max 5 free items per order.'
            ]);

            $buyXFreeY->menuItems()->attach([
                $menuItems[3]->id => ['quantity' => 1, 'is_free' => false]
            ]);

            $this->command->info("✓ Created: {$buyXFreeY->name}");
        }

        $this->command->info('');
        $this->command->info('✅ Test promotions seeded successfully!');
        $this->command->info('Total promotions: ' . Promotion::count());
    }
}
