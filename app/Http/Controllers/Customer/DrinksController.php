<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DrinksController extends Controller
{
    /**
     * Display the drinks menu page.
     */
    public function index()
    {
        return view('customer.drinks');
    }
}