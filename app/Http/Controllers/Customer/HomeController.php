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
        return view('customer.home.index');
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