<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\CustomerReward;
use App\Models\CheckinSetting;
use App\Models\User;
use App\Models\LoyaltyTier;
use App\Models\Order;
use App\Models\VoucherCollection;
use App\Models\SpecialEvent;
use App\Models\Achievement;
use App\Models\BonusPointChallenge;
use App\Models\CustomerVoucher;
use App\Models\VoucherTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RewardsController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Get available rewards (active rewards only)
            $allRewards = Reward::where('is_active', true)
                ->orderBy('points_required', 'asc')
                ->get();

            // Get customer profile
            $customerProfile = $user->customerProfile;

            if ($customerProfile) {
                // Add redemption count for each reward (for display purposes)
                foreach ($allRewards as $reward) {
                    $userRedemptions = CustomerReward::where('customer_profile_id', $customerProfile->id)
                        ->where('reward_id', $reward->id)
                        ->count();

                    $reward->user_redemptions_count = $userRedemptions;
                }
            }

            // Limit to first 4 for main display
            $availableRewards = $allRewards->take(4);
            $hasMoreRewards = $allRewards->count() > 4;

            // Get check-in settings for daily rewards
            $checkinSettings = CheckinSetting::first();

            // Calculate user tier based on spending
            $userSpending = $this->calculateUserSpending($user->id);
            $userTierInfo = $this->calculateUserTier($userSpending);

            // Get user's redeemed rewards for staff display
            if ($customerProfile) {
                $allRedeemedRewards = CustomerReward::where('customer_profile_id', $customerProfile->id)
                    ->with('reward')
                    ->whereHas('reward')
                    ->latest()
                    ->get();
            } else {
                $allRedeemedRewards = collect();
            }

            // Limit to first 2 for main display
            $redeemedRewards = $allRedeemedRewards->take(2);
            $hasMoreRedeemed = $allRedeemedRewards->count() > 2;

            // Load Voucher Collections (Spend & Earn)
            // Filter out already collected vouchers
            $collectedVoucherNames = collect();
            if ($customerProfile) {
                $collectedVoucherNames = CustomerVoucher::where('customer_profile_id', $customerProfile->id)
                    ->where('status', 'active')
                    ->with('voucherTemplate')
                    ->get()
                    ->pluck('voucherTemplate.name')
                    ->filter()
                    ->unique();
            }

            $voucherCollections = VoucherCollection::active()
                ->valid()
                ->whereNotIn('name', $collectedVoucherNames)
                ->orderBy('spending_requirement', 'asc')
                ->get();

            // Load Special Events
            $specialEvents = SpecialEvent::active()
                ->orderBy('created_at', 'desc')
                ->get();

            // Load Achievements
            $achievements = Achievement::active()
                ->orderBy('target_value', 'asc')
                ->get();

            // Load Bonus Point Challenges
            $bonusChallenges = BonusPointChallenge::active()
                ->valid()
                ->orderBy('bonus_points', 'desc')
                ->get();

            // Get user's vouchers
            $userVouchers = collect();
            if ($customerProfile) {
                $userVouchers = CustomerVoucher::where('customer_profile_id', $customerProfile->id)
                    ->where('status', 'active')
                    ->where(function($query) {
                        $query->whereNull('expiry_date')
                            ->orWhere('expiry_date', '>=', now());
                    })
                    ->with('voucherTemplate')
                    ->latest()
                    ->get();
            }

            return view('customer.rewards.index', compact(
                'user',
                'availableRewards',
                'hasMoreRewards',
                'allRewards',
                'checkinSettings',
                'userTierInfo',
                'userSpending',
                'redeemedRewards',
                'hasMoreRedeemed',
                'allRedeemedRewards',
                'voucherCollections',
                'specialEvents',
                'achievements',
                'bonusChallenges',
                'userVouchers'
            ));
        } else {
            // For visitors, show login prompt like orders page
            $guest = true;
            $user = null;
            $availableRewards = collect();

            return view('customer.rewards.index', compact('guest', 'user', 'availableRewards'));
        }
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'reward_id' => 'required|exists:rewards,id'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login to redeem rewards']);
        }

        // Check if user has customer profile
        $customerProfile = $user->customerProfile;
        if (!$customerProfile) {
            return response()->json(['success' => false, 'message' => 'Customer profile not found. Please contact support.']);
        }

        $reward = Reward::findOrFail($request->reward_id);

        // Check if reward is active
        if (!$reward->is_active) {
            return response()->json(['success' => false, 'message' => 'This reward is no longer available']);
        }

        // Check if user has enough points
        $userPoints = $user->points_balance ?? 0;
        if ($userPoints < $reward->points_required) {
            return response()->json(['success' => false, 'message' => 'Insufficient points']);
        }

        // Process redemption
        try {
            // Deduct points from user
            $user->decrement('points_balance', $reward->points_required);

            // Calculate expiry date based on expiry_days
            $expiryDate = $reward->expiry_days ? now()->addDays($reward->expiry_days) : null;

            // Create redemption record
            $customerReward = CustomerReward::create([
                'customer_profile_id' => $customerProfile->id,
                'reward_id' => $reward->id,
                'points_spent' => $reward->points_required,
                'status' => 'pending',
                'claimed_at' => now(),
                'expires_at' => $expiryDate
            ]);

            // If reward has voucher template, create a CustomerVoucher for cart usage
            if ($reward->voucher_template_id) {
                $voucherTemplate = $reward->voucherTemplate;

                if ($voucherTemplate) {
                    $customerVoucher = CustomerVoucher::create([
                        'customer_profile_id' => $customerProfile->id,
                        'voucher_template_id' => $voucherTemplate->id,
                        'source' => 'reward',
                        'status' => 'active',
                        'expiry_date' => $expiryDate
                    ]);

                    logger()->info('Voucher created from reward redemption', [
                        'customer_reward_id' => $customerReward->id,
                        'customer_voucher_id' => $customerVoucher->id,
                        'voucher_template_id' => $voucherTemplate->id,
                        'reward_id' => $reward->id
                    ]);
                }
            }

            $message = $this->getRedemptionMessage($reward);

            return response()->json([
                'success' => true,
                'message' => $message,
                'new_balance' => $user->fresh()->points_balance ?? 0
            ]);
        } catch (\Exception $e) {
            logger()->error('Reward redemption failed', [
                'error' => $e->getMessage(),
                'reward_id' => $reward->id,
                'user_id' => $user->id
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to redeem reward. Please try again.']);
        }
    }

    public function checkin(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login to check in']);
        }

        // Check if user already checked in today
        $today = Carbon::today();
        $lastCheckin = $user->last_checkin_date ? Carbon::parse($user->last_checkin_date) : null;

        if ($lastCheckin && $lastCheckin->isSameDay($today)) {
            return response()->json([
                'success' => false,
                'message' => 'You have already checked in today! Come back tomorrow! 😊'
            ]);
        }

        // Get checkin settings
        $checkinSettings = CheckinSetting::first();
        $dailyPoints = $checkinSettings ? $checkinSettings->daily_points : [25, 5, 5, 10, 10, 15, 20];

        // Calculate streak
        $currentStreak = $user->checkin_streak ?? 0;
        if ($lastCheckin && $lastCheckin->diffInDays($today) === 1) {
            // Consecutive day - continue streak
            $newStreak = ($currentStreak + 1) % 7; // Reset after 7 days
        } else {
            // Not consecutive or first checkin - start/restart streak
            $newStreak = 0;
        }

        // Get points for the NEW streak position (the day they're checking in for)
        $earnedPoints = $dailyPoints[$newStreak] ?? 5;

        // Update user data
        try {
            $user->increment('points_balance', $earnedPoints);
            $user->update([
                'last_checkin_date' => $today,
                'checkin_streak' => $newStreak
            ]);

            return response()->json([
                'success' => true,
                'message' => "🎉 Check-in successful! +{$earnedPoints} points earned!",
                'points_earned' => $earnedPoints,
                'new_balance' => $user->fresh()->points_balance,
                'streak' => $newStreak,
                'checked_in_today' => true
            ]);
        } catch (\Exception $e) {
            \Log::error('Check-in error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to process check-in: ' . $e->getMessage()]);
        }
    }

    private function getRedemptionMessage($reward)
    {
        return "Reward redeemed successfully! Please show this confirmation to our staff to claim your {$reward->title}.";
    }

    private function calculateUserSpending($userId)
    {
        return Order::where('user_id', $userId)
            ->where('payment_status', 'paid')
            ->sum('total_amount');
    }

    private function calculateUserTier($spending)
    {
        $tiers = LoyaltyTier::active()->ordered()->get();
        $currentTier = null;
        $nextTier = null;

        foreach ($tiers as $tier) {
            if ($spending >= $tier->minimum_spending) {
                $currentTier = $tier;
            } else {
                if (!$nextTier) {
                    $nextTier = $tier;
                }
                break;
            }
        }

        // If no current tier found, use the first tier
        if (!$currentTier) {
            $currentTier = $tiers->first();
        }

        return [
            'current' => $currentTier,
            'next' => $nextTier,
            'spending' => $spending,
            'progress' => $nextTier ? min(100, ($spending / $nextTier->minimum_spending) * 100) : 100,
            'amount_needed' => $nextTier ? max(0, $nextTier->minimum_spending - $spending) : 0
        ];
    }

    /**
     * Collect voucher from voucher collection
     */
    public function collectVoucher(Request $request)
    {
        $request->validate([
            'voucher_collection_id' => 'required|exists:voucher_collections,id'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login to collect vouchers']);
        }

        $customerProfile = $user->customerProfile;
        if (!$customerProfile) {
            return response()->json(['success' => false, 'message' => 'Customer profile not found']);
        }

        $voucherCollection = VoucherCollection::findOrFail($request->voucher_collection_id);

        // Check if user already collected this voucher
        $existingVoucher = CustomerVoucher::whereHas('voucherTemplate', function($query) use ($voucherCollection) {
                $query->where('name', $voucherCollection->name);
            })
            ->where('customer_profile_id', $customerProfile->id)
            ->where('status', 'active')
            ->first();

        if ($existingVoucher) {
            return response()->json([
                'success' => false,
                'message' => 'You have already collected this voucher!'
            ]);
        }

        // Check if user meets spending requirement
        $userSpending = $this->calculateUserSpending($user->id);

        if ($userSpending < $voucherCollection->spending_requirement) {
            $amountNeeded = $voucherCollection->spending_requirement - $userSpending;
            return response()->json([
                'success' => false,
                'message' => sprintf('Spend RM%.2f more to unlock this voucher!', $amountNeeded)
            ]);
        }

        // Find or create voucher template for this collection
        $voucherTemplate = \App\Models\VoucherTemplate::firstOrCreate(
            [
                'name' => $voucherCollection->name,
                'discount_type' => $voucherCollection->voucher_type === 'percentage' ? 'percentage' : 'fixed',
                'discount_value' => $voucherCollection->voucher_value
            ],
            [
                'title' => $voucherCollection->name,
                'description' => $voucherCollection->description ?? 'Voucher from collection',
                'minimum_spend' => $voucherCollection->spending_requirement,
                'expiry_days' => 30, // Default 30 days validity
                'max_uses_per_user' => 1,
                'is_active' => true
            ]
        );

        // Create customer voucher
        $expiryDate = $voucherCollection->valid_until
            ? Carbon::parse($voucherCollection->valid_until)
            : now()->addDays($voucherTemplate->expiry_days ?? 30);

        $customerVoucher = CustomerVoucher::create([
            'customer_profile_id' => $customerProfile->id,
            'voucher_template_id' => $voucherTemplate->id,
            'source' => 'collection',
            'status' => 'active',
            'expiry_date' => $expiryDate
        ]);

        logger()->info('Voucher collected', [
            'user_id' => $user->id,
            'voucher_collection_id' => $voucherCollection->id,
            'customer_voucher_id' => $customerVoucher->id,
            'voucher_template_id' => $voucherTemplate->id
        ]);

        return response()->json([
            'success' => true,
            'message' => sprintf('Congratulations! You\'ve collected %s voucher!', $voucherCollection->name),
            'spending' => $userSpending,
            'requirement' => $voucherCollection->spending_requirement,
            'voucher' => [
                'id' => $customerVoucher->id,
                'name' => $voucherTemplate->name,
                'expiry_date' => $expiryDate->format('M j, Y')
            ]
        ]);
    }
}
