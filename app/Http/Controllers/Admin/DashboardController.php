<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Orders
        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        
        // Recent User Activity (last 10 users by updated_at)
        $recentActivity = User::orderBy('updated_at', 'desc')
            ->take(10)
            ->get(['id', 'name', 'email', 'updated_at']);
            
        // Popular Menu Items (top 5 based on order count)
        $popularMenuItems = MenuItem::with('category')
            ->withCount(['orderItems' => function($query) {
                $query->whereHas('order', function($q) {
                    $q->where('order_status', '!=', 'cancelled');
                });
            }])
            ->orderBy('order_items_count', 'desc')
            ->take(5)
            ->get();
            
        return view('admin.dashboard.index', compact(
            'totalOrders',
            'todayOrders', 
            'recentActivity',
            'popularMenuItems'
        ));
    }
}
