<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\VoucherCollection;
use App\Models\SpecialEvent;
use App\Models\Achievement;
use App\Models\BonusPointChallenge;
use Carbon\Carbon;

class RewardsDynamicDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Voucher Collections
        $voucherCollections = [
            [
                'name' => 'RM10 OFF',
                'description' => 'Get RM10 off on orders above RM50',
                'spending_requirement' => 50.00,
                'voucher_type' => 'fixed',
                'voucher_value' => 10.00,
                'valid_until' => Carbon::now()->addMonths(3),
                'status' => 'active'
            ],
            [
                'name' => 'RM20 OFF',
                'description' => 'Get RM20 off on orders above RM100',
                'spending_requirement' => 100.00,
                'voucher_type' => 'fixed',
                'voucher_value' => 20.00,
                'valid_until' => Carbon::now()->addMonths(3),
                'status' => 'active'
            ],
            [
                'name' => 'RM30 OFF',
                'description' => 'Get RM30 off on orders above RM150',
                'spending_requirement' => 150.00,
                'voucher_type' => 'fixed',
                'voucher_value' => 30.00,
                'valid_until' => Carbon::now()->addMonths(3),
                'status' => 'active'
            ],
            [
                'name' => 'RM50 OFF',
                'description' => 'VIP spending reward - Get RM50 off on orders above RM200',
                'spending_requirement' => 200.00,
                'voucher_type' => 'fixed',
                'voucher_value' => 50.00,
                'valid_until' => Carbon::now()->addMonths(6),
                'status' => 'active'
            ],
        ];

        foreach ($voucherCollections as $voucher) {
            VoucherCollection::updateOrCreate(
                ['name' => $voucher['name']],
                $voucher
            );
        }

        // Seed Special Events
        $specialEvents = [
            [
                'name' => 'Weekend Special',
                'description' => '20% off on all orders during weekends! Valid Saturday & Sunday.',
                'start_date' => Carbon::now()->startOfWeek(),
                'end_date' => Carbon::now()->addMonths(3)->endOfWeek(),
                'points_multiplier' => 1.20,
                'is_active' => true
            ],
            [
                'name' => 'Happy Hour Deals',
                'description' => 'Get 15% off on orders placed between 2PM - 5PM daily!',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addMonths(2),
                'points_multiplier' => 1.15,
                'is_active' => true
            ],
            [
                'name' => 'Birthday Month Special',
                'description' => 'It\'s your birthday month? Enjoy a free dessert with any main course!',
                'start_date' => Carbon::now(),
                'end_date' => Carbon::now()->addYear(),
                'points_multiplier' => 1.50,
                'is_active' => true
            ],
        ];

        foreach ($specialEvents as $event) {
            SpecialEvent::updateOrCreate(
                ['name' => $event['name']],
                $event
            );
        }

        // Seed Achievements
        $achievements = [
            [
                'name' => 'First Timer',
                'description' => 'Complete your first order',
                'target_type' => 'orders',
                'target_value' => 1,
                'reward_points' => 50,
                'status' => 'active'
            ],
            [
                'name' => 'Regular Customer',
                'description' => 'Complete 10 orders',
                'target_type' => 'orders',
                'target_value' => 10,
                'reward_points' => 200,
                'status' => 'active'
            ],
            [
                'name' => 'Food Enthusiast',
                'description' => 'Complete 25 orders',
                'target_type' => 'orders',
                'target_value' => 25,
                'reward_points' => 500,
                'status' => 'active'
            ],
            [
                'name' => 'Big Spender',
                'description' => 'Spend RM500 in total',
                'target_type' => 'spending',
                'target_value' => 500,
                'reward_points' => 300,
                'status' => 'active'
            ],
            [
                'name' => 'VIP Customer',
                'description' => 'Spend RM1000 in total',
                'target_type' => 'spending',
                'target_value' => 1000,
                'reward_points' => 1000,
                'status' => 'active'
            ],
            [
                'name' => 'Review Master',
                'description' => 'Leave 5 reviews',
                'target_type' => 'reviews',
                'target_value' => 5,
                'reward_points' => 150,
                'status' => 'active'
            ],
        ];

        foreach ($achievements as $achievement) {
            Achievement::updateOrCreate(
                ['name' => $achievement['name']],
                $achievement
            );
        }

        // Seed Bonus Point Challenges
        $bonusChallenges = [
            [
                'name' => 'First Order Bonus',
                'description' => 'Complete your first order to earn bonus points!',
                'condition' => 'Place your first order',
                'bonus_points' => 50,
                'end_date' => null,
                'status' => 'active'
            ],
            [
                'name' => 'Review & Rate',
                'description' => 'Leave a 5-star review after your meal',
                'condition' => 'Leave a 5-star review',
                'bonus_points' => 25,
                'end_date' => null,
                'status' => 'active'
            ],
            [
                'name' => 'Social Share',
                'description' => 'Share your experience on social media and tag us',
                'condition' => 'Share on social media',
                'bonus_points' => 15,
                'end_date' => null,
                'status' => 'active'
            ],
            [
                'name' => 'Weekend Warrior',
                'description' => 'Order 3 times during weekends this month',
                'condition' => 'Complete 3 weekend orders',
                'bonus_points' => 100,
                'end_date' => Carbon::now()->endOfMonth(),
                'status' => 'active'
            ],
            [
                'name' => 'Early Bird Special',
                'description' => 'Place an order before 11 AM for 5 days',
                'condition' => 'Order before 11 AM (5 times)',
                'bonus_points' => 75,
                'end_date' => Carbon::now()->addDays(30),
                'status' => 'active'
            ],
        ];

        foreach ($bonusChallenges as $challenge) {
            BonusPointChallenge::updateOrCreate(
                ['name' => $challenge['name']],
                $challenge
            );
        }

        $this->command->info('✅ Rewards dynamic data seeded successfully!');
        $this->command->info('   - Voucher Collections: ' . VoucherCollection::count());
        $this->command->info('   - Special Events: ' . SpecialEvent::count());
        $this->command->info('   - Achievements: ' . Achievement::count());
        $this->command->info('   - Bonus Challenges: ' . BonusPointChallenge::count());
    }
}
