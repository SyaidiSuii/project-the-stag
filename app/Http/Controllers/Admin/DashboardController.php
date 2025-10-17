<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SaleAnalytics;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Orders
        $totalOrders = Order::count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        
        // Revenue Data from SaleAnalytics
        $todayRevenue = SaleAnalytics::whereDate('date', today())
            ->sum('total_sales');
        $revenueGrowth = $this->calculateRevenueGrowth();

        // Customer Feedback Data - Count from sessions since we don't have dedicated feedback table
        $customerFeedbackCount = $this->getCustomerFeedbackCount();
        $feedbackGrowth = $this->calculateFeedbackGrowth();

        // Average Rating from MenuItem model
        $averageRating = MenuItem::where('rating_count', '>', 0)
            ->avg('rating_average');
        $totalReviews = MenuItem::sum('rating_count');

        // Recent User Activity (last 10 users by updated_at)
        $recentActivity = User::with('roles')
            ->orderBy('updated_at', 'desc')
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

        // Real Sales Chart Data from SaleAnalytics for the last 7 days
        $chartLabels = collect();
        $chartData = collect();
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels->push($date->format('D, M j'));
            
            $dailySales = SaleAnalytics::whereDate('date', $date)
                ->sum('total_sales');
            $chartData->push($dailySales);
        }
            
        return view('admin.dashboard.index', compact(
            'totalOrders',
            'todayOrders', 
            'todayRevenue',
            'revenueGrowth',
            'customerFeedbackCount',
            'feedbackGrowth',
            'averageRating',
            'totalReviews',
            'recentActivity',
            'popularMenuItems',
            'chartLabels',
            'chartData'
        ));
    }

    /**
     * Calculate revenue growth by comparing current week with previous week
     */
    /**
     * Calculate revenue growth by comparing current week with previous week
     */
    private function calculateRevenueGrowth()
    {
        $currentWeekRevenue = SaleAnalytics::whereBetween('date', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->sum('total_sales');

        $previousWeekRevenue = SaleAnalytics::whereBetween('date', [
            Carbon::now()->subWeek()->startOfWeek(),
            Carbon::now()->subWeek()->endOfWeek()
        ])->sum('total_sales');

        if ($previousWeekRevenue == 0) {
            return $currentWeekRevenue > 0 ? 100 : 0;
        }

        return round((($currentWeekRevenue - $previousWeekRevenue) / $previousWeekRevenue) * 100, 2);
    }

    /**
     * Get customer feedback count (placeholder - would need real feedback table)
     * For now, we can count the number of orders that might have feedback
     */
    private function getCustomerFeedbackCount()
    {
        // This is a placeholder - we'll need to implement real feedback storage
        // For now, we'll count completed orders as potential feedback sources
        return Order::where('order_status', 'completed')
            ->where('created_at', '>=', Carbon::now()->subWeek())
            ->count();
    }

    /**
     * Calculate feedback growth by comparing current week with previous week
     */
    private function calculateFeedbackGrowth()
    {
        $currentWeekFeedback = Order::where('order_status', 'completed')
            ->whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count();

        $previousWeekFeedback = Order::where('order_status', 'completed')
            ->whereBetween('created_at', [
                Carbon::now()->subWeek()->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek()
            ])->count();

        if ($previousWeekFeedback == 0) {
            return $currentWeekFeedback > 0 ? 100 : 0;
        }

        return round((($currentWeekFeedback - $previousWeekFeedback) / $previousWeekFeedback) * 100, 2);
    }
}
