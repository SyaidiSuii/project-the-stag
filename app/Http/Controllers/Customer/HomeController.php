<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the customer homepage.
     */
    public function index()
    {
        // Sample data for homepage - this can be moved to database later
        $promotions = [
            [
                'img' => '🥩',
                'name' => 'Signature Steak',
                'description' => 'Premium beef steak grilled to perfection with our special seasoning.',
                'price' => 45.00,
                'link' => route('customer.food.index')
            ],
            [
                'img' => '🍜',
                'name' => 'Laksa Special',
                'description' => 'Authentic Malaysian laksa with fresh ingredients and rich coconut broth.',
                'price' => 18.00,
                'link' => route('customer.food.index')
            ],
            [
                'img' => '🍝',
                'name' => 'Pasta Carbonara',
                'description' => 'Creamy carbonara pasta with crispy bacon and fresh herbs.',
                'price' => 22.00,
                'link' => route('customer.food.index')
            ],
            [
                'img' => '🍔',
                'name' => 'Gourmet Burger',
                'description' => 'Juicy beef patty with fresh lettuce, tomato, and our special sauce.',
                'price' => 28.00,
                'link' => route('customer.food.index')
            ],
            [
                'img' => '🥗',
                'name' => 'Garden Salad',
                'description' => 'Fresh mixed greens with seasonal vegetables and house dressing.',
                'price' => 15.00,
                'link' => route('customer.food.index')
            ]
        ];

        $stats = [
            'menu_items' => '100+',
            'rating' => '4.8',
            'experience' => '15+',
            'customers' => '25k+'
        ];

        $contact = [
            'address' => '123 Food Street, City Center, 50200 Kuala Lumpur',
            'phone' => '+60 12-345-6789',
            'hours' => 'Daily 11AM - 11PM',
            'email' => 'hello@thestag.com'
        ];

        return view('customer.index', compact('promotions', 'stats', 'contact'));
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

        return redirect()->route('customer.index')->with('feedback_success', 'Thank you! Your feedback has been submitted successfully.');
    }
}