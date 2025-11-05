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

        return response()->streamDownload(function() use ($redemptions) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Add separator hint for Excel
            fwrite($file, "sep=;\n");
            
            // Header row
            fputcsv($file, [
                'ID',
                'Customer Name',
                'Customer Email',
                'Reward Title',
                'Reward Type',
                'Points Spent',
                'Status',
                'Claimed Date',
                'Redeemed Date',
                'Expiry Date'
            ], ';');
            
            // Data rows
            foreach ($redemptions as $redemption) {
                fputcsv($file, [
                    $redemption->id,
                    $redemption->customerProfile->user->name ?? 'N/A',
                    $redemption->customerProfile->user->email ?? 'N/A',
                    $redemption->reward->title ?? 'N/A',
                    ucfirst($redemption->reward->reward_type ?? 'N/A'),
                    $redemption->points_spent,
                    ucfirst($redemption->status),
                    $redemption->claimed_at ? $redemption->claimed_at->format('Y-m-d H:i') : 'Not Claimed',
                    $redemption->redeemed_at ? $redemption->redeemed_at->format('Y-m-d H:i') : 'Not Redeemed',
                    $redemption->expires_at ? $redemption->expires_at->format('Y-m-d') : 'No Expiry',
                ], ';');
            }
            
            fclose($file);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }
}
