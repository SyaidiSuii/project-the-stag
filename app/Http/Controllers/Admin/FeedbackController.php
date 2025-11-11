<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerFeedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the customer feedbacks.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $feedbacks = CustomerFeedback::with('user')
            ->latest()
            ->paginate(15);

        return view('admin.feedback.index', compact('feedbacks'));
    }
}
