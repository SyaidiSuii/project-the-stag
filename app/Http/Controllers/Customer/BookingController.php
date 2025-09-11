<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    /**
     * Display booking page.
     */
    public function index()
    {
        return view('customer.booking.index');
    }
}