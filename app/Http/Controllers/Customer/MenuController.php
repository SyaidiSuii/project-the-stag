<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Category;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display the unified menu page with food, drinks, and set meals.
     */
    public function index()
    {
        // Get all categories with their available menu items
        $categories = Category::with(['menuItems' => function ($query) {
            $query->where('availability', true)->orderBy('name');
        }])
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

        return view('customer.menu.index', compact('categories'));
    }

    /**
     * Get menu data for AJAX requests
     */
    public function getMenuData(Request $request)
    {
        $type = $request->get('type', 'all');

        $query = MenuItem::where('availability', true)->with('category');

        if ($type !== 'all') {
            $query->whereHas('category', function($q) use ($type) {
                $q->where('type', $type);
            });
        }

        $menuItems = $query->orderBy('name')->get();

        return response()->json($menuItems);
    }
}
