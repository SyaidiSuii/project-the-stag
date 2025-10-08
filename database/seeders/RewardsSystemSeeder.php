<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    User,
    CustomerProfile,
    VoucherTemplate,
    Reward,
    CustomerReward,
    CustomerVoucher,
    LoyaltyTransaction,
    Promotion,
    UserPromotion,
    HappyHourDeal,
    HappyHourDealItem,
    SpecialEvent,
    LoyaltyTier,
    Achievement,
    BonusPointChallenge,
    VoucherCollection,
    MenuItem
};
use Carbon\Carbon;

class RewardsSystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Loyalty Tiers
        $bronzeTier = LoyaltyTier::create([
            'name' => 'Bronze',
            'minimum_spending' => 0,
            'points_multiplier' => 1.0,
            'benefits' => 'Standard points earning, Birthday reward',
            'is_active' => true
        ]);

        $silverTier = LoyaltyTier::create([
            'name' => 'Silver',
            'minimum_spending' => 500,
            'points_multiplier' => 1.5,
            'benefits' => '1.5x points, Free delivery, Priority support',
            'is_active' => true
        ]);

        $goldTier = LoyaltyTier::create([
            'name' => 'Gold',
            'minimum_spending' => 1500,
            'points_multiplier' => 2.0,
            'benefits' => '2x points, Free delivery, Exclusive menu access, Monthly voucher',
            'is_active' => true
        ]);

        // 2. Create Sample Users & Customer Profiles
        $ali = User::create([
            'name' => 'Ali Rahman',
            'email' => 'ali@email.com',
            'password' => bcrypt('password123'),
            'points_balance' => 120
        ]);

        CustomerProfile::create([
            'user_id' => $ali->id,
            'name' => 'Ali Rahman',
            'date_of_birth' => '1990-05-15',
            'address' => '123 Jalan Merdeka, Kuala Lumpur',
            'loyalty_points' => 120,
            'loyalty_tier_id' => $bronzeTier->id,
            'total_spent' => 350.00,
            'visit_count' => 12,
            'last_visit' => Carbon::now()->subDays(3)
        ]);

        $siti = User::create([
            'name' => 'Siti Nurhaliza',
            'email' => 'siti@email.com',
            'password' => bcrypt('password123'),
            'points_balance' => 580
        ]);

        CustomerProfile::create([
            'user_id' => $siti->id,
            'name' => 'Siti Nurhaliza',
            'date_of_birth' => '1988-03-22',
            'address' => '456 Jalan Ampang, Kuala Lumpur',
            'loyalty_points' => 580,
            'loyalty_tier_id' => $silverTier->id,
            'total_spent' => 890.50,
            'visit_count' => 28,
            'last_visit' => Carbon::now()->subDays(1)
        ]);

        $ahmad = User::create([
            'name' => 'Ahmad Zakaria',
            'email' => 'ahmad@email.com',
            'password' => bcrypt('password123'),
            'points_balance' => 1250
        ]);

        CustomerProfile::create([
            'user_id' => $ahmad->id,
            'name' => 'Ahmad Zakaria',
            'date_of_birth' => '1985-11-08',
            'address' => '789 Jalan Bukit Bintang, Kuala Lumpur',
            'loyalty_points' => 1250,
            'loyalty_tier_id' => $goldTier->id,
            'total_spent' => 2150.00,
            'visit_count' => 45,
            'last_visit' => Carbon::now()
        ]);

        // 3. Create Voucher Templates
        $tenPercentVoucher = VoucherTemplate::create([
            'name' => '10% OFF Voucher',
            'discount_type' => 'percentage',
            'discount_value' => 10.00,
            'expiry_days' => 30
        ]);

        $freeDeliveryVoucher = VoucherTemplate::create([
            'name' => 'Free Delivery',
            'discount_type' => 'fixed',
            'discount_value' => 8.00,
            'expiry_days' => 14
        ]);

        $rm20OffVoucher = VoucherTemplate::create([
            'name' => 'RM20 OFF',
            'discount_type' => 'fixed',
            'discount_value' => 20.00,
            'expiry_days' => 21
        ]);

        // 4. Create Rewards (Admin Define Catalog)
        $freeCoffeeReward = Reward::create([
            'title' => 'Free Coffee',
            'description' => 'Redeem a free regular coffee',
            'reward_type' => 'voucher',
            'points_required' => 100,
            'voucher_template_id' => $tenPercentVoucher->id,
            'expiry_days' => 30,
            'is_active' => true
        ]);

        $freeDeliveryReward = Reward::create([
            'title' => 'Free Delivery',
            'description' => 'Get free delivery on your next order',
            'reward_type' => 'voucher',
            'points_required' => 50,
            'voucher_template_id' => $freeDeliveryVoucher->id,
            'expiry_days' => 14,
            'is_active' => true
        ]);

        $tierUpgradeReward = Reward::create([
            'title' => 'Gold Member Upgrade',
            'description' => 'Upgrade to Gold tier membership',
            'reward_type' => 'tier_upgrade',
            'points_required' => 500,
            'voucher_template_id' => null,
            'expiry_days' => null,
            'is_active' => true
        ]);

        $bigDiscountReward = Reward::create([
            'title' => 'RM20 Discount',
            'description' => 'RM20 off on orders above RM100',
            'reward_type' => 'voucher',
            'points_required' => 200,
            'voucher_template_id' => $rm20OffVoucher->id,
            'expiry_days' => 21,
            'is_active' => true
        ]);

        // 5. Create Customer Rewards (Customer Claim Rewards)
        CustomerReward::create([
            'customer_profile_id' => $ali->customerProfile->id,
            'reward_id' => $freeCoffeeReward->id,
            'status' => 'active',
            'expiry_date' => Carbon::now()->addDays(30)
        ]);

        CustomerReward::create([
            'customer_profile_id' => $siti->customerProfile->id,
            'reward_id' => $freeDeliveryReward->id,
            'status' => 'redeemed',
            'expiry_date' => Carbon::now()->addDays(14),
            'redeemed_at' => Carbon::now()->subDays(2)
        ]);

        CustomerReward::create([
            'customer_profile_id' => $ahmad->customerProfile->id,
            'reward_id' => $bigDiscountReward->id,
            'status' => 'active',
            'expiry_date' => Carbon::now()->addDays(21)
        ]);

        // 6. Create Customer Vouchers (Issued to Customers)
        CustomerVoucher::create([
            'customer_profile_id' => $ali->customerProfile->id,
            'voucher_template_id' => $tenPercentVoucher->id,
            'voucher_code' => 'ALI001',
            'status' => 'active',
            'expiry_date' => Carbon::now()->addDays(30)
        ]);

        CustomerVoucher::create([
            'customer_profile_id' => $siti->customerProfile->id,
            'voucher_template_id' => $freeDeliveryVoucher->id,
            'voucher_code' => 'SITI002',
            'status' => 'redeemed',
            'expiry_date' => Carbon::now()->addDays(14),
            'redeemed_at' => Carbon::now()->subDays(2)
        ]);

        CustomerVoucher::create([
            'customer_profile_id' => $ahmad->customerProfile->id,
            'voucher_template_id' => $rm20OffVoucher->id,
            'voucher_code' => 'AHMAD003',
            'status' => 'active',
            'expiry_date' => Carbon::now()->addDays(21)
        ]);

        // 7. Create Loyalty Transactions (Log Points Movement)
        LoyaltyTransaction::create([
            'customer_profile_id' => $ali->customerProfile->id,
            'transaction_type' => 'earn_points',
            'points_change' => 20,
            'balance_after' => 120,
            'description' => 'Earned from Order #1234'
        ]);

        LoyaltyTransaction::create([
            'customer_profile_id' => $ali->customerProfile->id,
            'transaction_type' => 'redeem_points',
            'points_change' => -100,
            'balance_after' => 20,
            'description' => 'Redeemed: Free Coffee'
        ]);

        LoyaltyTransaction::create([
            'customer_profile_id' => $siti->customerProfile->id,
            'transaction_type' => 'earn_points',
            'points_change' => 50,
            'balance_after' => 580,
            'description' => 'Earned from Order #1235'
        ]);

        LoyaltyTransaction::create([
            'customer_profile_id' => $siti->customerProfile->id,
            'transaction_type' => 'redeem_voucher',
            'points_change' => 0,
            'balance_after' => 580,
            'description' => 'Used voucher: Free Delivery'
        ]);

        LoyaltyTransaction::create([
            'customer_profile_id' => $ahmad->customerProfile->id,
            'transaction_type' => 'earn_points',
            'points_change' => 100,
            'balance_after' => 1250,
            'description' => 'Earned from Order #1236'
        ]);

        // 8. Create Promotions
        $festivePromo = Promotion::create([
            'name' => 'Festive Sale',
            'promo_code' => 'FESTIVE2025',
            'discount_type' => 'percentage',
            'discount_value' => 25.00,
            'minimum_order_value' => 80.00,
            'start_date' => Carbon::now()->subDays(10),
            'end_date' => Carbon::now()->addDays(20),
            'is_active' => true
        ]);

        $newYearPromo = Promotion::create([
            'name' => 'New Year Special',
            'promo_code' => 'NEWYEAR2025',
            'discount_type' => 'fixed',
            'discount_value' => 15.00,
            'minimum_order_value' => 50.00,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(30),
            'is_active' => true
        ]);

        // 9. Link Promotions with Users
        UserPromotion::create([
            'user_id' => $ali->id,
            'promotion_id' => $festivePromo->id,
            'status' => 'active'
        ]);

        UserPromotion::create([
            'user_id' => $siti->id,
            'promotion_id' => $newYearPromo->id,
            'status' => 'used',
            'used_at' => Carbon::now()->subDays(5)
        ]);

        // 10. Create Happy Hour Deals (if menu items exist)
        $menuItems = MenuItem::limit(3)->get();
        if ($menuItems->isNotEmpty()) {
            $happyBurgerDeal = HappyHourDeal::create([
                'name' => 'Happy Burger Hour',
                'discount_percentage' => 20.00,
                'start_time' => '17:00:00',
                'end_time' => '19:00:00',
                'days_of_week' => json_encode(['Monday', 'Tuesday', 'Wednesday']),
                'is_active' => true
            ]);

            foreach ($menuItems as $item) {
                HappyHourDealItem::create([
                    'happy_hour_deal_id' => $happyBurgerDeal->id,
                    'menu_item_id' => $item->id
                ]);
            }
        }

        // 11. Create Special Events
        SpecialEvent::create([
            'name' => 'Birthday Party',
            'description' => 'Birthday celebration for 10 people',
            'start_date' => Carbon::now()->addDays(10),
            'end_date' => Carbon::now()->addDays(10),
            'points_multiplier' => 1.5,
            'is_active' => true
        ]);

        SpecialEvent::create([
            'name' => 'Corporate Event',
            'description' => 'Company dinner for 30 people',
            'start_date' => Carbon::now()->addDays(15),
            'end_date' => Carbon::now()->addDays(15),
            'points_multiplier' => 2.0,
            'is_active' => true
        ]);

        // 12. Create Achievements
        Achievement::create([
            'name' => 'First Order',
            'description' => 'Complete your first order',
            'target_type' => 'order_count',
            'target_value' => 1,
            'reward_points' => 50,
            'status' => 'active'
        ]);

        Achievement::create([
            'name' => 'Regular Customer',
            'description' => 'Make 10 orders',
            'target_type' => 'order_count',
            'target_value' => 10,
            'reward_points' => 100,
            'status' => 'active'
        ]);

        Achievement::create([
            'name' => 'Big Spender',
            'description' => 'Spend RM500 in total',
            'target_type' => 'total_spent',
            'target_value' => 500,
            'reward_points' => 200,
            'status' => 'active'
        ]);

        // 13. Create Bonus Point Challenges
        BonusPointChallenge::create([
            'name' => 'Weekend Warrior',
            'description' => 'Order 3 times this weekend',
            'condition' => 'weekend_orders_count >= 3',
            'bonus_points' => 150,
            'status' => 'active'
        ]);

        BonusPointChallenge::create([
            'name' => 'Big Order Challenge',
            'description' => 'Place an order above RM200',
            'condition' => 'order_total > 200',
            'bonus_points' => 100,
            'status' => 'active'
        ]);

        // 14. Create Voucher Collections
        VoucherCollection::create([
            'name' => 'Summer Collection',
            'description' => 'Special summer vouchers for hot days',
            'spending_requirement' => 100.00,
            'voucher_type' => 'percentage',
            'voucher_value' => 15.00,
            'valid_until' => Carbon::now()->addMonths(3),
            'status' => 'active'
        ]);

        VoucherCollection::create([
            'name' => 'Birthday Specials',
            'description' => 'Exclusive birthday month vouchers',
            'spending_requirement' => 50.00,
            'voucher_type' => 'fixed',
            'voucher_value' => 20.00,
            'valid_until' => Carbon::now()->addMonths(6),
            'status' => 'active'
        ]);

        $this->command->info('✅ Rewards System seeded successfully with real data!');
    }
}
