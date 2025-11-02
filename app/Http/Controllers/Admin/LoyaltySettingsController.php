<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheckinSetting;
use App\Models\RewardsContent;
use App\Models\SpecialEvent;
use Illuminate\Http\Request;

/**
 * PHASE 4: Loyalty Settings Controller
 *
 * Manages loyalty program settings like check-in rules,
 * rewards content, and special events.
 */
class LoyaltySettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin|manager');
    }

    // ==================== CHECK-IN SETTINGS ====================

    /**
     * Show check-in settings page
     */
    public function indexCheckin()
    {
        $settings = CheckinSetting::first();

        if (!$settings) {
            // Create default settings
            $settings = CheckinSetting::create([
                'base_points' => 10,
                'streak_bonus' => 5,
                'max_streak_bonus' => 50,
                'is_enabled' => true,
            ]);
        }

        return view('admin.rewards.checkin.index', compact('settings'));
    }

    /**
     * Update check-in settings
     */
    public function updateCheckin(Request $request)
    {
        $request->validate([
            'base_points' => 'required|integer|min:1|max:1000',
            'streak_bonus' => 'nullable|integer|min:0|max:100',
            'max_streak_bonus' => 'nullable|integer|min:0|max:1000',
            'is_enabled' => 'nullable|boolean',
        ]);

        $settings = CheckinSetting::first();

        $data = $request->only(['base_points', 'streak_bonus', 'max_streak_bonus']);
        $data['is_enabled'] = $request->has('is_enabled');

        if ($settings) {
            $settings->update($data);
        } else {
            CheckinSetting::create($data);
        }

        return redirect()
            ->route('admin.checkin.index')
            ->with('success', 'Check-in settings updated successfully');
    }

    // ==================== REWARDS CONTENT ====================

    /**
     * Show rewards content editor
     */
    public function indexContent()
    {
        $content = RewardsContent::first();

        if (!$content) {
            // Create default content
            $content = RewardsContent::create([
                'hero_title' => 'Join Our Loyalty Program',
                'hero_subtitle' => 'Earn points with every purchase',
                'how_it_works' => 'Earn points, redeem rewards, enjoy exclusive benefits',
            ]);
        }

        return view('admin.rewards.content.index', compact('content'));
    }

    /**
     * Update rewards content
     */
    public function updateContent(Request $request)
    {
        $request->validate([
            'hero_title' => 'nullable|string|max:255',
            'hero_subtitle' => 'nullable|string|max:255',
            'how_it_works' => 'nullable|string|max:2000',
            'terms_conditions' => 'nullable|string|max:5000',
        ]);

        $content = RewardsContent::first();

        if ($content) {
            $content->update($request->all());
        } else {
            RewardsContent::create($request->all());
        }

        return redirect()
            ->route('admin.rewards-content.index')
            ->with('success', 'Rewards content updated successfully');
    }

    // ==================== SPECIAL EVENTS ====================

    /**
     * Display special events listing
     */
    public function indexEvents()
    {
        $events = SpecialEvent::orderBy('created_at', 'desc')->get();
        return view('admin.rewards.special-events.index', compact('events'));
    }

    /**
     * Show form for creating special event
     */
    public function createEvent()
    {
        return view('admin.rewards.special-events.form');
    }

    /**
     * Store special event
     */
    public function storeEvent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'points_multiplier' => 'required|numeric|min:1|max:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        SpecialEvent::create($data);

        return redirect()
            ->route('admin.special-events.index')
            ->with('success', 'Special event created successfully');
    }

    /**
     * Show form for editing special event
     */
    public function editEvent(SpecialEvent $event)
    {
        return view('admin.rewards.special-events.form', compact('event'));
    }

    /**
     * Update special event
     */
    public function updateEvent(Request $request, SpecialEvent $event)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'points_multiplier' => 'required|numeric|min:1|max:10',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $event->update($data);

        return redirect()
            ->route('admin.special-events.index')
            ->with('success', 'Special event updated successfully');
    }

    /**
     * Delete special event
     */
    public function destroyEvent(SpecialEvent $event)
    {
        $event->delete();

        return redirect()
            ->route('admin.special-events.index')
            ->with('success', 'Special event deleted successfully');
    }

    /**
     * Toggle event active status (AJAX)
     */
    public function toggleEvent(SpecialEvent $event)
    {
        $event->update([
            'is_active' => !$event->is_active
        ]);

        return response()->json([
            'success' => true,
            'is_active' => $event->is_active,
            'message' => $event->is_active ? 'Event activated' : 'Event deactivated'
        ]);
    }
}
