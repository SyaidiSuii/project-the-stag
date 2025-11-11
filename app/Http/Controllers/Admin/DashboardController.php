<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SaleAnalytics;
use App\Models\AdminNotification;
use App\Models\CustomerFeedback;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getNotifications()
    {
        $notifications = AdminNotification::latest()->take(15)->get();
        $unreadCount = AdminNotification::whereNull('read_at')->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markNotificationsAsRead()
    {
        AdminNotification::whereNull('read_at')->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function index()
    {
        // Order metrics
        $yesterdayOrders = Order::whereDate('created_at', today()->subDay())->count();
        $todayOrders = Order::whereDate('created_at', today())->count();
        
        // Revenue Data from Order table (based on completed/served and paid orders)
        $todayRevenue = Order::whereDate('created_at', today())
            ->whereIn('order_status', ['completed', 'served'])
            ->where('payment_status', 'paid')
            ->sum('total_amount');
        $revenueGrowth = $this->calculateDailyRevenueGrowth($todayRevenue);

        // Customer Feedback Data
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

        // Unpaid Orders Alert - Get flagged unpaid orders
        $unpaidOrders = Order::where('is_flagged_unpaid', true)
            ->where('payment_status', 'unpaid')
            ->with(['user', 'table'])
            ->orderBy('unpaid_alert_sent_at', 'desc')
            ->get();

        return view('admin.dashboard.index', compact(
            'yesterdayOrders',
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
            'chartData',
            'unpaidOrders'
        ));
    }

    /**
     * Calculate revenue growth by comparing today with yesterday.
     */
    private function calculateDailyRevenueGrowth($todayRevenue)
    {
        $yesterdayRevenue = Order::whereDate('created_at', today()->subDay())
            ->whereIn('order_status', ['completed', 'served'])
            ->where('payment_status', 'paid')
            ->sum('total_amount');

        if ($yesterdayRevenue == 0) {
            return $todayRevenue > 0 ? 100 : 0; // Or handle as infinite growth
        }

        return round((($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100, 2);
    }

    /**
     * Get total customer feedback count.
     */
    private function getCustomerFeedbackCount()
    {
        return CustomerFeedback::count();
    }

    /**
     * Calculate feedback growth by comparing current week with previous week.
     */
    private function calculateFeedbackGrowth()
    {
        $currentWeekFeedback = CustomerFeedback::whereBetween('created_at', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])->count();

        $previousWeekFeedback = CustomerFeedback::whereBetween('created_at', [
                Carbon::now()->subWeek()->startOfWeek(),
                Carbon::now()->subWeek()->endOfWeek()
            ])->count();

        if ($previousWeekFeedback == 0) {
            return $currentWeekFeedback > 0 ? 100 : 0;
        }

        return round((($currentWeekFeedback - $previousWeekFeedback) / $previousWeekFeedback) * 100);
    }
}
