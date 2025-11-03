<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\HomepageContent;
use App\Models\MenuItem;
use App\Models\CustomerFeedback;
use App\Models\Order;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Display the customer homepage.
     */
    public function index()
    {
        // Fetch homepage content from database
        $heroSection = HomepageContent::bySection('hero')->active()->first();
        $aboutSection = HomepageContent::bySection('about')->active()->first();
        $statsSection = HomepageContent::bySection('statistics')->active()->first();
        $contactSection = HomepageContent::bySection('contact')->active()->first();
        $promotionHeaderSection = HomepageContent::bySection('promotion')->active()->first();
        
        // Get AI Recommendations or Popular Items
        $recommendedItems = collect(); // Initialize as empty Collection
        if (Auth::check()) {
            // Logged in: Get personalized recommendations
            try {
                $userId = Auth::id();
                $recommendedItemIds = $this->recommendationService->getRecommendations($userId, 6);
                
                if (!empty($recommendedItemIds)) {
                    $recommendedItems = MenuItem::whereIn('id', $recommendedItemIds)
                        ->where('availability', true)
                        ->with('category')
                        ->get()
                        ->sortBy(function($item) use ($recommendedItemIds) {
                            return array_search($item->id, $recommendedItemIds);
                        })
                        ->values();
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch AI recommendations for homepage: ' . $e->getMessage());
            }
        }
        
        // Fallback to popular items if no recommendations
        if ($recommendedItems->isEmpty()) {
            $recommendedItems = MenuItem::where('availability', true)
                ->with('category')
                ->orderBy('rating_average', 'desc')
                ->orderBy('is_featured', 'desc')
                ->limit(5)  // Max 5 items for guests
                ->get();
        }

        // Prepare data for the view
        // Remove old promotions mapping
        // Now using AI recommendations

        $stats = [
            'menu_items' => $statsSection->stat1_value ?? '100+',
            'rating' => $statsSection->stat2_value ?? '4.8',
            'experience' => $statsSection->stat3_value ?? '15+',
            'customers' => $statsSection->stat4_value ?? '25k+'
        ];

        $contact = [
            'title' => $contactSection->title ?? 'Visit Us Today',
            'subtitle' => $contactSection->subtitle ?? "We're located in the heart of the city, ready to serve you exceptional dining experiences.",
            'address' => $contactSection->address ?? '123 Food Street, City Center, 50200 Kuala Lumpur',
            'phone' => $contactSection->phone ?? '+60 12-345-6789',
            'hours' => $contactSection->hours ?? 'Daily 11AM - 11PM',
            'email' => $contactSection->email ?? 'hello@thestag.com',
            'feedback_title' => $contactSection->feedback_form_title ?? 'Share Your Feedback',
            'feedback_subtitle' => $contactSection->feedback_form_subtitle ?? 'Help us improve by sharing your dining experience with us.'
        ];

        // Prepare hero and about data
        $hero = [
            'title' => $heroSection->title ?? 'Welcome to The Stag',
            'subtitle' => $heroSection->subtitle ?? 'Experience premium dining with our signature steaks',
            'btn1_text' => $heroSection->primary_button_text ?? 'Explore Menu',
            'btn2_text' => $heroSection->secondary_button_text ?? 'Learn More'
        ];

        $about = [
            'title' => $aboutSection->title ?? 'About The Stag',
            'subtitle' => $aboutSection->subtitle ?? 'Discover the perfect blend',
            'description' => $aboutSection->description ?? 'At The Stag, we pride ourselves...',
            'feature1' => $aboutSection->feature_1 ?? 'Premium beef steaks',
            'feature2' => $aboutSection->feature_2 ?? 'Authentic Malaysian dishes',
            'feature3' => $aboutSection->feature_3 ?? 'Fresh pasta made daily',
            'feature4' => $aboutSection->feature_4 ?? 'Award-winning culinary team',
            'btn1_text' => $aboutSection->about_primary_button_text ?? 'View Full Menu',
            'btn2_text' => $aboutSection->about_secondary_button_text ?? 'Contact Us'
        ];

        // Prepare promotion header data (now for recommendations)
        $promotionHeader = [
            'title' => Auth::check() ? 'Recommended For You' : 'Popular Dishes',
            'subtitle' => Auth::check()
                ? 'Based on your order history and preferences'
                : 'Try our most popular and highly-rated dishes!'
        ];

        // Calculate remaining feedback submissions
        $feedbackInfo = null;
        if (Auth::check()) {
            $totalOrders = Order::where('user_id', Auth::id())
                ->whereIn('order_status', ['completed', 'delivered'])
                ->count();
            $existingFeedbacks = CustomerFeedback::where('user_id', Auth::id())->count();
            $remainingFeedbacks = max(0, $totalOrders - $existingFeedbacks);

            $feedbackInfo = [
                'total_orders' => $totalOrders,
                'submitted' => $existingFeedbacks,
                'remaining' => $remainingFeedbacks,
                'can_submit' => $remainingFeedbacks > 0
            ];
        }

        return view('customer.home.index', compact('hero', 'about', 'promotionHeader', 'recommendedItems', 'stats', 'contact', 'feedbackInfo'));
    }

    /**
     * Store customer feedback.
     */
    public function storeFeedback(Request $request)
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to submit feedback.');
        }

        $user = Auth::user();

        // Count total completed orders
        $totalOrders = Order::where('user_id', $user->id)
            ->whereIn('order_status', ['completed', 'delivered'])
            ->count();

        // Count existing feedbacks
        $existingFeedbacks = CustomerFeedback::where('user_id', $user->id)->count();

        // Check if user can submit more feedback (limited to number of orders)
        if ($existingFeedbacks >= $totalOrders) {
            return redirect()->route('customer.home.index')
                ->with('feedback_error', 'You have reached the maximum number of feedbacks. You can submit one feedback per completed order.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:1000'
        ]);

        // Create feedback
        CustomerFeedback::create([
            'user_id' => $user->id,
            'rating' => $request->rating,
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        return redirect()->route('customer.home.index')
            ->with('feedback_success', 'Thank you for your feedback! Your review has been submitted successfully.');
    }
}