<?php

namespace App\Services\Analytics;

use App\Models\Promotion;
use App\Models\PromotionUsageLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PromotionAnalyticsService
{
    /**
     * Get overall promotion summary statistics
     * For display in promotion index page
     */
    public function getOverallSummary(): array
    {
        // Total revenue from all promotions
        $totalRevenue = PromotionUsageLog::sum('order_total');

        // Total discount given
        $totalDiscount = PromotionUsageLog::sum('discount_amount');

        // Total promotions used
        $totalUsage = PromotionUsageLog::count();

        // Unique users who used promotions
        $uniqueUsers = PromotionUsageLog::distinct('user_id')->count('user_id');

        // Top performing promotion (by revenue)
        $topPromotion = PromotionUsageLog::select('promotion_id', DB::raw('SUM(order_total) as total_revenue'))
            ->groupBy('promotion_id')
            ->orderByDesc('total_revenue')
            ->first();

        $topPromotionDetails = null;
        if ($topPromotion) {
            $promotion = Promotion::find($topPromotion->promotion_id);
            $topPromotionDetails = [
                'id' => $promotion->id,
                'name' => $promotion->name,
                'revenue' => $topPromotion->total_revenue,
            ];
        }

        // Active promotions count
        $activePromotions = Promotion::where('is_active', true)->count();

        return [
            'total_revenue' => $totalRevenue,
            'total_discount' => $totalDiscount,
            'total_usage' => $totalUsage,
            'unique_users' => $uniqueUsers,
            'top_promotion' => $topPromotionDetails,
            'active_promotions' => $activePromotions,
            'average_order_value' => $totalUsage > 0 ? ($totalRevenue / $totalUsage) : 0,
            'average_discount' => $totalUsage > 0 ? ($totalDiscount / $totalUsage) : 0,
        ];
    }

    /**
     * Get detailed analytics for specific promotion
     */
    public function getPromotionAnalytics(int $promotionId, ?string $dateFrom = null, ?string $dateTo = null): array
    {
        $promotion = Promotion::findOrFail($promotionId);

        // Build base query conditions (use closure to reuse)
        $baseQueryBuilder = function() use ($promotionId, $dateFrom, $dateTo) {
            $query = PromotionUsageLog::where('promotion_id', $promotionId);

            if ($dateFrom) {
                $query->where('used_at', '>=', Carbon::parse($dateFrom));
            }
            if ($dateTo) {
                $query->where('used_at', '<=', Carbon::parse($dateTo)->endOfDay());
            }

            return $query;
        };

        // Basic stats - each query is independent
        $totalUsage = $baseQueryBuilder()->count();
        $totalRevenue = $baseQueryBuilder()->sum('order_total');
        $totalDiscount = $baseQueryBuilder()->sum('discount_amount');
        $uniqueUsers = $baseQueryBuilder()->distinct()->count('user_id');
        $avgOrderValue = $totalUsage > 0 ? ($totalRevenue / $totalUsage) : 0;
        $avgDiscount = $totalUsage > 0 ? ($totalDiscount / $totalUsage) : 0;

        // Usage trend (by day) - convert to array for view compatibility
        $usageTrend = $baseQueryBuilder()
            ->select(
                DB::raw('DATE(used_at) as date'),
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(order_total) as revenue'),
                DB::raw('SUM(discount_amount) as discount')
            )
            ->groupBy(DB::raw('DATE(used_at)'))
            ->orderBy(DB::raw('DATE(used_at)'))
            ->get()
            ->toArray();

        // Peak usage times (by hour) - convert to array for view compatibility
        $peakHours = $baseQueryBuilder()
            ->select(
                DB::raw('HOUR(used_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy(DB::raw('HOUR(used_at)'))
            ->orderByDesc('count')
            ->limit(5)
            ->get()
            ->toArray();

        // Top users
        $topUsers = $baseQueryBuilder()
            ->select('user_id', DB::raw('COUNT(*) as usage_count'), DB::raw('SUM(order_total) as total_revenue'), DB::raw('SUM(discount_amount) as total_discount'))
            ->groupBy('user_id')
            ->orderByDesc('usage_count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $user = \App\Models\User::find($item->user_id);
                return [
                    'user_id' => $item->user_id,
                    'user_name' => $user ? $user->name : 'Unknown',
                    'user_email' => $user ? $user->email : 'N/A',
                    'usage_count' => $item->usage_count,
                    'total_revenue' => $item->total_revenue,
                    'total_discount' => $item->total_discount,
                ];
            })
            ->toArray();

        // Conversion rate (if we have view tracking - for now use a placeholder)
        $views = $promotion->views ?? 0; // Assuming views field exists or will be added
        $conversionRate = $views > 0 ? (($totalUsage / $views) * 100) : 0;

        return [
            // Summary stats
            'total_usage' => $totalUsage,
            'total_revenue' => $totalRevenue,
            'total_discount' => $totalDiscount,
            'unique_users' => $uniqueUsers,
            'avg_order_value' => $avgOrderValue,
            'avg_discount' => $avgDiscount,
            'conversion_rate' => $conversionRate,
            'total_views' => $views,

            // Trends data
            'usage_trend' => $usageTrend,
            'peak_hours' => $peakHours,

            // User data
            'top_users' => $topUsers,

            // Meta data
            'date_range' => [
                'from' => $dateFrom,
                'to' => $dateTo,
            ],
        ];
    }

    /**
     * Compare multiple promotions
     */
    public function comparePromotions(array $promotionIds): array
    {
        $comparisons = [];

        foreach ($promotionIds as $promotionId) {
            $promotion = Promotion::find($promotionId);
            if (!$promotion) continue;

            $stats = PromotionUsageLog::where('promotion_id', $promotionId)
                ->selectRaw('
                    COUNT(*) as usage_count,
                    SUM(order_total) as total_revenue,
                    SUM(discount_amount) as total_discount,
                    COUNT(DISTINCT user_id) as unique_users,
                    AVG(order_total) as avg_order_value
                ')
                ->first();

            $comparisons[] = [
                'promotion_id' => $promotion->id,
                'promotion_name' => $promotion->name,
                'promotion_type' => $promotion->type_label,
                'usage_count' => $stats->usage_count ?? 0,
                'total_revenue' => $stats->total_revenue ?? 0,
                'total_discount' => $stats->total_discount ?? 0,
                'unique_users' => $stats->unique_users ?? 0,
                'avg_order_value' => $stats->avg_order_value ?? 0,
                'roi' => $this->calculateROI($stats->total_revenue ?? 0, $stats->total_discount ?? 0),
            ];
        }

        return $comparisons;
    }

    /**
     * Calculate ROI (Return on Investment)
     * ROI = ((Revenue - Discount) / Discount) * 100
     */
    private function calculateROI(float $revenue, float $discount): float
    {
        if ($discount == 0) return 0;
        return (($revenue - $discount) / $discount) * 100;
    }

    /**
     * Get promotion performance ranking
     */
    public function getPromotionRanking(string $metric = 'revenue', int $limit = 10): array
    {
        $orderBy = match($metric) {
            'revenue' => 'total_revenue',
            'usage' => 'usage_count',
            'users' => 'unique_users',
            'discount' => 'total_discount',
            default => 'total_revenue'
        };

        $rankings = PromotionUsageLog::select('promotion_id')
            ->selectRaw('
                COUNT(*) as usage_count,
                SUM(order_total) as total_revenue,
                SUM(discount_amount) as total_discount,
                COUNT(DISTINCT user_id) as unique_users
            ')
            ->groupBy('promotion_id')
            ->orderByDesc($orderBy)
            ->limit($limit)
            ->get()
            ->map(function ($item, $index) {
                $promotion = Promotion::find($item->promotion_id);
                return [
                    'rank' => $index + 1,
                    'promotion_id' => $item->promotion_id,
                    'promotion_name' => $promotion ? $promotion->name : 'Unknown',
                    'promotion_type' => $promotion ? $promotion->type_label : 'Unknown',
                    'usage_count' => $item->usage_count,
                    'total_revenue' => $item->total_revenue,
                    'total_discount' => $item->total_discount,
                    'unique_users' => $item->unique_users,
                ];
            });

        return $rankings->toArray();
    }

    /**
     * Export promotion analytics to CSV format
     */
    public function exportToCSV(int $promotionId): string
    {
        $analytics = $this->getPromotionAnalytics($promotionId);

        $csv = "Promotion Analytics Report\n";
        $csv .= "Promotion: {$analytics['promotion']['name']}\n";
        $csv .= "Type: {$analytics['promotion']['type_label']}\n";
        $csv .= "Generated: " . now()->format('Y-m-d H:i:s') . "\n\n";

        $csv .= "Summary Statistics\n";
        $csv .= "Metric,Value\n";
        $csv .= "Total Usage,{$analytics['summary']['total_usage']}\n";
        $csv .= "Total Revenue,RM " . number_format($analytics['summary']['total_revenue'], 2) . "\n";
        $csv .= "Total Discount,RM " . number_format($analytics['summary']['total_discount'], 2) . "\n";
        $csv .= "Unique Users,{$analytics['summary']['unique_users']}\n";
        $csv .= "Average Order Value,RM " . number_format($analytics['summary']['average_order_value'], 2) . "\n";
        $csv .= "Average Discount,RM " . number_format($analytics['summary']['average_discount'], 2) . "\n\n";

        $csv .= "Daily Usage Trend\n";
        $csv .= "Date,Usage Count,Revenue,Discount\n";
        foreach ($analytics['trends']['usage_by_day'] as $day) {
            $csv .= "{$day->date},{$day->usage_count},RM " . number_format($day->revenue, 2) . ",RM " . number_format($day->discount, 2) . "\n";
        }

        return $csv;
    }
}
