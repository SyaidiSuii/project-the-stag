<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RewardsController extends Controller
{
    /**
     * Display customer rewards page.
     */
    public function index()
    {
        return view('customer.rewards');
    }
}