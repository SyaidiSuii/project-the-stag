<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerReward;
use App\Models\User;
use App\Services\Loyalty\RewardRedemptionService;
use Illuminate\Http\Request;

/**
 * PHASE 4: Redemption Management Controller
 *
 * Manages customer reward redemptions.
 * Uses RewardRedemptionService from Phase 3.
 */
class RedemptionManagementController extends Controller
{
    protected RewardRedemptionService $redemptionService;

    public function __construct(RewardRedemptionService $redemptionService)
    {
        $this->middleware('auth');
        $this->middleware('role:admin|manager');
        $this->redemptionService = $redemptionService;
    }

    /**
     * Display redemptions listing
     */
    public function index(Request $request)
    {
        $query = CustomerReward::with(['customerProfile.user', 'reward']);

        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $redemptions = $query->orderBy('created_at', 'desc')
            ->paginate(20);

        // Statistics
        $stats = [
            'total' => CustomerReward::count(),
            'active' => CustomerReward::active()->count(),
            'redeemed' => CustomerReward::redeemed()->count(),
            'expired' => CustomerReward::expired()->count(),
            'pending' => CustomerReward::pending()->count(),
        ];

        return view('admin.rewards.redemptions.index', compact('redemptions', 'stats'));
    }

    /**
     * Mark redemption as redeemed (staff confirms usage)
     */
    public function markAsRedeemed(CustomerReward $redemption)
    {
        try {
            $this->redemptionService->markAsRedeemed($redemption);

            return redirect()
                ->back()
                ->with('success', 'Redemption marked as used successfully');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to mark redemption: ' . $e->getMessage());
        }
    }

    /**
     * Cancel redemption with points refund
     */
    public function cancel(CustomerReward $redemption, Request $request)
    {
        $refundPoints = $request->has('refund_points');

        try {
            $this->redemptionService->cancelRedemption($redemption, $refundPoints);

            $message = $refundPoints
                ? 'Redemption cancelled and points refunded'
                : 'Redemption cancelled without refund';

            return redirect()
                ->back()
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to cancel redemption: ' . $e->getMessage());
        }
    }

    /**
     * View redemption details
     */
    public function show(CustomerReward $redemption)
    {
        $redemption->load(['customerProfile.user', 'reward']);

        return view('admin.rewards.redemptions.show', compact('redemption'));
    }

    /**
     * Export redemptions to CSV
     */
    public function export(Request $request)
    {
        $query = CustomerReward::with(['customerProfile.user', 'reward']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $redemptions = $query->orderBy('created_at', 'desc')->get();

        $filename = 'redemptions_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($redemptions) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, ['ID', 'Customer', 'Email', 'Reward', 'Points Spent', 'Status', 'Claimed At', 'Redeemed At', 'Expires At']);

            // Data rows
            foreach ($redemptions as $redemption) {
                fputcsv($file, [
                    $redemption->id,
                    $redemption->customerProfile->user->name ?? 'N/A',
                    $redemption->customerProfile->user->email ?? 'N/A',
                    $redemption->reward->title ?? 'N/A',
                    $redemption->points_spent,
                    $redemption->status,
                    $redemption->claimed_at?->format('Y-m-d H:i:s'),
                    $redemption->redeemed_at?->format('Y-m-d H:i:s'),
                    $redemption->expires_at?->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
