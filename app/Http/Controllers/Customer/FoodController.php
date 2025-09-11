<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Category;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    /**
     * Display the food menu page.
     */
    public function index()
    {
        $categories = Category::with(['menuItems' => function ($query) {
            $query->available()->orderBy('name');
        }])->where('type', 'sub')
          ->whereHas('parent', function($q) {
              $q->where('name', 'LIKE', '%food%');
          })
          ->orderBy('name')->get();
        
        $featuredItems = MenuItem::featured()->available()->with('category')->take(6)->get();
        
        $allMenuItems = MenuItem::available()->with('category')->orderBy('name')->get();
        
        return view('customer.food.index', compact('categories', 'featuredItems', 'allMenuItems'));
    }
}