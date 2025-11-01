<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\CustomerReward;
use App\Models\CheckinSetting;
use App\Models\User;
use App\Models\LoyaltyTier;
use App\Models\Order;
// PHASE 2.1: VoucherCollection deprecated - use VoucherTemplate with source_type='collection'
// use App\Models\VoucherCollection;
use App\Models\SpecialEvent;
use App\Models\Achievement;
use App\Models\BonusPointChallenge;
use App\Models\CustomerVoucher;
use App\Models\VoucherTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;

// PHASE 3: Import Loyalty Services
use App\Services\Loyalty\LoyaltyService;
use App\Services\Loyalty\RewardRedemptionService;
use App\Services\Loyalty\VoucherService;

class RewardsController extends Controller
{
    // PHASE 3: Inject services via constructor
    protected LoyaltyService $loyaltyService;
    protected RewardRedemptionService $rewardService;
    protected VoucherService $voucherService;

    public function __construct(
        LoyaltyService $loyaltyService,
        RewardRedemptionService $rewardService,
        VoucherService $voucherService
    ) {
        $this->loyaltyService = $loyaltyService;
        $this->rewardService = $rewardService;
        $this->voucherService = $voucherService;
    }
    public function index()
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Get customer profile
            $customerProfile = $user->customerProfile;

            // PHASE 7: Use RewardRedemptionService to get available rewards
            // This automatically filters by:
            // - Active status
            // - Tier requirements (tier-exclusive rewards)
            // - Usage limits
            // - Expiry dates
            // - Max redemptions
            $allRewards = $this->rewardService->getAvailableRewards($user, onlyAffordable: false);

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

            // PHASE 2.1: Use unified VoucherTemplate with source_type='collection'
            $voucherCollections = VoucherTemplate::collectionVouchers()
                ->active()
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

        // PHASE 3: Use RewardRedemptionService instead of direct model operations
        try {
            // Service handles all validation and business logic
            $customerReward = $this->rewardService->redeemReward($user, $reward);

            // If reward has voucher template, issue voucher
            if ($reward->voucher_template_id && $reward->voucherTemplate) {
                try {
                    $this->voucherService->issueVoucher(
                        $user,
                        $reward->voucherTemplate,
                        'reward'
                    );
                } catch (\Exception $e) {
                    // Log but don't fail the whole redemption
                    logger()->warning('Failed to issue voucher for reward', [
                        'customer_reward_id' => $customerReward->id,
                        'reward_id' => $reward->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $message = $this->getRedemptionMessage($reward);

            return response()->json([
                'success' => true,
                'message' => $message,
                'new_balance' => $this->loyaltyService->getBalance($user)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
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

        // PHASE 3: Use LoyaltyService for points award
        try {
            // Award points through service (handles transaction logging automatically)
            $this->loyaltyService->awardCheckInPoints($user, $earnedPoints);

            // Update user checkin data
            $user->update([
                'last_checkin_date' => $today,
                'checkin_streak' => $newStreak
            ]);

            return response()->json([
                'success' => true,
                'message' => "🎉 Check-in successful! +{$earnedPoints} points earned!",
                'points_earned' => $earnedPoints,
                'new_balance' => $this->loyaltyService->getBalance($user),
                'streak' => $newStreak,
                'checked_in_today' => true
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to process check-in: ' . $e->getMessage()
            ]);
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
            'voucher_collection_id' => 'required|exists:voucher_templates,id'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login to collect vouchers']);
        }

        $customerProfile = $user->customerProfile;
        if (!$customerProfile) {
            return response()->json(['success' => false, 'message' => 'Customer profile not found']);
        }

        $voucherTemplate = VoucherTemplate::collectionVouchers()
            ->findOrFail($request->voucher_collection_id);

        // PHASE 3: Use VoucherService for voucher issuance
        try {
            // Service handles all validation (spending requirement, usage limits, etc.)
            $customerVoucher = $this->voucherService->issueVoucher(
                $user,
                $voucherTemplate,
                'collection'
            );

            $expiryDate = $customerVoucher->expiry_date
                ? Carbon::parse($customerVoucher->expiry_date)
                : null;

            return response()->json([
                'success' => true,
                'message' => sprintf('Congratulations! You\'ve collected %s voucher!', $voucherTemplate->name),
                'voucher' => [
                    'id' => $customerVoucher->id,
                    'code' => $customerVoucher->voucher_code,
                    'name' => $voucherTemplate->name,
                    'expiry_date' => $expiryDate ? $expiryDate->format('M j, Y') : 'No expiry'
                ]
            ]);

        } catch (\Exception $e) {
            // Log full error for debugging
            \Log::error('Voucher collection failed', [
                'user_id' => $user->id,
                'template_id' => $request->voucher_collection_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
