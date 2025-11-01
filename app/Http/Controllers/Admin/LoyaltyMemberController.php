<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\LoyaltyTransaction;
use App\Services\Loyalty\LoyaltyService;
use Illuminate\Http\Request;

/**
 * PHASE 4: Loyalty Member Management Controller
 *
 * Manages loyalty program members (customers).
 * Uses LoyaltyService from Phase 3 for points operations.
 */
class LoyaltyMemberController extends Controller
{
    protected LoyaltyService $loyaltyService;

    public function __construct(LoyaltyService $loyaltyService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin|manager');
        $this->loyaltyService = $loyaltyService;
    }

    /**
     * Display loyalty members listing
     */
    public function index(Request $request)
    {
        $query = User::whereNotNull('points_balance')
            ->with('customerProfile', 'loyaltyTier');

        // Search by name or email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by tier
        if ($request->has('tier_id') && $request->tier_id !== 'all') {
            $query->where('loyalty_tier_id', $request->tier_id);
        }

        // Sort options
        $sortField = $request->get('sort', 'points_balance');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $members = $query->paginate(20);

        // Statistics
        $stats = [
            'total_members' => User::whereNotNull('points_balance')->count(),
            'total_points' => User::sum('points_balance'),
            'avg_points' => User::avg('points_balance'),
            'total_spending' => CustomerProfile::sum('total_spent'),
        ];

        return view('admin.rewards.members.index', compact('members', 'stats'));
    }

    /**
     * View member details and activity
     */
    public function show(User $member)
    {
        $member->load('customerProfile', 'loyaltyTier');

        // Get loyalty transactions
        $transactions = $this->loyaltyService->getTransactionHistory($member, 50);

        // Get redemption history
        $redemptions = $member->customerProfile
            ? $member->customerProfile->customerRewards()
                ->with('reward')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
            : collect();

        // Get voucher history
        $vouchers = $member->customerProfile
            ? $member->customerProfile->customerVouchers()
                ->with('voucherTemplate')
                ->orderBy('created_at', 'desc')
                ->limit(20)
                ->get()
            : collect();

        // Calculate stats
        $memberStats = [
            'total_earned' => $transactions->where('points_change', '>', 0)->sum('points_change'),
            'total_spent' => abs($transactions->where('points_change', '<', 0)->sum('points_change')),
            'total_redemptions' => $redemptions->count(),
            'active_redemptions' => $redemptions->where('status', 'active')->count(),
            'total_vouchers' => $vouchers->count(),
            'active_vouchers' => $vouchers->where('status', 'active')->count(),
        ];

        return view('admin.rewards.members.show', compact('member', 'transactions', 'redemptions', 'vouchers', 'memberStats'));
    }

    /**
     * Manually adjust member points
     */
    public function adjustPoints(Request $request, User $member)
    {
        $request->validate([
            'points' => 'required|integer|not_in:0',
            'description' => 'required|string|max:255',
        ]);

        try {
            $points = (int) $request->points;

            if ($points > 0) {
                // Award points
                $this->loyaltyService->awardPoints(
                    $member,
                    $points,
                    $request->description . ' (Manual adjustment by admin)',
                    null,
                    null
                );
                $message = "Successfully awarded {$points} points";
            } else {
                // Deduct points
                $this->loyaltyService->deductPoints(
                    $member,
                    abs($points),
                    $request->description . ' (Manual adjustment by admin)',
                    null,
                    null
                );
                $message = "Successfully deducted " . abs($points) . " points";
            }

            return redirect()
                ->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to adjust points: ' . $e->getMessage());
        }
    }

    /**
     * Reset member points to zero
     */
    public function resetPoints(Request $request, User $member)
    {
        $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        try {
            $currentBalance = $this->loyaltyService->getBalance($member);

            if ($currentBalance > 0) {
                $this->loyaltyService->deductPoints(
                    $member,
                    $currentBalance,
                    "Points reset: {$request->reason}",
                    null,
                    null
                );
            }

            return redirect()
                ->back()
                ->with('success', 'Member points reset successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to reset points: ' . $e->getMessage());
        }
    }

    /**
     * Export members to CSV
     */
    public function export()
    {
        $members = User::whereNotNull('points_balance')
            ->with('customerProfile', 'loyaltyTier')
            ->orderBy('points_balance', 'desc')
            ->get();

        $filename = 'loyalty_members_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($members) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, ['ID', 'Name', 'Email', 'Points Balance', 'Loyalty Tier', 'Total Spent', 'Visit Count', 'Joined Date']);

            // Data rows
            foreach ($members as $member) {
                fputcsv($file, [
                    $member->id,
                    $member->name,
                    $member->email,
                    $member->points_balance ?? 0,
                    $member->loyaltyTier->name ?? 'N/A',
                    $member->customerProfile->total_spent ?? 0,
                    $member->customerProfile->visit_count ?? 0,
                    $member->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
