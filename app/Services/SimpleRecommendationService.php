<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Simple rule-based recommendation service
 * Used as fallback when AI service is unavailable
 */
class SimpleRecommendationService
{
    /**
     * Get recommendations using simple rule-based logic
     *
     * @param int $userId User ID to get recommendations for
     * @param int $limit Number of recommendations to return
     * @param array|null $excludeItems Menu item IDs to exclude
     * @return array Menu item IDs
     */
    public function getRecommendations(int $userId, int $limit = 10, ?array $excludeItems = null): array
    {
        $excludeItems = $excludeItems ?? [];
        $recommendations = [];

        // Rule 1: Items frequently ordered together with user's favorites
        $companionItems = $this->getCompanionItems($userId, $excludeItems);
        $recommendations = array_merge($recommendations, $companionItems);

        // Rule 2: Time-based recommendations (breakfast, lunch, dinner)
        if (count($recommendations) < $limit) {
            $timeBasedItems = $this->getTimeBasedRecommendations($excludeItems);
            $recommendations = array_merge($recommendations, $timeBasedItems);
        }

        // Rule 3: Category balancing (if user ordered mains, suggest drinks/sides)
        if (count($recommendations) < $limit) {
            $balancedItems = $this->getCategoryBalancedItems($userId, $excludeItems);
            $recommendations = array_merge($recommendations, $balancedItems);
        }

        // Rule 4: Trending items (popular in last 7 days)
        if (count($recommendations) < $limit) {
            $trendingItems = $this->getTrendingItems($excludeItems);
            $recommendations = array_merge($recommendations, $trendingItems);
        }

        // Rule 5: Popular items (all-time popular)
        if (count($recommendations) < $limit) {
            $popularItems = $this->getPopularItems(10, $excludeItems);
            $recommendations = array_merge($recommendations, $popularItems);
        }

        // Remove duplicates and limit
        $recommendations = array_values(array_unique($recommendations));
        $recommendations = array_diff($recommendations, $excludeItems);

        return array_slice($recommendations, 0, $limit);
    }

    /**
     * Get items frequently ordered together with user's favorite items
     */
    private function getCompanionItems(int $userId, array $excludeItems): array
    {
        try {
            // Get user's top 3 most ordered items
            $favoriteItems = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->where('orders.user_id', $userId)
                ->where('orders.order_status', 'completed')
                ->whereNotIn('order_items.menu_item_id', $excludeItems)
                ->select('order_items.menu_item_id', DB::raw('SUM(order_items.quantity) as total_quantity'))
                ->groupBy('order_items.menu_item_id')
                ->orderBy('total_quantity', 'desc')
                ->limit(3)
                ->pluck('menu_item_id')
                ->toArray();

            if (empty($favoriteItems)) {
                return [];
            }

            // Find items frequently ordered in same order as user's favorites
            $companionItems = DB::table('order_items as oi1')
                ->join('order_items as oi2', 'oi1.order_id', '=', 'oi2.order_id')
                ->join('orders', 'oi1.order_id', '=', 'orders.id')
                ->join('menu_items', 'oi2.menu_item_id', '=', 'menu_items.id')
                ->whereIn('oi1.menu_item_id', $favoriteItems)
                ->whereNotIn('oi2.menu_item_id', $favoriteItems)
                ->whereNotIn('oi2.menu_item_id', $excludeItems)
                ->where('orders.order_status', 'completed')
                ->where('menu_items.availability', true)
                ->select('oi2.menu_item_id', DB::raw('COUNT(*) as frequency'))
                ->groupBy('oi2.menu_item_id')
                ->orderBy('frequency', 'desc')
                ->limit(5)
                ->pluck('menu_item_id')
                ->toArray();

            return $companionItems;

        } catch (\Exception $e) {
            Log::warning('Failed to get companion items', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get time-based recommendations (breakfast, lunch, dinner)
     */
    private function getTimeBasedRecommendations(array $excludeItems): array
    {
        try {
            $hour = Carbon::now()->hour;
            $categoryKeywords = [];

            // Breakfast (6 AM - 10 AM)
            if ($hour >= 6 && $hour < 10) {
                $categoryKeywords = ['breakfast', 'coffee', 'tea', 'pastry', 'egg'];
            }
            // Lunch (11 AM - 2 PM)
            elseif ($hour >= 11 && $hour < 14) {
                $categoryKeywords = ['lunch', 'salad', 'sandwich', 'soup', 'rice'];
            }
            // Dinner (5 PM - 9 PM)
            elseif ($hour >= 17 && $hour < 21) {
                $categoryKeywords = ['dinner', 'steak', 'pasta', 'burger', 'pizza'];
            }
            // Snack time
            else {
                $categoryKeywords = ['snack', 'dessert', 'drink', 'appetizer'];
            }

            if (empty($categoryKeywords)) {
                return [];
            }

            $items = DB::table('menu_items')
                ->leftJoin('categories', 'menu_items.category_id', '=', 'categories.id')
                ->where('menu_items.availability', true)
                ->whereNotIn('menu_items.id', $excludeItems)
                ->where(function ($query) use ($categoryKeywords) {
                    foreach ($categoryKeywords as $keyword) {
                        $query->orWhere('menu_items.name', 'LIKE', "%{$keyword}%")
                              ->orWhere('menu_items.description', 'LIKE', "%{$keyword}%")
                              ->orWhere('categories.name', 'LIKE', "%{$keyword}%");
                    }
                })
                ->select('menu_items.id')
                ->limit(5)
                ->pluck('id')
                ->toArray();

            return $items;

        } catch (\Exception $e) {
            Log::warning('Failed to get time-based recommendations', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get category-balanced items
     * If user ordered mains, suggest drinks/sides/desserts
     */
    private function getCategoryBalancedItems(int $userId, array $excludeItems): array
    {
        try {
            // Get categories user has ordered from recently
            $userCategories = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->where('orders.user_id', $userId)
                ->where('orders.order_status', 'completed')
                ->where('orders.order_time', '>=', Carbon::now()->subDays(30))
                ->select('menu_items.category_id')
                ->distinct()
                ->pluck('category_id')
                ->toArray();

            if (empty($userCategories)) {
                return [];
            }

            // Find complementary categories
            // Get categories user hasn't ordered from
            $complementaryItems = DB::table('menu_items')
                ->leftJoin('categories', 'menu_items.category_id', '=', 'categories.id')
                ->where('menu_items.availability', true)
                ->whereNotIn('menu_items.id', $excludeItems)
                ->whereNotIn('menu_items.category_id', $userCategories)
                ->where(function ($query) {
                    // Prioritize drinks, sides, desserts
                    $query->where('categories.name', 'LIKE', '%drink%')
                          ->orWhere('categories.name', 'LIKE', '%side%')
                          ->orWhere('categories.name', 'LIKE', '%dessert%')
                          ->orWhere('categories.name', 'LIKE', '%appetizer%');
                })
                ->select('menu_items.id')
                ->limit(5)
                ->pluck('id')
                ->toArray();

            return $complementaryItems;

        } catch (\Exception $e) {
            Log::warning('Failed to get category-balanced items', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get trending items (popular in last 7 days)
     */
    private function getTrendingItems(array $excludeItems): array
    {
        try {
            $items = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->where('orders.order_status', 'completed')
                ->where('orders.order_time', '>=', Carbon::now()->subDays(7))
                ->where('menu_items.availability', true)
                ->whereNotIn('menu_items.id', $excludeItems)
                ->select('menu_items.id', DB::raw('COUNT(*) as order_count'))
                ->groupBy('menu_items.id')
                ->orderBy('order_count', 'desc')
                ->limit(5)
                ->pluck('id')
                ->toArray();

            return $items;

        } catch (\Exception $e) {
            Log::warning('Failed to get trending items', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get popular items (all-time popular)
     */
    public function getPopularItems(int $limit = 10, array $excludeItems = []): array
    {
        try {
            $items = DB::table('order_items')
                ->join('orders', 'order_items.order_id', '=', 'orders.id')
                ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->where('orders.order_status', 'completed')
                ->where('orders.order_time', '>=', Carbon::now()->subDays(30))
                ->where('menu_items.availability', true)
                ->whereNotIn('menu_items.id', $excludeItems)
                ->select('menu_items.id', DB::raw('COUNT(*) as order_count'))
                ->groupBy('menu_items.id')
                ->orderBy('order_count', 'desc')
                ->limit($limit)
                ->pluck('id')
                ->toArray();

            return $items;

        } catch (\Exception $e) {
            Log::warning('Failed to get popular items', [
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Get recommendations with detailed reasoning
     * Useful for debugging or showing users why items were recommended
     */
    public function getRecommendationsWithReasons(int $userId, int $limit = 10, ?array $excludeItems = null): array
    {
        $excludeItems = $excludeItems ?? [];
        $recommendations = [];

        // Rule 1: Companion items
        $companionItems = $this->getCompanionItems($userId, $excludeItems);
        foreach ($companionItems as $itemId) {
            $recommendations[] = [
                'menu_item_id' => $itemId,
                'reason' => 'Frequently ordered with your favorites',
                'score' => 0.9
            ];
        }

        // Rule 2: Time-based
        $timeBasedItems = $this->getTimeBasedRecommendations($excludeItems);
        foreach ($timeBasedItems as $itemId) {
            if (!in_array($itemId, array_column($recommendations, 'menu_item_id'))) {
                $hour = Carbon::now()->hour;
                $timeOfDay = $hour >= 6 && $hour < 10 ? 'breakfast' :
                            ($hour >= 11 && $hour < 14 ? 'lunch' :
                            ($hour >= 17 && $hour < 21 ? 'dinner' : 'snack'));

                $recommendations[] = [
                    'menu_item_id' => $itemId,
                    'reason' => "Perfect for {$timeOfDay}",
                    'score' => 0.7
                ];
            }
        }

        // Rule 3: Category balance
        $balancedItems = $this->getCategoryBalancedItems($userId, $excludeItems);
        foreach ($balancedItems as $itemId) {
            if (!in_array($itemId, array_column($recommendations, 'menu_item_id'))) {
                $recommendations[] = [
                    'menu_item_id' => $itemId,
                    'reason' => 'Complements your usual orders',
                    'score' => 0.6
                ];
            }
        }

        // Rule 4: Trending
        $trendingItems = $this->getTrendingItems($excludeItems);
        foreach ($trendingItems as $itemId) {
            if (!in_array($itemId, array_column($recommendations, 'menu_item_id'))) {
                $recommendations[] = [
                    'menu_item_id' => $itemId,
                    'reason' => 'Trending this week',
                    'score' => 0.5
                ];
            }
        }

        // Rule 5: Popular
        $popularItems = $this->getPopularItems(10, $excludeItems);
        foreach ($popularItems as $itemId) {
            if (!in_array($itemId, array_column($recommendations, 'menu_item_id'))) {
                $recommendations[] = [
                    'menu_item_id' => $itemId,
                    'reason' => 'Customer favorite',
                    'score' => 0.4
                ];
            }
        }

        // Limit results
        return array_slice($recommendations, 0, $limit);
    }
}
