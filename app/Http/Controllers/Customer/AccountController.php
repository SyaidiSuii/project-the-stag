<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display customer account page.
     */
    public function index()
    {
        return view('customer.account.index');
    }
}