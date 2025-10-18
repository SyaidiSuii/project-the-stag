<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\HomepageContent;
use Illuminate\Http\Request;

class HomeController extends Controller
{
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
        $promotionSections = HomepageContent::bySection('promotion')->active()->ordered()->get();

        // Prepare data for the view
        $promotions = $promotionSections->map(function($promo) {
            return [
                'name' => $promo->title,
                'description' => $promo->content,
                'price' => $promo->minimum_order_amount ?? 25.00,
                'img' => $promo->image_url ?: '🍽️',
                'link' => $promo->button_link ?: route('customer.menu.index')
            ];
        })->toArray();

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

        // Prepare promotion header data
        $promotionHeader = [
            'title' => $promotionHeaderSection->title ?? 'Featured Promotions',
            'subtitle' => $promotionHeaderSection->subtitle ?? "Don't miss out on our limited-time offers and special deals!"
        ];

        return view('customer.home.index', compact('hero', 'about', 'promotionHeader', 'promotions', 'stats', 'contact'));
    }

    /**
     * Store customer feedback.
     */
    public function storeFeedback(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact' => 'nullable|string|max:255',
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:1000'
        ]);

        // For now, we'll just store in session/log
        // Later this can be moved to database
        
        session()->flash('feedback_success', 'Thank you! Your feedback has been submitted successfully.');

        return redirect()->route('customer.home.index')->with('feedback_success', 'Thank you! Your feedback has been submitted successfully.');
    }
}