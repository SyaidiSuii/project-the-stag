<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    /**
     * Display the food menu page.
     */
    public function index()
    {
        return view('customer.food');
    }
}