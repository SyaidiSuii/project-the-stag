<?php

namespace App\Services;

use App\Models\MenuItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Menu Recommendation Service
 * Combines AI-powered recommendations with menu intelligence for business insights
 */
class MenuRecommendationService
{
    protected $aiRecommendationService;
    protected $menuIntelligenceService;

    public function __construct(
        RecommendationService $aiRecommendationService,
        MenuIntelligenceService $menuIntelligenceService
    ) {
        $this->aiRecommendationService = $aiRecommendationService;
        $this->menuIntelligenceService = $menuIntelligenceService;
    }

    /**
     * Get personalized menu recommendations for a user
     * Combines AI recommendations with business rules
     *
     * @param int $userId
     * @param int $limit
     * @param array $context Additional context (current_cart, preferences, etc.)
     * @return array
     */
    public function getPersonalizedRecommendations(int $userId, int $limit = 10, array $context = []): array
    {
        // Get AI recommendations
        $aiRecommendations = $this->aiRecommendationService->getRecommendationsWithScores($userId, $limit * 2);

        // Get menu performance data
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(30);
        $menuPerformance = $this->menuIntelligenceService->getMenuPerformanceAnalysis($startDate, $endDate);

        // Create performance lookup
        $performanceLookup = collect($menuPerformance['all_items'])
            ->keyBy('item_id')
            ->toArray();

        // Enhance AI recommendations with business intelligence
        $enhancedRecommendations = [];
        foreach ($aiRecommendations as $recommendation) {
            $itemId = $recommendation['menu_item_id'];
            $performance = $performanceLookup[$itemId] ?? null;

            // Skip unavailable or poor-performing items
            if (!$performance || !$performance['is_available']) {
                continue;
            }

            // Calculate combined score (AI score + performance score)
            $aiScore = $recommendation['score'] ?? 0;
            $performanceScore = $performance['performance_score'] ?? 0;
            $combinedScore = ($aiScore * 0.6) + ($performanceScore * 0.4); // 60% AI, 40% performance

            $enhancedRecommendations[] = [
                'item_id' => $itemId,
                'name' => $performance['name'],
                'price' => $performance['price'],
                'category' => $performance['category'],
                'ai_score' => round($aiScore, 2),
                'performance_score' => round($performanceScore, 2),
                'combined_score' => round($combinedScore, 2),
                'rating' => $performance['metrics']['rating'] ?? 0,
                'profit_margin' => $performance['metrics']['profit_margin'] ?? 0,
                'popularity_rank' => $this->getPopularityRank($itemId, $menuPerformance),
                'reason' => $this->generateRecommendationReason($recommendation, $performance, $context),
            ];
        }

        // Sort by combined score
        usort($enhancedRecommendations, function($a, $b) {
            return $b['combined_score'] <=> $a['combined_score'];
        });

        return [
            'recommendations' => array_slice($enhancedRecommendations, 0, $limit),
            'source' => 'ai_enhanced',
            'context' => $context,
        ];
    }

    /**
     * Get menu improvement suggestions for admin
     *
     * @return array
     */
    public function getMenuImprovementSuggestions(): array
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(30);

        $analysis = $this->menuIntelligenceService->getMenuPerformanceAnalysis($startDate, $endDate);
        $underperformers = $this->menuIntelligenceService->getUnderperformingItems($startDate, $endDate, 10);
        $pricing = $this->menuIntelligenceService->getPricingOpportunities($startDate, $endDate);
        $bundles = $this->menuIntelligenceService->getBundleOpportunities($startDate, $endDate);

        $suggestions = [];

        // 1. Remove underperformers
        if (!empty($underperformers['underperformers'])) {
            foreach (array_slice($underperformers['underperformers'], 0, 3) as $item) {
                if ($item['performance_score'] < 20 && $item['metrics']['order_count'] < 5) {
                    $suggestions[] = [
                        'type' => 'remove_item',
                        'priority' => 'high',
                        'item' => $item['name'],
                        'reason' => "Very low performance (score: {$item['performance_score']}) with only {$item['metrics']['order_count']} orders in 30 days",
                        'expected_impact' => 'Free up menu space for better performing items',
                    ];
                }
            }
        }

        // 2. Pricing adjustments
        if (!empty($pricing['opportunities'])) {
            foreach (array_slice($pricing['opportunities'], 0, 3) as $opp) {
                $suggestions[] = [
                    'type' => 'price_adjustment',
                    'priority' => 'medium',
                    'item' => $opp['item'],
                    'current_price' => $opp['current_price'],
                    'suggested_price' => $opp['suggested_price'],
                    'reason' => $opp['reason'],
                    'expected_impact' => $opp['potential_revenue_increase'] ?? 'Increased sales volume',
                ];
            }
        }

        // 3. Bundle opportunities
        if (!empty($bundles['bundle_opportunities'])) {
            foreach (array_slice($bundles['bundle_opportunities'], 0, 3) as $bundle) {
                $suggestions[] = [
                    'type' => 'create_bundle',
                    'priority' => 'high',
                    'items' => $bundle['items'],
                    'suggested_price' => $bundle['suggested_bundle_price'],
                    'discount' => $bundle['discount_percentage'] . '%',
                    'reason' => "These items are ordered together {$bundle['frequency']} times",
                    'expected_impact' => "Potential revenue: RM {$bundle['potential_revenue']}",
                ];
            }
        }

        // 4. Promote high-performers
        foreach (array_slice($analysis['top_performers'], 0, 3) as $item) {
            if ($item['performance_score'] > 70 && $item['metrics']['profit_margin'] > 50) {
                $suggestions[] = [
                    'type' => 'promote_item',
                    'priority' => 'medium',
                    'item' => $item['name'],
                    'reason' => "High performance (score: {$item['performance_score']}) and excellent profit margin ({$item['metrics']['profit_margin']}%)",
                    'expected_impact' => 'Feature in promotions or combo deals',
                ];
            }
        }

        return [
            'suggestions' => $suggestions,
            'total_suggestions' => count($suggestions),
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString(),
            ],
        ];
    }

    /**
     * Get complementary item suggestions for upselling
     *
     * @param int $itemId
     * @param int $limit
     * @return array
     */
    public function getComplementaryItems(int $itemId, int $limit = 5): array
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(30);

        // Find items frequently ordered with this item
        $bundles = $this->menuIntelligenceService->getBundleOpportunities($startDate, $endDate);

        $complementary = [];
        foreach ($bundles['bundle_opportunities'] as $bundle) {
            // Check if our item is in this bundle
            $itemIds = array_map(function($item) {
                return MenuItem::where('name', $item)->value('id');
            }, $bundle['items']);

            if (in_array($itemId, $itemIds)) {
                // Find the other item(s)
                foreach ($itemIds as $otherId) {
                    if ($otherId != $itemId) {
                        $item = MenuItem::find($otherId);
                        if ($item && $item->is_available) {
                            $complementary[] = [
                                'item_id' => $item->id,
                                'name' => $item->name,
                                'price' => $item->price,
                                'frequency' => $bundle['frequency'],
                                'suggested_discount' => $bundle['discount_percentage'],
                            ];
                        }
                    }
                }
            }
        }

        // Sort by frequency
        usort($complementary, function($a, $b) {
            return $b['frequency'] <=> $a['frequency'];
        });

        return [
            'complementary_items' => array_slice($complementary, 0, $limit),
            'item_id' => $itemId,
        ];
    }

    /**
     * Get trending menu items
     *
     * @param int $days
     * @param int $limit
     * @return array
     */
    public function getTrendingItems(int $days = 7, int $limit = 10): array
    {
        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays($days);

        $analysis = $this->menuIntelligenceService->getMenuPerformanceAnalysis($startDate, $endDate);

        // Filter for items with increasing trend
        $trendingItems = collect($analysis['all_items'])
            ->filter(function($item) {
                return isset($item['trend']['direction'])
                    && $item['trend']['direction'] === 'increasing'
                    && $item['is_available']
                    && $item['metrics']['order_count'] > 0;
            })
            ->sortByDesc(function($item) {
                return $item['trend']['percentage_change'] ?? 0;
            })
            ->take($limit)
            ->map(function($item) {
                return [
                    'item_id' => $item['item_id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'category' => $item['category'],
                    'trend_percentage' => $item['trend']['percentage_change'] ?? 0,
                    'recent_orders' => $item['metrics']['order_count'],
                    'performance_score' => $item['performance_score'],
                ];
            })
            ->values()
            ->toArray();

        return [
            'trending_items' => $trendingItems,
            'period' => "{$days} days",
        ];
    }

    /**
     * Get seasonal menu recommendations
     *
     * @return array
     */
    public function getSeasonalRecommendations(): array
    {
        $currentMonth = Carbon::now()->month;
        $season = $this->determineSeason($currentMonth);

        // Get items with seasonal patterns
        $menuItems = MenuItem::where('is_available', 1)->get();
        $seasonalItems = [];

        foreach ($menuItems as $item) {
            $trends = $this->menuIntelligenceService->getSeasonalTrends($item->id, 12);

            if ($trends['seasonality_detected']) {
                // Check if current month is a peak month
                $peakMonths = array_map('strtolower', $trends['peak_months']);
                $currentMonthName = strtolower(Carbon::now()->format('F'));

                if (in_array($currentMonthName, $peakMonths)) {
                    $seasonalItems[] = [
                        'item_id' => $item->id,
                        'name' => $item->name,
                        'price' => $item->price,
                        'peak_months' => $trends['peak_months'],
                        'reason' => 'Peak season for this item',
                    ];
                }
            }
        }

        return [
            'seasonal_items' => $seasonalItems,
            'current_season' => $season,
            'current_month' => Carbon::now()->format('F'),
        ];
    }

    /**
     * Generate recommendation reason
     *
     * @param array $aiRecommendation
     * @param array $performance
     * @param array $context
     * @return string
     */
    private function generateRecommendationReason(array $aiRecommendation, array $performance, array $context): string
    {
        $reasons = [];

        // AI-based reason
        if (isset($aiRecommendation['reason'])) {
            $reasons[] = $aiRecommendation['reason'];
        } elseif ($aiRecommendation['score'] > 0.8) {
            $reasons[] = 'Highly personalized for you';
        }

        // Performance-based reason
        if ($performance['performance_score'] > 80) {
            $reasons[] = 'Popular choice';
        }

        // Rating-based reason
        if ($performance['metrics']['rating'] >= 4.5) {
            $reasons[] = 'Highly rated';
        }

        // Profit margin (for internal use, not shown to customer)
        if ($performance['metrics']['profit_margin'] > 60) {
            $reasons[] = 'Chef\'s special';
        }

        // Trend-based reason
        if (isset($performance['trend']['direction']) && $performance['trend']['direction'] === 'increasing') {
            $reasons[] = 'Trending now';
        }

        return empty($reasons) ? 'Recommended for you' : implode(' • ', $reasons);
    }

    /**
     * Get popularity rank
     *
     * @param int $itemId
     * @param array $menuPerformance
     * @return int
     */
    private function getPopularityRank(int $itemId, array $menuPerformance): int
    {
        $allItems = $menuPerformance['all_items'];

        foreach ($allItems as $index => $item) {
            if ($item['item_id'] == $itemId) {
                return $index + 1;
            }
        }

        return 9999;
    }

    /**
     * Determine season based on month
     *
     * @param int $month
     * @return string
     */
    private function determineSeason(int $month): string
    {
        // For Malaysia (tropical climate, monsoon seasons)
        if (in_array($month, [11, 12, 1, 2, 3])) {
            return 'Northeast Monsoon (Wet Season)';
        } elseif (in_array($month, [5, 6, 7, 8, 9])) {
            return 'Southwest Monsoon';
        } else {
            return 'Inter-monsoon';
        }
    }
}
