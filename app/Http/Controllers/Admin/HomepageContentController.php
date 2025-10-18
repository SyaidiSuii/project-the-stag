<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageContent;
use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageContentController extends Controller
{
    /**
     * Display the homepage content management page
     */
    public function index()
    {
        // Get all sections for the homepage
        $heroSection = HomepageContent::bySection('hero')->active()->first();
        $aboutSection = HomepageContent::bySection('about')->active()->first();
        $statsSection = HomepageContent::bySection('statistics')->active()->first();
        $contactSection = HomepageContent::bySection('contact')->active()->first();

        // Get featured menu header section
        $featuredMenuSection = HomepageContent::bySection('featured_menu')->active()->first();

        // Get all promotion sections
        $promotionSections = HomepageContent::bySection('promotion')
            ->active()
            ->ordered()
            ->get();

        // Get dashboard statistics for stats section
        $totalCustomers = User::role('customer')->count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $todayOrders = Order::whereDate('created_at', today())->count();

        return view('admin.home-content.index', compact(
            'heroSection',
            'aboutSection',
            'statsSection',
            'contactSection',
            'featuredMenuSection',
            'promotionSections',
            'totalCustomers',
            'totalOrders',
            'totalRevenue',
            'todayOrders'
        ));
    }

    /**
     * Get a specific section's content
     */
    public function getSection($sectionType)
    {
        $content = HomepageContent::bySection($sectionType)->active()->first();

        return response()->json([
            'success' => true,
            'content' => $content
        ]);
    }

    /**
     * Store a newly created section
     */
    public function store(Request $request)
    {
        $validated = $this->validateSection($request);

        try {
            $content = HomepageContent::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Homepage content created successfully',
                'content' => $content
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create homepage content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified section
     */
    public function update(Request $request, $id)
    {
        $validated = $this->validateSection($request);

        try {
            $content = HomepageContent::findOrFail($id);
            $content->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Homepage content updated successfully',
                'content' => $content
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update homepage content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified section
     */
    public function destroy($id)
    {
        try {
            $content = HomepageContent::findOrFail($id);

            // Delete associated image if exists
            if ($content->image_url && Storage::disk('public')->exists($content->image_url)) {
                Storage::disk('public')->delete($content->image_url);
            }

            $content->delete();

            return response()->json([
                'success' => true,
                'message' => 'Homepage content deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete homepage content: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload image for homepage content
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        try {
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('homepage-images', $filename, 'public');

                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully',
                    'url' => Storage::url($path),
                    'path' => $path
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No image file found'
            ], 400);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validate section data based on section type
     */
    private function validateSection(Request $request)
    {
        $rules = [
            'section_type' => 'required|in:hero,statistics,featured_menu,about,contact,promotion',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];

        // Common fields
        $commonRules = [
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'content' => 'nullable|string',
            'image_url' => 'nullable|string|max:500',
            'button_text' => 'nullable|string|max:100',
            'button_link' => 'nullable|string|max:500',
        ];

        // Hero section specific
        if ($request->section_type === 'hero') {
            $rules = array_merge($rules, $commonRules, [
                'highlighted_text' => 'nullable|string|max:255',
                'primary_button_text' => 'nullable|string|max:100',
                'secondary_button_text' => 'nullable|string|max:100',
                'background_color_1' => 'nullable|string|max:7',
                'background_color_2' => 'nullable|string|max:7',
                'background_color_3' => 'nullable|string|max:7',
                'gradient_direction' => 'nullable|string|max:20',
            ]);
        }

        // About section specific
        if ($request->section_type === 'about') {
            $rules = array_merge($rules, $commonRules, [
                'description' => 'nullable|string',
                'feature_1' => 'nullable|string|max:255',
                'feature_2' => 'nullable|string|max:255',
                'feature_3' => 'nullable|string|max:255',
                'feature_4' => 'nullable|string|max:255',
                'about_primary_button_text' => 'nullable|string|max:100',
                'about_secondary_button_text' => 'nullable|string|max:100',
            ]);
        }

        // Statistics section specific
        if ($request->section_type === 'statistics') {
            $rules = array_merge($rules, [
                'stat1_icon' => 'nullable|string|max:10',
                'stat1_value' => 'nullable|string|max:50',
                'stat1_label' => 'nullable|string|max:100',
                'stat2_icon' => 'nullable|string|max:10',
                'stat2_value' => 'nullable|string|max:50',
                'stat2_label' => 'nullable|string|max:100',
                'stat3_icon' => 'nullable|string|max:10',
                'stat3_value' => 'nullable|string|max:50',
                'stat3_label' => 'nullable|string|max:100',
                'stat4_icon' => 'nullable|string|max:10',
                'stat4_value' => 'nullable|string|max:50',
                'stat4_label' => 'nullable|string|max:100',
            ]);
        }

        // Contact section specific
        if ($request->section_type === 'contact') {
            $rules = array_merge($rules, $commonRules, [
                'address' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:50',
                'hours' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'feedback_form_title' => 'nullable|string|max:255',
                'feedback_form_subtitle' => 'nullable|string|max:500',
            ]);
        }

        // Promotion section specific
        if ($request->section_type === 'promotion') {
            $rules = array_merge($rules, $commonRules, [
                'discount_percentage' => 'nullable|numeric|min:0|max:100',
                'promotion_code' => 'nullable|string|max:50',
                'promotion_start_date' => 'nullable|date',
                'promotion_end_date' => 'nullable|date|after_or_equal:promotion_start_date',
                'minimum_order_amount' => 'nullable|numeric|min:0',
                'is_promotion_active' => 'boolean',
            ]);
        }

        return $request->validate($rules);
    }
}
