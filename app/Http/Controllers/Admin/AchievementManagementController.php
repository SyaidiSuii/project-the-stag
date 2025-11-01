<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use Illuminate\Http\Request;

/**
 * PHASE 4: Achievement Management Controller
 *
 * Simple CRUD controller for achievements.
 */
class AchievementManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|manager');
    }

    public function index()
    {
        $achievements = Achievement::orderBy('created_at', 'desc')->get();
        return view('admin.rewards.achievements.index', compact('achievements'));
    }

    public function create()
    {
        return view('admin.rewards.achievements.form');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'points_reward' => 'required|integer|min:0',
            'criteria' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        Achievement::create($data);

        return redirect()
            ->route('admin.achievements.index')
            ->with('success', 'Achievement created successfully');
    }

    public function edit(Achievement $achievement)
    {
        return view('admin.rewards.achievements.form', compact('achievement'));
    }

    public function update(Request $request, Achievement $achievement)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'points_reward' => 'required|integer|min:0',
            'criteria' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $achievement->update($data);

        return redirect()
            ->route('admin.achievements.index')
            ->with('success', 'Achievement updated successfully');
    }

    public function destroy(Achievement $achievement)
    {
        $achievement->delete();

        return redirect()
            ->route('admin.achievements.index')
            ->with('success', 'Achievement deleted successfully');
    }
}
