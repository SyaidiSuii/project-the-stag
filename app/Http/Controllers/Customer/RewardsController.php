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

            // Get all tiers for the modal
            $allTiers = LoyaltyTier::active()->ordered()->get();

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
                'userVouchers',
                'allTiers'
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
        // PHASE 8: Consolidated to customer_rewards only - no more auto-voucher issuance
        try {
            // Service handles all validation and business logic
            $customerReward = $this->rewardService->redeemReward($user, $reward);

            // Note: Voucher-type rewards are now stored directly in customer_rewards
            // No separate customer_voucher record is created

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
            $newStreak = $currentStreak + 1;
        } else {
            // Not consecutive or first checkin - start/restart streak
            $newStreak = 1; // Start from 1, not 0 (Day 1 check-in)
        }

        // Get points based on position in 7-day cycle (0-6)
        $cyclePosition = ($newStreak - 1) % 7; // Map streak 1→0, 2→1, ..., 7→6, 8→0, etc.
        $earnedPoints = $dailyPoints[$cyclePosition] ?? 5;

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
                'checked_in_today' => true,
                'is_milestone' => $checkinSettings && isset($checkinSettings->streak_milestones) 
                    ? in_array($newStreak, $checkinSettings->streak_milestones) 
                    : in_array($newStreak, [7, 14, 30, 60, 100])
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

    /**
     * Claim bonus point challenge
     */
    public function claimBonusChallenge(Request $request)
    {
        $request->validate([
            'challenge_id' => 'required|exists:bonus_point_challenges,id'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login to claim bonus points']);
        }

        $challenge = \App\Models\BonusPointChallenge::findOrFail($request->challenge_id);

        // Use model method to check eligibility
        $eligibility = $challenge->isEligibleFor($user);
        if (!$eligibility['eligible']) {
            return response()->json([
                'success' => false,
                'message' => $eligibility['reason']
            ]);
        }

        // Award points using LoyaltyService and record claim
        try {
            \DB::transaction(function() use ($user, $challenge) {
                // Award points
                $this->loyaltyService->awardPoints(
                    $user,
                    $challenge->bonus_points,
                    'bonus_challenge',
                    "Bonus Challenge: {$challenge->name}"
                );

                // Record the claim
                \App\Models\UserBonusChallengeCall::create([
                    'user_id' => $user->id,
                    'bonus_point_challenge_id' => $challenge->id,
                    'points_awarded' => $challenge->bonus_points,
                ]);

                // Increment total claims counter
                $challenge->increment('current_claims');
            });

            return response()->json([
                'success' => true,
                'message' => sprintf('Congratulations! You earned %d bonus points from "%s"!', $challenge->bonus_points, $challenge->name),
                'points_earned' => $challenge->bonus_points,
                'new_balance' => $this->loyaltyService->getBalance($user)
            ]);

        } catch (\Exception $e) {
            \Log::error('Bonus challenge claim failed', [
                'user_id' => $user->id,
                'challenge_id' => $request->challenge_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to claim bonus points: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Mark a customer reward as pending when user applies it to cart
     */
    public function markAsPending(Request $request)
    {
        $request->validate([
            'redemption_id' => 'required|exists:customer_rewards,id'
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login']);
        }

        try {
            $customerReward = CustomerReward::findOrFail($request->redemption_id);

            // Verify this reward belongs to the current user
            $customerProfile = $user->customerProfile;
            if (!$customerProfile || $customerReward->customer_profile_id !== $customerProfile->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access to this reward'
                ]);
            }

            // Update status to pending (reward is in cart but not yet ordered)
            $customerReward->update(['status' => 'pending']);

            \Log::info('Customer reward marked as pending', [
                'customer_reward_id' => $customerReward->id,
                'user_id' => $user->id,
                'reward_title' => $customerReward->reward->title ?? 'Unknown'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Reward status updated'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to mark reward as pending', [
                'user_id' => $user->id,
                'redemption_id' => $request->redemption_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update reward status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Apply voucher from customer reward to cart
     * Used for voucher-type rewards from admin
     */
    public function applyVoucherFromReward(Request $request)
    {
        try {
            $request->validate([
                'customer_reward_id' => 'required|exists:customer_rewards,id'
            ]);

            $user = Auth::user();
            if (!$user || !$user->customerProfile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please login to apply voucher'
                ], 401);
            }

            // Find CustomerReward
            $customerReward = CustomerReward::with('reward.voucherTemplate')
                ->where('customer_profile_id', $user->customerProfile->id)
                ->findOrFail($request->customer_reward_id);

            // Check if reward is active
            if ($customerReward->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'This reward is no longer active'
                ], 400);
            }

            // Check if reward is voucher type
            if ($customerReward->reward->reward_type !== 'voucher') {
                return response()->json([
                    'success' => false,
                    'message' => 'This reward is not a voucher type'
                ], 400);
            }

            // Check if expired
            if ($customerReward->expires_at && $customerReward->expires_at < now()) {
                return response()->json([
                    'success' => false,
                    'message' => 'This reward has expired'
                ], 400);
            }

            // FIX: If redemption_code is missing, create CustomerVoucher on-the-fly
            $customerVoucher = null;

            if ($customerReward->redemption_code) {
                // Try to find existing CustomerVoucher by voucher_code
                $customerVoucher = CustomerVoucher::where('customer_profile_id', $user->customerProfile->id)
                    ->where('voucher_code', $customerReward->redemption_code)
                    ->where('status', 'active')
                    ->first();
            }

            // If no CustomerVoucher exists, create one now
            if (!$customerVoucher && $customerReward->reward->voucher_template_id) {
                \Log::info('Creating CustomerVoucher on-the-fly for voucher-type reward', [
                    'customer_reward_id' => $customerReward->id,
                    'reward_id' => $customerReward->reward_id,
                    'voucher_template_id' => $customerReward->reward->voucher_template_id,
                ]);

                // Generate unique voucher code
                $voucherCode = 'RWD-' . strtoupper(uniqid());

                // Create CustomerVoucher record
                $customerVoucher = CustomerVoucher::create([
                    'customer_profile_id' => $user->customerProfile->id,
                    'voucher_template_id' => $customerReward->reward->voucher_template_id,
                    'voucher_code' => $voucherCode,
                    'status' => 'active',
                    'source' => 'reward',
                    'expiry_date' => $customerReward->expires_at ? $customerReward->expires_at->toDateString() : null,
                    'redeemed_at' => null,
                ]);

                // Update CustomerReward with the voucher code
                $customerReward->update([
                    'redemption_code' => $voucherCode
                ]);

                \Log::info('CustomerVoucher created on-the-fly', [
                    'customer_voucher_id' => $customerVoucher->id,
                    'voucher_code' => $voucherCode,
                ]);
            }

            if (!$customerVoucher) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to create voucher. Please contact support.'
                ], 500);
            }

            // Return voucher data for frontend to call CartController::applyVoucher
            return response()->json([
                'success' => true,
                'message' => 'Voucher found',
                'voucher_id' => $customerVoucher->id,
                'voucher_code' => $customerVoucher->voucher_code,
                'voucher_name' => $customerReward->reward->voucherTemplate->name ?? 'Reward Voucher'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to apply voucher from reward', [
                'user_id' => $user->id ?? 'unknown',
                'customer_reward_id' => $request->customer_reward_id ?? 'missing',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to apply voucher: ' . $e->getMessage()
            ], 500);
        }
    }
}
