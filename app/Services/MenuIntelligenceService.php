<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\SaleAnalytics;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Menu Intelligence Service
 * Analyzes menu performance, identifies underperformers, and provides optimization insights
 */
class MenuIntelligenceService
{
    /**
     * Get comprehensive menu performance analysis
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getMenuPerformanceAnalysis(Carbon $startDate, Carbon $endDate): array
    {
        $menuItems = MenuItem::with('category')->get();
        $performanceData = [];

        foreach ($menuItems as $item) {
            $performanceData[] = $this->analyzeMenuItem($item, $startDate, $endDate);
        }

        // Sort by performance score
        usort($performanceData, function($a, $b) {
            return $b['performance_score'] <=> $a['performance_score'];
        });

        return [
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
            'total_items_analyzed' => count($performanceData),
            'top_performers' => array_slice($performanceData, 0, 10),
            'underperformers' => array_slice(array_reverse($performanceData), 0, 10),
            'all_items' => $performanceData,
            'summary' => $this->getPerformanceSummary($performanceData),
        ];
    }

    /**
     * Analyze individual menu item performance
     *
     * @param MenuItem $item
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function analyzeMenuItem(MenuItem $item, Carbon $startDate, Carbon $endDate): array
    {
        // Get order data
        $orderItems = OrderItem::where('menu_item_id', $item->id)
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                    ->whereIn('order_status', ['completed', 'served'])
                    ->where('payment_status', 'paid');
            })
            ->with('order')
            ->get();

        $totalQuantity = $orderItems->sum('quantity');
        $totalRevenue = $orderItems->sum('total_price');
        $orderCount = $orderItems->pluck('order_id')->unique()->count();

        // Calculate metrics
        $avgQuantityPerOrder = $orderCount > 0 ? $totalQuantity / $orderCount : 0;
        $revenuePerOrder = $orderCount > 0 ? $totalRevenue / $orderCount : 0;

        // Profitability (assuming cost data exists)
        $cost = $item->cost ?? 0;
        $profit = $totalRevenue - ($cost * $totalQuantity);
        $profitMargin = $totalRevenue > 0 ? ($profit / $totalRevenue) * 100 : 0;

        // Performance score (weighted calculation)
        $performanceScore = $this->calculatePerformanceScore([
            'quantity_sold' => $totalQuantity,
            'revenue' => $totalRevenue,
            'order_frequency' => $orderCount,
            'profit_margin' => $profitMargin,
            'rating' => $item->rating ?? 0,
        ]);

        // Trend analysis
        $trend = $this->calculateItemTrend($item->id, $startDate, $endDate);

        return [
            'item_id' => $item->id,
            'name' => $item->name,
            'category' => $item->category->name ?? 'Uncategorized',
            'price' => $item->price,
            'cost' => $cost,
            'is_available' => $item->is_available,
            'metrics' => [
                'total_quantity_sold' => $totalQuantity,
                'total_revenue' => round($totalRevenue, 2),
                'total_profit' => round($profit, 2),
                'profit_margin' => round($profitMargin, 2),
                'order_count' => $orderCount,
                'avg_quantity_per_order' => round($avgQuantityPerOrder, 2),
                'revenue_per_order' => round($revenuePerOrder, 2),
                'rating' => $item->rating ?? 0,
            ],
            'performance_score' => round($performanceScore, 2),
            'performance_grade' => $this->getPerformanceGrade($performanceScore),
            'trend' => $trend,
            'recommendations' => $this->generateItemRecommendations($item, $performanceScore, $profitMargin, $trend),
        ];
    }

    /**
     * Get underperforming menu items
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @param int $limit
     * @return array
     */
    public function getUnderperformingItems(Carbon $startDate, Carbon $endDate, int $limit = 10): array
    {
        $analysis = $this->getMenuPerformanceAnalysis($startDate, $endDate);

        $underperformers = collect($analysis['all_items'])
            ->filter(function($item) {
                return $item['performance_score'] < 40; // Below 40% is underperforming
            })
            ->sortBy('performance_score')
            ->take($limit)
            ->values()
            ->toArray();

        return [
            'underperformers' => $underperformers,
            'count' => count($underperformers),
            'action_items' => $this->generateUnderperformerActions($underperformers),
        ];
    }

    /**
     * Get menu items with pricing optimization opportunities
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getPricingOpportunities(Carbon $startDate, Carbon $endDate): array
    {
        $analysis = $this->getMenuPerformanceAnalysis($startDate, $endDate);

        $opportunities = [];

        foreach ($analysis['all_items'] as $item) {
            // High demand + low profit margin = increase price
            if ($item['metrics']['order_count'] > 50 && $item['metrics']['profit_margin'] < 30) {
                $opportunities[] = [
                    'item' => $item['name'],
                    'current_price' => $item['price'],
                    'suggested_price' => round($item['price'] * 1.1, 2),
                    'reason' => 'High demand with low margin - can increase price by 10%',
                    'potential_revenue_increase' => round($item['metrics']['total_revenue'] * 0.1, 2),
                ];
            }

            // Low demand + high price = reduce price
            if ($item['metrics']['order_count'] < 10 && $item['price'] > 15 && $item['is_available']) {
                $opportunities[] = [
                    'item' => $item['name'],
                    'current_price' => $item['price'],
                    'suggested_price' => round($item['price'] * 0.9, 2),
                    'reason' => 'Low demand - try reducing price by 10% to stimulate sales',
                    'potential_volume_increase' => '20-30% estimated',
                ];
            }
        }

        return [
            'opportunities' => $opportunities,
            'count' => count($opportunities),
        ];
    }

    /**
     * Identify bundle/combo opportunities
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getBundleOpportunities(Carbon $startDate, Carbon $endDate): array
    {
        // Find items frequently ordered together
        $itemPairs = DB::table('order_items as oi1')
            ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
            ->join('orders', 'oi1.order_id', '=', 'orders.id')
            ->join('menu_items as mi1', 'oi1.menu_item_id', '=', 'mi1.id')
            ->join('menu_items as mi2', 'oi2.menu_item_id', '=', 'mi2.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.order_status', ['completed', 'served'])
            ->where('orders.payment_status', 'paid')
            ->where('oi1.menu_item_id', '<', 'oi2.menu_item_id')
            ->select(
                'oi1.menu_item_id as item1_id',
                'oi2.menu_item_id as item2_id',
                'mi1.name as item1_name',
                'mi2.name as item2_name',
                'mi1.price as item1_price',
                'mi2.price as item2_price',
                DB::raw('COUNT(*) as frequency')
            )
            ->groupBy('oi1.menu_item_id', 'oi2.menu_item_id', 'mi1.name', 'mi2.name', 'mi1.price', 'mi2.price')
            ->having('frequency', '>=', 5) // Ordered together at least 5 times
            ->orderByDesc('frequency')
            ->limit(10)
            ->get();

        $bundles = $itemPairs->map(function($pair) {
            $totalPrice = $pair->item1_price + $pair->item2_price;
            $suggestedBundlePrice = round($totalPrice * 0.9, 2); // 10% discount

            return [
                'items' => [
                    $pair->item1_name,
                    $pair->item2_name,
                ],
                'individual_prices' => [
                    $pair->item1_price,
                    $pair->item2_price,
                ],
                'total_individual_price' => round($totalPrice, 2),
                'suggested_bundle_price' => $suggestedBundlePrice,
                'discount_percentage' => 10,
                'frequency' => $pair->frequency,
                'potential_revenue' => round($suggestedBundlePrice * $pair->frequency, 2),
            ];
        })->toArray();

        return [
            'bundle_opportunities' => $bundles,
            'count' => count($bundles),
        ];
    }

    /**
     * Analyze seasonal trends for menu items
     *
     * @param int $itemId
     * @param int $months
     * @return array
     */
    public function getSeasonalTrends(int $itemId, int $months = 12): array
    {
        $endDate = Carbon::now();
        $startDate = $endDate->copy()->subMonths($months);

        $monthlySales = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.menu_item_id', $itemId)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.order_status', ['completed', 'served'])
            ->where('orders.payment_status', 'paid')
            ->select(
                DB::raw('YEAR(orders.created_at) as year'),
                DB::raw('MONTH(orders.created_at) as month'),
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.total_price) as total_revenue')
            )
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return [
            'item_id' => $itemId,
            'period_analyzed' => "{$months} months",
            'monthly_data' => $monthlySales->toArray(),
            'peak_months' => $this->identifyPeakMonths($monthlySales),
            'seasonality_detected' => $monthlySales->count() >= 6 && $this->hasSeasonalPattern($monthlySales),
        ];
    }

    /**
     * Calculate performance score for menu item
     *
     * @param array $metrics
     * @return float
     */
    private function calculatePerformanceScore(array $metrics): float
    {
        // Weighted scoring system
        $weights = [
            'quantity_sold' => 0.25,
            'revenue' => 0.30,
            'order_frequency' => 0.20,
            'profit_margin' => 0.15,
            'rating' => 0.10,
        ];

        // Normalize metrics to 0-100 scale
        $normalizedScores = [
            'quantity_sold' => min(100, ($metrics['quantity_sold'] / 100) * 100),
            'revenue' => min(100, ($metrics['revenue'] / 1000) * 100),
            'order_frequency' => min(100, ($metrics['order_frequency'] / 50) * 100),
            'profit_margin' => min(100, $metrics['profit_margin']),
            'rating' => ($metrics['rating'] / 5) * 100,
        ];

        $score = 0;
        foreach ($weights as $metric => $weight) {
            $score += $normalizedScores[$metric] * $weight;
        }

        return $score;
    }

    /**
     * Calculate trend for menu item
     *
     * @param int $itemId
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function calculateItemTrend(int $itemId, Carbon $startDate, Carbon $endDate): array
    {
        $weeklySales = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('order_items.menu_item_id', $itemId)
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->whereIn('orders.order_status', ['completed', 'served'])
            ->where('orders.payment_status', 'paid')
            ->select(
                DB::raw('WEEK(orders.created_at) as week'),
                DB::raw('SUM(order_items.quantity) as quantity')
            )
            ->groupBy('week')
            ->orderBy('week')
            ->get()
            ->pluck('quantity')
            ->toArray();

        if (count($weeklySales) < 2) {
            return ['direction' => 'insufficient_data'];
        }

        $firstHalf = array_slice($weeklySales, 0, ceil(count($weeklySales) / 2));
        $secondHalf = array_slice($weeklySales, ceil(count($weeklySales) / 2));

        $firstAvg = array_sum($firstHalf) / count($firstHalf);
        $secondAvg = array_sum($secondHalf) / count($secondHalf);

        $percentageChange = $firstAvg > 0 ? (($secondAvg - $firstAvg) / $firstAvg) * 100 : 0;

        return [
            'direction' => $percentageChange > 5 ? 'increasing' : ($percentageChange < -5 ? 'decreasing' : 'stable'),
            'percentage_change' => round($percentageChange, 2),
        ];
    }

    /**
     * Get performance grade
     *
     * @param float $score
     * @return string
     */
    private function getPerformanceGrade(float $score): string
    {
        if ($score >= 90) return 'A+';
        if ($score >= 80) return 'A';
        if ($score >= 70) return 'B+';
        if ($score >= 60) return 'B';
        if ($score >= 50) return 'C';
        if ($score >= 40) return 'D';
        return 'F';
    }

    /**
     * Generate recommendations for menu item
     *
     * @param MenuItem $item
     * @param float $score
     * @param float $profitMargin
     * @param array $trend
     * @return array
     */
    private function generateItemRecommendations(MenuItem $item, float $score, float $profitMargin, array $trend): array
    {
        $recommendations = [];

        // Low performance
        if ($score < 40) {
            $recommendations[] = 'Consider removing or replacing this item';
            $recommendations[] = 'Try promotional pricing to boost sales';
        } elseif ($score < 60) {
            $recommendations[] = 'Needs improvement - review recipe and presentation';
        }

        // Profitability
        if ($profitMargin < 20) {
            $recommendations[] = 'Low profit margin - review costs or increase price';
        } elseif ($profitMargin > 70) {
            $recommendations[] = 'High margin item - consider promotional bundle';
        }

        // Trend
        if (isset($trend['direction'])) {
            if ($trend['direction'] === 'decreasing') {
                $recommendations[] = 'Sales declining - investigate customer feedback';
            } elseif ($trend['direction'] === 'increasing') {
                $recommendations[] = 'Growing popularity - ensure consistent quality and availability';
            }
        }

        // Availability
        if (!$item->is_available) {
            $recommendations[] = 'Item currently unavailable - update menu or make available';
        }

        return $recommendations;
    }

    /**
     * Get performance summary
     *
     * @param array $performanceData
     * @return array
     */
    private function getPerformanceSummary(array $performanceData): array
    {
        $scores = array_column($performanceData, 'performance_score');

        return [
            'average_score' => round(array_sum($scores) / count($scores), 2),
            'highest_score' => round(max($scores), 2),
            'lowest_score' => round(min($scores), 2),
            'items_needing_attention' => count(array_filter($scores, fn($s) => $s < 50)),
        ];
    }

    /**
     * Generate actions for underperformers
     *
     * @param array $underperformers
     * @return array
     */
    private function generateUnderperformerActions(array $underperformers): array
    {
        return [
            'immediate' => 'Review underperforming items with kitchen team',
            'short_term' => 'Test promotional pricing for bottom 3 items',
            'long_term' => 'Consider menu redesign and replacement of lowest performers',
        ];
    }

    /**
     * Identify peak months from sales data
     *
     * @param \Illuminate\Support\Collection $monthlySales
     * @return array
     */
    private function identifyPeakMonths($monthlySales): array
    {
        return $monthlySales
            ->sortByDesc('total_quantity')
            ->take(3)
            ->map(function($month) {
                return Carbon::create(null, $month->month)->format('F');
            })
            ->toArray();
    }

    /**
     * Detect seasonal pattern in sales data
     *
     * @param \Illuminate\Support\Collection $monthlySales
     * @return bool
     */
    private function hasSeasonalPattern($monthlySales): bool
    {
        $quantities = $monthlySales->pluck('total_quantity')->toArray();
        $avg = array_sum($quantities) / count($quantities);
        $variance = array_sum(array_map(fn($q) => pow($q - $avg, 2), $quantities)) / count($quantities);
        $stdDev = sqrt($variance);

        // If standard deviation is > 30% of mean, likely seasonal
        return $avg > 0 && ($stdDev / $avg) > 0.3;
    }
}
