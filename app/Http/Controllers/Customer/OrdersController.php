<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    /**
     * Display customer orders page.
     */
    public function index()
    {
        return view('customer.order.index');
    }
}