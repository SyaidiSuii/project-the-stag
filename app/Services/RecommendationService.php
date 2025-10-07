<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class RecommendationService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('services.ai_recommender.base_url', 'http://localhost:8000');
        $this->timeout = config('services.ai_recommender.timeout', 30);
    }

    /**
     * Get recommendations for a user with context data
     */
    public function getRecommendations(int $userId, ?float $alpha = null, int $topn = 5, ?array $context = null): array
    {
        try {
            // Build user context if not provided
            $userContext = $context ?? $this->buildUserContext($userId);
            
            $requestData = [
                'user_id' => $userId,
                'topn' => $topn,
                'context' => $userContext
            ];

            if ($alpha !== null) {
                $requestData['alpha'] = $alpha;
            }

            // Try enhanced POST request first
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/recommend", $requestData);

            if ($response->successful()) {
                return $response->json();
            }

            // Fallback to old GET method if POST fails
            Log::warning('Enhanced recommendation failed, trying fallback', [
                'status' => $response->status(),
                'user_id' => $userId
            ]);
            
            return $this->getRecommendationsFallback($userId, $alpha, $topn);

        } catch (Exception $e) {
            Log::error('Recommendation service error', [
                'message' => $e->getMessage(),
                'user_id' => $userId,
                'alpha' => $alpha,
                'topn' => $topn
            ]);

            // Try fallback method
            return $this->getRecommendationsFallback($userId, $alpha, $topn);
        }
    }

    /**
     * Fallback to simple GET request (old method)
     */
    private function getRecommendationsFallback(int $userId, ?float $alpha = null, int $topn = 5): array
    {
        $params = ['topn' => $topn];
        if ($alpha !== null) {
            $params['alpha'] = $alpha;
        }

        $response = Http::timeout($this->timeout)
            ->get("{$this->baseUrl}/recommend/{$userId}", $params);

        if ($response->successful()) {
            return $response->json();
        }

        throw new Exception("Both enhanced and fallback recommendation requests failed");
    }

    /**
     * Trigger model retraining
     */
    public function retrain(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/retrain");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('AI Recommender retrain error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            throw new Exception("Retrain request failed with status: " . $response->status());

        } catch (Exception $e) {
            Log::error('Retrain service error', [
                'message' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Trigger immediate model update with latest data
     */
    public function triggerModelUpdate(): array
    {
        try {
            Log::info('Triggering AI model update with latest database data');
            
            // Trigger immediate model retrain with latest data
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/retrain");

            if ($response->successful()) {
                $result = $response->json();
                Log::info('Model retrained successfully with latest data', [
                    'records_used' => $result['records_used'] ?? 'unknown',
                    'status' => $result['status'] ?? 'success'
                ]);
                return $result;
            }

            Log::error('Model retrain failed', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            throw new Exception("Model retrain failed with status: " . $response->status());

        } catch (Exception $e) {
            Log::error('Model retrain error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Auto-retrain model after order completion
     */
    public function onOrderCompleted(int $orderId): void
    {
        try {
            Log::info('Order completed, triggering model update', ['order_id' => $orderId]);
            
            // Use background job for non-blocking retrain
            dispatch(function () {
                try {
                    $this->triggerModelUpdate();
                } catch (Exception $e) {
                    Log::warning('Background model retrain failed', [
                        'error' => $e->getMessage()
                    ]);
                }
            })->onQueue('ai-training');
            
        } catch (Exception $e) {
            Log::warning('Auto-retrain dispatch failed', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Auto-retrain model after menu updates
     */
    public function onMenuUpdated(string $action, int $menuItemId): void
    {
        try {
            Log::info('Menu updated, triggering model update', [
                'action' => $action,
                'menu_item_id' => $menuItemId
            ]);
            
            // Use background job for non-blocking retrain
            dispatch(function () use ($action, $menuItemId) {
                try {
                    $this->triggerModelUpdate();
                } catch (Exception $e) {
                    Log::warning('Background model retrain after menu update failed', [
                        'action' => $action,
                        'menu_item_id' => $menuItemId,
                        'error' => $e->getMessage()
                    ]);
                }
            })->onQueue('ai-training');
            
        } catch (Exception $e) {
            Log::warning('Auto-retrain after menu update dispatch failed', [
                'action' => $action,
                'menu_item_id' => $menuItemId,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get AI service status and model information
     */
    public function getServiceStatus(): array
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/model/status");

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'service_available' => false,
                'error' => 'Service unavailable',
                'status_code' => $response->status()
            ];

        } catch (Exception $e) {
            return [
                'service_available' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Force model retrain (for admin use)
     */
    public function forceRetrain(): array
    {
        try {
            Log::info('Force retrain initiated by admin');
            
            $result = $this->triggerModelUpdate();
            
            Log::info('Force retrain completed', $result);
            
            return [
                'success' => true,
                'message' => 'Model retrain initiated successfully',
                'result' => $result
            ];
            
        } catch (Exception $e) {
            Log::error('Force retrain failed', ['error' => $e->getMessage()]);
            
            return [
                'success' => false,
                'message' => 'Model retrain failed',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Check if AI service is healthy
     */
    public function healthCheck(): bool
    {
        try {
            $response = Http::timeout(5)
                ->get("{$this->baseUrl}/");

            return $response->successful();

        } catch (Exception $e) {
            Log::warning('AI service health check failed', [
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get recommendations with fallback to popular items
     */
    public function getRecommendationsWithFallback(int $userId, ?float $alpha = null, int $topn = 5, ?array $context = null): array
    {
        try {
            return $this->getRecommendations($userId, $alpha, $topn, $context);
        } catch (Exception $e) {
            Log::warning('Using fallback recommendations', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            // Return fallback recommendations (you can implement this based on your business logic)
            return $this->getFallbackRecommendations($topn);
        }
    }

    /**
     * Fallback recommendations when AI service is unavailable
     */
    private function getFallbackRecommendations(int $topn): array
    {
        // This is a placeholder - implement based on your business logic
        // Could be popular items, recently added items, etc.
        return [
            'user_id' => null,
            'recommendations' => [],
            'fallback' => true,
            'message' => 'AI service unavailable, using fallback recommendations'
        ];
    }

    /**
     * Build comprehensive user context for recommendations
     */
    private function buildUserContext(int $userId): array
    {
        return [
            'current_cart' => $this->getCurrentCart($userId),
            'recent_orders' => $this->getRecentOrders($userId),
            'available_menu' => $this->getAvailableMenu(),
            'favorites' => $this->getUserFavorites($userId), // placeholder for future
            'view_history' => $this->getViewHistory($userId), // placeholder for future
        ];
    }

    /**
     * Get current cart items for user
     */
    private function getCurrentCart(int $userId): array
    {
        try {
            return DB::table('user_carts')
                ->join('menu_items', 'user_carts.menu_item_id', '=', 'menu_items.id')
                ->where('user_carts.user_id', $userId)
                ->whereNull('user_carts.deleted_at')
                ->select([
                    'menu_items.id as menu_id',
                    'menu_items.name',
                    'menu_items.category',
                    'user_carts.quantity',
                    'user_carts.unit_price',
                    'user_carts.special_notes'
                ])
                ->get()
                ->toArray();
        } catch (Exception $e) {
            Log::warning('Failed to get current cart', ['user_id' => $userId, 'error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get recent orders history for user (last 30 days)
     */
    private function getRecentOrders(int $userId): array
    {
        try {
            return DB::table('orders')
                ->join('order_items', 'orders.id', '=', 'order_items.order_id')
                ->join('menu_items', 'order_items.menu_item_id', '=', 'menu_items.id')
                ->where('orders.user_id', $userId)
                ->where('orders.order_status', '!=', 'cancelled')
                ->where('orders.created_at', '>=', now()->subDays(30))
                ->whereNull('orders.deleted_at')
                ->whereNull('order_items.deleted_at')
                ->select([
                    'menu_items.id as menu_id',
                    'menu_items.name',
                    'menu_items.category',
                    'order_items.quantity',
                    'orders.created_at as order_date',
                    'orders.order_type',
                    DB::raw('COUNT(*) as frequency')
                ])
                ->groupBy([
                    'menu_items.id', 'menu_items.name', 'menu_items.category',
                    'order_items.quantity', 'orders.created_at', 'orders.order_type'
                ])
                ->orderBy('orders.created_at', 'desc')
                ->limit(20)
                ->get()
                ->toArray();
        } catch (Exception $e) {
            Log::warning('Failed to get recent orders', ['user_id' => $userId, 'error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get available menu items
     */
    private function getAvailableMenu(): array
    {
        try {
            return DB::table('menu_items')
                ->where('availability', true)
                ->whereNull('deleted_at')
                ->select([
                    'id as menu_id',
                    'name',
                    'category',
                    'price',
                    'is_featured',
                    'rating_average',
                    'rating_count'
                ])
                ->orderBy('category')
                ->orderBy('name')
                ->get()
                ->toArray();
        } catch (Exception $e) {
            Log::warning('Failed to get available menu', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get user favorites (placeholder for future implementation)
     */
    private function getUserFavorites(int $userId): array
    {
        // TODO: Implement when favorites feature is added
        // This would query a user_favorites table or similar
        return [];
    }

    /**
     * Get user view history (placeholder for future implementation)
     */
    private function getViewHistory(int $userId): array
    {
        // TODO: Implement when view tracking feature is added
        // This would query a menu_views or user_activity table
        return [];
    }
}