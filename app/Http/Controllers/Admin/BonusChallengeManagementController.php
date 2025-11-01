<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BonusPointChallenge;
use Illuminate\Http\Request;

/**
 * PHASE 4: Bonus Challenge Management Controller
 *
 * Simple CRUD controller for bonus point challenges.
 */
class BonusChallengeManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|manager');
    }

    public function index()
    {
        $challenges = BonusPointChallenge::orderBy('created_at', 'desc')->get();
        return view('admin.rewards.bonus-challenges.index', compact('challenges'));
    }

    public function create()
    {
        return view('admin.rewards.bonus-challenges.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'condition' => 'required|string|max:255',
            'bonus_points' => 'required|integer|min:1',
            'end_date' => 'nullable|date|after:today',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();

        BonusPointChallenge::create($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Bonus challenge created successfully')
            ->with('active_tab', 'bonus-challenges');
    }

    public function edit(BonusPointChallenge $challenge)
    {
        return view('admin.rewards.bonus-challenges.form', compact('challenge'));
    }

    public function update(Request $request, BonusPointChallenge $challenge)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'condition' => 'required|string|max:255',
            'bonus_points' => 'required|integer|min:1',
            'end_date' => 'nullable|date|after:today',
            'status' => 'required|in:active,inactive',
        ]);

        $data = $request->all();

        $challenge->update($data);

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Bonus challenge updated successfully')
            ->with('active_tab', 'bonus-challenges');
    }

    public function destroy(BonusPointChallenge $challenge)
    {
        $challenge->delete();

        return redirect()
            ->route('admin.rewards.index')
            ->with('success', 'Bonus challenge deleted successfully')
            ->with('active_tab', 'bonus-challenges');
    }
}
