<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreLoyaltyTierRequest;
use App\Models\LoyaltyTier;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * PHASE 4: Loyalty Tier Management Controller
 *
 * Focused controller for managing loyalty tiers.
 */
class LoyaltyTierManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|manager');
    }

    /**
     * Display loyalty tiers listing
     */
    public function index()
    {
        $tiers = LoyaltyTier::ordered()->get();

        // Get customer distribution per tier
        foreach ($tiers as $tier) {
            $tier->customer_count = User::where('loyalty_tier_id', $tier->id)->count();
        }

        return redirect()->route('admin.rewards.index');
    }

    /**
     * Show form for creating loyalty tier
     */
    public function create()
    {
        return view('admin.rewards.loyalty-tiers.form');
    }

    /**
     * Store loyalty tier
     */
    public function store(StoreLoyaltyTierRequest $request)
    {
        $data = $request->validated();

        // Auto-set order if not provided
        if (!isset($data['order'])) {
            $data['order'] = LoyaltyTier::max('order') + 1;
        }

        LoyaltyTier::create($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Loyalty tier created successfully');
    }

    /**
     * Show form for editing loyalty tier
     */
    public function edit(LoyaltyTier $tier)
    {
        return view('admin.rewards.loyalty-tiers.form', compact('tier'));
    }

    /**
     * Update loyalty tier
     */
    public function update(StoreLoyaltyTierRequest $request, LoyaltyTier $tier)
    {
        $data = $request->validated();

        $tier->update($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Loyalty tier updated successfully');
    }

    /**
     * Delete loyalty tier
     */
    public function destroy(LoyaltyTier $tier)
    {
        // Check if tier has customers
        $customerCount = User::where('loyalty_tier_id', $tier->id)->count();

        if ($customerCount > 0) {
            return redirect()
                ->back()
                ->with('error', "Cannot delete tier with {$customerCount} customers. Please reassign them first.");
        }

        $tier->delete();

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Loyalty tier deleted successfully');
    }

    /**
     * Toggle tier active status (AJAX)
     */
    public function toggle(LoyaltyTier $tier)
    {
        $tier->update([
            'is_active' => !$tier->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $tier->is_active,
            'message' => $tier->is_active ? 'Tier activated' : 'Tier deactivated'
        ]);
    }

    /**
     * Reorder tiers (AJAX)
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'tiers' => 'required|array',
            'tiers.*.id' => 'required|exists:loyalty_tiers,id',
            'tiers.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->tiers as $tierData) {
            LoyaltyTier::where('id', $tierData['id'])
                ->update(['order' => $tierData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tier order updated successfully'
        ]);
    }

    /**
     * View tier statistics
     */
    public function showStats(LoyaltyTier $tier)
    {
        $customers = User::where('loyalty_tier_id', $tier->id)
            ->with('customerProfile')
            ->orderBy('points_balance', 'desc')
            ->paginate(50);

        $stats = [
            'total_customers' => User::where('loyalty_tier_id', $tier->id)->count(),
            'total_points' => User::where('loyalty_tier_id', $tier->id)->sum('points_balance'),
            'avg_points' => User::where('loyalty_tier_id', $tier->id)->avg('points_balance'),
            'total_spending' => User::where('loyalty_tier_id', $tier->id)
                ->join('customer_profiles', 'users.id', '=', 'customer_profiles.user_id')
                ->sum('customer_profiles.total_spent'),
        ];

        return view('admin.rewards.loyalty-tiers.stats', compact('tier', 'customers', 'stats'));
    }
}
