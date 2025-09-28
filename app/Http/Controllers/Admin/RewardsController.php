<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reward;
use App\Models\VoucherTemplate;
use App\Models\CustomerReward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RewardsController extends Controller
{
    public function index()
    {
        $rewards = Reward::with('voucherTemplate')->paginate(10);
        
        $totalActiveRewards = Reward::where('is_active', true)->count();
        $totalRedeemed = CustomerReward::count();
        $totalPointsInSystem = DB::table('loyalty_transactions')
            ->where('transaction_type', 'earned')
            ->sum('points') - DB::table('loyalty_transactions')
            ->where('transaction_type', 'redeemed')
            ->sum('points');
        $activeSpecialEvents = DB::table('special_events')
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();

        return view('admin.rewards.index', compact(
            'rewards',
            'totalActiveRewards',
            'totalRedeemed',
            'totalPointsInSystem',
            'activeSpecialEvents'
        ));
    }

    public function create()
    {
        $voucherTemplates = VoucherTemplate::all();
        return view('admin.rewards.form', compact('voucherTemplates'));
    }

    public function edit($id)
    {
        $reward = Reward::findOrFail($id);
        $voucherTemplates = VoucherTemplate::all();
        return view('admin.rewards.form', compact('reward', 'voucherTemplates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reward_type' => 'required|in:points,voucher,tier_upgrade',
            'points_required' => 'nullable|integer|min:1',
            'voucher_template_id' => 'nullable|exists:voucher_templates,id',
            'expiry_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        Reward::create($request->all());

        return redirect()->route('admin.rewards.index')->with('success', 'Reward created successfully');
    }

    public function show($id)
    {
        $reward = Reward::with('voucherTemplate')->findOrFail($id);
        return response()->json($reward);
    }

    public function update(Request $request, $id)
    {
        $reward = Reward::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'reward_type' => 'required|in:points,voucher,tier_upgrade',
            'points_required' => 'nullable|integer|min:1',
            'voucher_template_id' => 'nullable|exists:voucher_templates,id',
            'expiry_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean'
        ]);

        $reward->update($request->all());

        return redirect()->route('admin.rewards.index')->with('success', 'Reward updated successfully');
    }

    public function destroy($id)
    {
        $reward = Reward::findOrFail($id);
        $reward->delete();

        return response()->json([
            'success' => true,
            'message' => 'Reward deleted successfully'
        ]);
    }

    public function toggle($id)
    {
        $reward = Reward::findOrFail($id);
        $reward->is_active = !$reward->is_active;
        $reward->save();

        return response()->json([
            'success' => true,
            'message' => 'Reward status updated successfully',
            'is_active' => $reward->is_active
        ]);
    }

    public function getVoucherTemplates()
    {
        $voucherTemplates = VoucherTemplate::all();
        return response()->json($voucherTemplates);
    }
}