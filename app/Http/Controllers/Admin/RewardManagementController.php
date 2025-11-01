<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreRewardRequest;
use App\Http\Requests\Admin\UpdateRewardRequest;
use App\Models\Reward;
use App\Models\VoucherTemplate;
use Illuminate\Http\Request;

/**
 * PHASE 4: Reward Management Controller
 *
 * Focused controller for managing rewards only.
 * Extracted from the monolithic RewardsController (1000 lines → ~200 lines per controller)
 *
 * Benefits:
 * - Single Responsibility Principle
 * - Easier testing and maintenance
 * - Clear separation of concerns
 * - Better code organization
 */
class RewardManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|manager');
    }

    /**
     * Display a listing of rewards
     */
    public function index()
    {
        $rewards = Reward::withCount('customerRewards')
            ->orderBy('points_required')
            ->get();

        return view('admin.rewards.rewards.index', compact('rewards'));
    }

    /**
     * Show the form for creating a new reward
     */
    public function create()
    {
        $voucherTemplates = VoucherTemplate::where('is_active', true)
            ->orderBy('name')
            ->get();

        $menuItems = \App\Models\MenuItem::where('is_available', true)
            ->orderBy('name')
            ->get();

        $loyaltyTiers = \App\Models\LoyaltyTier::orderBy('points_threshold')
            ->get();

        return view('admin.rewards.rewards.form', compact('voucherTemplates', 'menuItems', 'loyaltyTiers'));
    }

    /**
     * Store a newly created reward
     */
    public function store(StoreRewardRequest $request)
    {
        $data = $request->validated();

        $reward = Reward::create($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Reward created successfully');
    }

    /**
     * Show the form for editing the specified reward
     */
    public function edit(Reward $reward)
    {
        $voucherTemplates = VoucherTemplate::where('is_active', true)
            ->orderBy('name')
            ->get();

        $menuItems = \App\Models\MenuItem::where('is_available', true)
            ->orderBy('name')
            ->get();

        $loyaltyTiers = \App\Models\LoyaltyTier::orderBy('points_threshold')
            ->get();

        return view('admin.rewards.rewards.form', compact('reward', 'voucherTemplates', 'menuItems', 'loyaltyTiers'));
    }

    /**
     * Update the specified reward
     */
    public function update(UpdateRewardRequest $request, Reward $reward)
    {
        $data = $request->validated();

        $reward->update($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Reward updated successfully');
    }

    /**
     * Remove the specified reward (soft delete)
     */
    public function destroy(Reward $reward)
    {
        // Check if reward has active redemptions
        $activeRedemptions = $reward->customerRewards()
            ->whereIn('status', ['active', 'pending'])
            ->count();

        if ($activeRedemptions > 0) {
            return redirect()
                ->back()
                ->with('error', "Cannot delete reward with {$activeRedemptions} active redemptions");
        }

        $reward->delete();

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Reward deleted successfully');
    }

    /**
     * Toggle reward active status (AJAX)
     */
    public function toggle(Reward $reward)
    {
        $reward->update([
            'is_active' => !$reward->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $reward->is_active,
            'message' => $reward->is_active ? 'Reward activated' : 'Reward deactivated'
        ]);
    }

    /**
     * Get reward details (AJAX)
     */
    public function show(Reward $reward)
    {
        $reward->load(['voucherTemplate', 'customerRewards' => function ($query) {
            $query->latest()->limit(10);
        }]);

        return response()->json([
            'success' => true,
            'reward' => $reward,
            'redemption_count' => $reward->customerRewards()->count(),
            'active_redemptions' => $reward->customerRewards()->active()->count(),
        ]);
    }

    /**
     * Duplicate a reward
     */
    public function duplicate(Reward $reward)
    {
        $newReward = $reward->replicate();
        $newReward->title = $reward->title . ' (Copy)';
        $newReward->is_active = false;
        $newReward->save();

        return redirect()
            ->route('admin.rewards.edit', $newReward)
            ->with('success', 'Reward duplicated successfully. Please review and activate.');
    }
}
