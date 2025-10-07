<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Reward, CustomerReward, User, Achievement, VoucherCollection, BonusPointChallenge, CheckinSetting, SpecialEvent, RewardsContent, LoyaltyTier, MenuItem, VoucherTemplate};

class RewardsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|manager');
    }

    public function index()
    {
        // Rewards data
        $rewards = Reward::withCount('customerRewards')
            ->orderBy('points_required')
            ->get();

        // Statistics for redemptions and members
        $redemptions = CustomerReward::with(['customerProfile.user', 'reward'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $members = User::whereNotNull('points_balance')
            ->orderBy('points_balance', 'desc')
            ->limit(20)
            ->get();

        // Rewards system data
        $checkinSettings = CheckinSetting::first();
        $specialEvents = SpecialEvent::orderBy('created_at', 'desc')->get();
        $rewardsContent = RewardsContent::first();
        $loyaltyTiers = LoyaltyTier::active()->ordered()->get();

        // Additional sections data
        $achievements = Achievement::orderBy('created_at', 'desc')->get();
        $voucherCollections = VoucherCollection::orderBy('created_at', 'desc')->get();
        $bonusPointsChallenges = BonusPointChallenge::orderBy('created_at', 'desc')->get();
        $voucherTemplates = VoucherTemplate::withCount('rewards')->orderBy('created_at', 'desc')->get();

        // Menu items for promotions
        $menuItems = MenuItem::where('is_available', true)->orderBy('name')->get();

        return view('admin.rewards.index', compact(
            'rewards', 'redemptions', 'members', 'checkinSettings', 'specialEvents',
            'rewardsContent', 'loyaltyTiers', 'achievements', 'voucherCollections',
            'bonusPointsChallenges', 'menuItems', 'voucherTemplates'
        ));
    }

    public function create()
    {
        return view('admin.rewards.form');
    }

    public function edit(Reward $reward)
    {
        return view('admin.rewards.form', compact('reward'));
    }

    // === REWARDS SECTION (New RESTful Methods) ===
    public function rewardsIndex()
    {
        $rewards = Reward::withCount('customerRewards')
            ->orderBy('points_required')
            ->get();

        return view('admin.rewards.rewards.index', compact('rewards'));
    }

    public function rewardsCreate()
    {
        return view('admin.rewards.rewards.form');
    }

    public function rewardsStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'reward_type' => 'required|in:points,voucher,tier_upgrade',
            'points_required' => 'required|integer|min:0',
            'voucher_template_id' => 'nullable|exists:voucher_templates,id',
            'expiry_days' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only([
            'title', 'description', 'reward_type', 'points_required',
            'voucher_template_id', 'expiry_days'
        ]);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        Reward::create($data);

        return redirect()->route('admin.rewards.rewards.index')
            ->with('success', 'Reward created successfully!');
    }

    public function rewardsEdit(Reward $reward)
    {
        return view('admin.rewards.rewards.form', compact('reward'));
    }

    public function rewardsUpdate(Request $request, Reward $reward)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'reward_type' => 'required|in:points,voucher,tier_upgrade',
            'points_required' => 'required|integer|min:0',
            'voucher_template_id' => 'nullable|exists:voucher_templates,id',
            'expiry_days' => 'nullable|integer|min:1',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only([
            'title', 'description', 'reward_type', 'points_required',
            'voucher_template_id', 'expiry_days'
        ]);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        $reward->update($data);

        return redirect()->route('admin.rewards.rewards.index')
            ->with('success', 'Reward updated successfully!');
    }

    public function rewardsDestroy(Reward $reward)
    {
        // Delete image if exists
        if ($reward->image_url) {
            \Storage::disk('public')->delete($reward->image_url);
        }

        $reward->delete();

        return redirect()->route('admin.rewards.rewards.index')
            ->with('success', 'Reward deleted successfully!');
    }

    // Reward Management
    public function storeReward(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'points_required' => 'required|integer|min:1',
            'is_active' => 'nullable|boolean',
            'max_redemptions_per_customer' => 'nullable|integer|min:1',
        ]);

        Reward::create([
            'title' => $request->title,
            'description' => $request->description,
            'points_required' => $request->points_required,
            'is_active' => $request->is_active ?? true,
            'max_redemptions_per_customer' => $request->max_redemptions_per_customer,
        ]);

        return response()->json(['success' => true, 'message' => 'Reward added successfully']);
    }

    public function updateReward(Request $request, Reward $reward)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'points_required' => 'required|integer|min:1',
                'is_active' => 'required|boolean',
                'max_redemptions_per_customer' => 'nullable|integer|min:1',
            ]);

            $reward->update([
                'title' => $request->title,
                'description' => $request->description,
                'points_required' => $request->points_required,
                'is_active' => $request->is_active,
                'max_redemptions_per_customer' => $request->max_redemptions_per_customer,
            ]);

            return response()->json(['success' => true, 'message' => 'Reward updated successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update reward: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroyReward(Reward $reward)
    {
        $reward->delete();
        return response()->json(['success' => true, 'message' => 'Reward deleted successfully']);
    }

    public function toggleReward(Reward $reward)
    {
        $newStatus = !$reward->is_active;
        $reward->update(['is_active' => $newStatus]);

        $message = $newStatus ? 'Reward enabled successfully' : 'Reward disabled successfully';
        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $newStatus
        ]);
    }

    // Loyalty Tier Management
    public function storeLoyaltyTier(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'minimum_spending' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        LoyaltyTier::create([
            'name' => $request->name,
            'minimum_spending' => $request->minimum_spending,
            'color' => $request->color ?? '#6366f1',
            'icon' => $request->icon ?? 'fas fa-star',
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Loyalty tier added successfully']);
    }

    public function updateLoyaltyTier(Request $request, LoyaltyTier $tier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'minimum_spending' => 'required|numeric|min:0',
            'color' => 'nullable|string|max:20',
            'icon' => 'nullable|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $tier->update([
            'name' => $request->name,
            'minimum_spending' => $request->minimum_spending,
            'color' => $request->color ?? $tier->color,
            'icon' => $request->icon ?? $tier->icon,
            'sort_order' => $request->sort_order ?? $tier->sort_order,
        ]);

        return response()->json(['success' => true, 'message' => 'Loyalty tier updated successfully']);
    }

    public function destroyLoyaltyTier(LoyaltyTier $tier)
    {
        $tier->delete();
        return response()->json(['success' => true, 'message' => 'Loyalty tier deleted successfully']);
    }

    public function toggleLoyaltyTier(LoyaltyTier $tier)
    {
        $tier->update(['is_active' => !$tier->is_active]);
        $message = $tier->is_active ? 'Loyalty tier enabled successfully' : 'Loyalty tier disabled successfully';

        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $tier->is_active
        ]);
    }

    // Achievement Management
    public function storeAchievement(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_type' => 'required|string|max:50',
            'target_value' => 'required|integer|min:1',
            'reward_points' => 'required|integer|min:1',
        ]);

        Achievement::create([
            'name' => $request->name,
            'description' => $request->description,
            'target_type' => $request->target_type,
            'target_value' => $request->target_value,
            'reward_points' => $request->reward_points,
            'status' => 'active',
        ]);

        return response()->json(['success' => true, 'message' => 'Achievement added successfully']);
    }

    public function updateAchievement(Request $request, Achievement $achievement)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'target_type' => 'required|string|max:50',
            'target_value' => 'required|integer|min:1',
            'reward_points' => 'required|integer|min:1',
            'status' => 'required|in:active,inactive',
        ]);

        $achievement->update($request->all());

        return response()->json(['success' => true, 'message' => 'Achievement updated successfully']);
    }

    public function destroyAchievement(Achievement $achievement)
    {
        $achievement->delete();
        return response()->json(['success' => true, 'message' => 'Achievement deleted successfully']);
    }

    // Bonus Point Challenge Management
    public function storeBonusPointChallenge(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'condition' => 'required|string|max:255',
            'bonus_points' => 'required|integer|min:1',
            'end_date' => 'nullable|date|after:today',
        ]);

        BonusPointChallenge::create([
            'name' => $request->name,
            'description' => $request->description,
            'condition' => $request->condition,
            'bonus_points' => $request->bonus_points,
            'end_date' => $request->end_date,
            'status' => 'active',
        ]);

        return response()->json(['success' => true, 'message' => 'Bonus point challenge added successfully']);
    }

    public function updateBonusPointChallenge(Request $request, BonusPointChallenge $bonusPointChallenge)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'condition' => 'required|string|max:255',
            'bonus_points' => 'required|integer|min:1',
            'end_date' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);

        $bonusPointChallenge->update($request->all());

        return response()->json(['success' => true, 'message' => 'Bonus point challenge updated successfully']);
    }

    public function destroyBonusPointChallenge(BonusPointChallenge $bonusPointChallenge)
    {
        $bonusPointChallenge->delete();
        return response()->json(['success' => true, 'message' => 'Bonus point challenge deleted successfully']);
    }

    // Voucher Collection Management
    public function storeVoucherCollection(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'spending_requirement' => 'required|numeric|min:0',
            'voucher_type' => 'required|in:percentage,fixed_amount',
            'voucher_value' => 'required|numeric|min:0',
            'valid_until' => 'nullable|date|after:today',
        ]);

        VoucherCollection::create([
            'name' => $request->name,
            'spending_requirement' => $request->spending_requirement,
            'voucher_type' => $request->voucher_type,
            'voucher_value' => $request->voucher_value,
            'valid_until' => $request->valid_until,
            'status' => 'active',
        ]);

        return response()->json(['success' => true, 'message' => 'Voucher collection added successfully']);
    }

    public function updateVoucherCollection(Request $request, VoucherCollection $voucherCollection)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'spending_requirement' => 'required|numeric|min:0',
            'voucher_type' => 'required|in:percentage,fixed_amount',
            'voucher_value' => 'required|numeric|min:0',
            'valid_until' => 'nullable|date',
            'status' => 'required|in:active,inactive',
        ]);

        $voucherCollection->update($request->all());

        return response()->json(['success' => true, 'message' => 'Voucher collection updated successfully']);
    }

    public function destroyVoucherCollection(VoucherCollection $voucherCollection)
    {
        $voucherCollection->delete();
        return response()->json(['success' => true, 'message' => 'Voucher collection deleted successfully']);
    }

    // Mark Redemption as Redeemed
    public function markRedemptionAsRedeemed(CustomerReward $redemption)
    {
        $redemption->update(['status' => 'redeemed']);
        return response()->json(['success' => true, 'message' => 'Redemption marked as redeemed']);
    }

    // Settings Management
    public function updateCheckinSettings(Request $request)
    {
        $request->validate([
            'daily_points' => 'required|array',
            'daily_points.*' => 'required|integer|min:1',
        ]);

        CheckinSetting::updateOrCreate(
            ['id' => 1],
            ['daily_points' => $request->daily_points]
        );

        return response()->json(['success' => true, 'message' => 'Check-in settings updated successfully']);
    }

    public function updateRewardsContent(Request $request)
    {
        $request->validate([
            'main_title' => 'required|string|max:255',
            'points_label' => 'required|string|max:100',
            'checkin_header' => 'required|string|max:255',
            'checkin_description' => 'required|string|max:1000',
        ]);

        RewardsContent::updateOrCreate(
            ['id' => 1],
            $request->all()
        );

        return response()->json(['success' => true, 'message' => 'Rewards content updated successfully']);
    }

    // Special Events Management
    public function storeSpecialEvent(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        SpecialEvent::create([
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'active',
        ]);

        return response()->json(['success' => true, 'message' => 'Special event added successfully']);
    }

    public function updateSpecialEvent(Request $request, SpecialEvent $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive',
        ]);

        $event->update($request->all());

        return response()->json(['success' => true, 'message' => 'Special event updated successfully']);
    }

    public function destroySpecialEvent(SpecialEvent $event)
    {
        $event->delete();
        return response()->json(['success' => true, 'message' => 'Special event deleted successfully']);
    }

    public function toggleSpecialEvent(SpecialEvent $event)
    {
        $newStatus = $event->status === 'active' ? 'inactive' : 'active';
        $event->update(['status' => $newStatus]);

        $message = $newStatus === 'active' ? 'Special event enabled successfully' : 'Special event disabled successfully';
        return response()->json([
            'success' => true,
            'message' => $message,
            'status' => $newStatus
        ]);
    }

    // Voucher Template Management
    public function storeVoucherTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'expiry_days' => 'required|integer|min:1',
        ]);

        VoucherTemplate::create($request->all());

        return response()->json(['success' => true, 'message' => 'Voucher template created successfully']);
    }

    public function updateVoucherTemplate(Request $request, VoucherTemplate $voucherTemplate)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'expiry_days' => 'required|integer|min:1',
        ]);

        $voucherTemplate->update($request->all());

        return response()->json(['success' => true, 'message' => 'Voucher template updated successfully']);
    }

    public function destroyVoucherTemplate(VoucherTemplate $voucherTemplate)
    {
        $voucherTemplate->delete();
        return response()->json(['success' => true, 'message' => 'Voucher template deleted successfully']);
    }

    public function generateVouchersFromTemplate(Request $request, VoucherTemplate $voucherTemplate)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:1000',
            'points_required' => 'required|integer|min:1',
        ]);

        $createdVouchers = [];

        try {
            for ($i = 1; $i <= $request->quantity; $i++) {
                $voucher = Reward::create([
                    'voucher_template_id' => $voucherTemplate->id,
                    'title' => $voucherTemplate->name . ' #' . str_pad($i, 4, '0', STR_PAD_LEFT),
                    'description' => 'Generated from template: ' . $voucherTemplate->name,
                    'points_required' => $request->points_required,
                    'is_active' => true,
                    'max_redemptions_per_customer' => 1,
                ]);

                $createdVouchers[] = $voucher;
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate vouchers: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "{$request->quantity} vouchers generated successfully from template",
            'vouchers' => $createdVouchers
        ]);
    }

    // === VOUCHER TEMPLATES SECTION ===
    public function voucherTemplatesIndex()
    {
        $templates = VoucherTemplate::withCount('rewards')->orderBy('created_at', 'desc')->get();
        return view('admin.rewards.voucher-templates.index', compact('templates'));
    }

    public function voucherTemplatesCreate()
    {
        return view('admin.rewards.voucher-templates.form');
    }

    public function voucherTemplatesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'expiry_days' => 'required|integer|min:1',
        ]);

        VoucherTemplate::create($request->all());

        return redirect()->route('admin.rewards.voucher-templates.index')
            ->with('success', 'Voucher template created successfully!');
    }

    public function voucherTemplatesEdit(VoucherTemplate $template)
    {
        return view('admin.rewards.voucher-templates.form', compact('template'));
    }

    public function voucherTemplatesUpdate(Request $request, VoucherTemplate $template)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'expiry_days' => 'required|integer|min:1',
        ]);

        $template->update($request->all());

        return redirect()->route('admin.rewards.voucher-templates.index')
            ->with('success', 'Voucher template updated successfully!');
    }

    public function voucherTemplatesDestroy(VoucherTemplate $template)
    {
        $template->delete();
        return redirect()->route('admin.rewards.voucher-templates.index')
            ->with('success', 'Voucher template deleted successfully!');
    }

    // === ACHIEVEMENTS SECTION ===
    public function achievementsIndex()
    {
        $achievements = Achievement::orderBy('created_at', 'desc')->get();
        return view('admin.rewards.achievements.index', compact('achievements'));
    }

    public function achievementsCreate()
    {
        return view('admin.rewards.achievements.form');
    }

    public function achievementsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_reward' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:100',
        ]);

        Achievement::create($request->all());

        return redirect()->route('admin.rewards.achievements.index')
            ->with('success', 'Achievement created successfully!');
    }

    public function achievementsEdit(Achievement $achievement)
    {
        return view('admin.rewards.achievements.form', compact('achievement'));
    }

    public function achievementsUpdate(Request $request, Achievement $achievement)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_reward' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:100',
        ]);

        $achievement->update($request->all());

        return redirect()->route('admin.rewards.achievements.index')
            ->with('success', 'Achievement updated successfully!');
    }

    public function achievementsDestroy(Achievement $achievement)
    {
        $achievement->delete();
        return redirect()->route('admin.rewards.achievements.index')
            ->with('success', 'Achievement deleted successfully!');
    }

    // === VOUCHER COLLECTIONS SECTION ===
    public function voucherCollectionsIndex()
    {
        $collections = VoucherCollection::orderBy('created_at', 'desc')->get();
        return view('admin.rewards.voucher-collections.index', compact('collections'));
    }

    public function voucherCollectionsCreate()
    {
        return view('admin.rewards.voucher-collections.form');
    }

    public function voucherCollectionsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        VoucherCollection::create($request->all());

        return redirect()->route('admin.rewards.voucher-collections.index')
            ->with('success', 'Voucher collection created successfully!');
    }

    public function voucherCollectionsEdit(VoucherCollection $collection)
    {
        return view('admin.rewards.voucher-collections.form', compact('collection'));
    }

    public function voucherCollectionsUpdate(Request $request, VoucherCollection $collection)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $collection->update($request->all());

        return redirect()->route('admin.rewards.voucher-collections.index')
            ->with('success', 'Voucher collection updated successfully!');
    }

    public function voucherCollectionsDestroy(VoucherCollection $collection)
    {
        $collection->delete();
        return redirect()->route('admin.rewards.voucher-collections.index')
            ->with('success', 'Voucher collection deleted successfully!');
    }

    // === BONUS CHALLENGES SECTION ===
    public function bonusChallengesIndex()
    {
        $challenges = BonusPointChallenge::orderBy('created_at', 'desc')->get();
        return view('admin.rewards.bonus-challenges.index', compact('challenges'));
    }

    public function bonusChallengesCreate()
    {
        return view('admin.rewards.bonus-challenges.form');
    }

    public function bonusChallengesStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'bonus_points' => 'required|integer|min:0',
        ]);

        BonusPointChallenge::create($request->all());

        return redirect()->route('admin.rewards.bonus-challenges.index')
            ->with('success', 'Bonus challenge created successfully!');
    }

    public function bonusChallengesEdit(BonusPointChallenge $challenge)
    {
        return view('admin.rewards.bonus-challenges.form', compact('challenge'));
    }

    public function bonusChallengesUpdate(Request $request, BonusPointChallenge $challenge)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'bonus_points' => 'required|integer|min:0',
        ]);

        $challenge->update($request->all());

        return redirect()->route('admin.rewards.bonus-challenges.index')
            ->with('success', 'Bonus challenge updated successfully!');
    }

    public function bonusChallengesDestroy(BonusPointChallenge $challenge)
    {
        $challenge->delete();
        return redirect()->route('admin.rewards.bonus-challenges.index')
            ->with('success', 'Bonus challenge deleted successfully!');
    }

    // === SPECIAL EVENTS SECTION ===
    public function specialEventsIndex()
    {
        $events = SpecialEvent::orderBy('created_at', 'desc')->get();
        return view('admin.rewards.special-events.index', compact('events'));
    }

    public function specialEventsCreate()
    {
        return view('admin.rewards.special-events.form');
    }

    public function specialEventsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_multiplier' => 'required|numeric|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        SpecialEvent::create($data);

        return redirect()->route('admin.rewards.special-events.index')
            ->with('success', 'Special event created successfully!');
    }

    public function specialEventsEdit(SpecialEvent $event)
    {
        return view('admin.rewards.special-events.form', compact('event'));
    }

    public function specialEventsUpdate(Request $request, SpecialEvent $event)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'points_multiplier' => 'required|numeric|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $event->update($data);

        return redirect()->route('admin.rewards.special-events.index')
            ->with('success', 'Special event updated successfully!');
    }

    public function specialEventsDestroy(SpecialEvent $event)
    {
        $event->delete();
        return redirect()->route('admin.rewards.special-events.index')
            ->with('success', 'Special event deleted successfully!');
    }

    // === LOYALTY TIERS SECTION ===
    public function loyaltyTiersIndex()
    {
        $tiers = LoyaltyTier::ordered()->get();
        return view('admin.rewards.loyalty-tiers.index', compact('tiers'));
    }

    public function loyaltyTiersCreate()
    {
        return view('admin.rewards.loyalty-tiers.form');
    }

    public function loyaltyTiersStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'minimum_spending' => 'required|numeric|min:0',
            'points_multiplier' => 'required|numeric|min:1',
            'benefits' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        LoyaltyTier::create($data);

        return redirect()->route('admin.rewards.loyalty-tiers.index')
            ->with('success', 'Loyalty tier created successfully!');
    }

    public function loyaltyTiersEdit(LoyaltyTier $tier)
    {
        return view('admin.rewards.loyalty-tiers.form', compact('tier'));
    }

    public function loyaltyTiersUpdate(Request $request, LoyaltyTier $tier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'minimum_spending' => 'required|numeric|min:0',
            'points_multiplier' => 'required|numeric|min:1',
            'benefits' => 'nullable|string',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $tier->update($data);

        return redirect()->route('admin.rewards.loyalty-tiers.index')
            ->with('success', 'Loyalty tier updated successfully!');
    }

    public function loyaltyTiersDestroy(LoyaltyTier $tier)
    {
        $tier->delete();
        return redirect()->route('admin.rewards.loyalty-tiers.index')
            ->with('success', 'Loyalty tier deleted successfully!');
    }

    // Check-in Settings
    public function checkinIndex()
    {
        $checkinSettings = CheckinSetting::first();
        return view('admin.rewards.checkin.index', compact('checkinSettings'));
    }

    // Redemptions
    public function redemptionsIndex()
    {
        $redemptions = CustomerReward::with(['customerProfile.user', 'reward'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.rewards.redemptions.index', compact('redemptions'));
    }

    // Members
    public function membersIndex()
    {
        $members = User::with('customerProfile.loyaltyTier')
            ->whereNotNull('points_balance')
            ->orderBy('points_balance', 'desc')
            ->paginate(20);
        return view('admin.rewards.members.index', compact('members'));
    }
}