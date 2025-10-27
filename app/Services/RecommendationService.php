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
    private SimpleRecommendationService $simpleRecommender;

    public function __construct(SimpleRecommendationService $simpleRecommender)
    {
        $this->baseUrl = config('services.ai_recommender.base_url', 'http://localhost:8000');
        $this->timeout = config('services.ai_recommender.timeout', 30);
        $this->simpleRecommender = $simpleRecommender;
    }

    /**
     * Get recommendations for a user with smart fallback
     *
     * Fallback strategy:
     * 1. Try AI service (Python collaborative filtering)
     * 2. If AI unavailable -> Use smart rule-based recommendations
     * 3. If both fail -> Use popular items
     *
     * @param int $userId User ID to get recommendations for
     * @param int $limit Number of recommendations to return (default 10)
     * @param array|null $excludeItems Menu item IDs to exclude from recommendations
     * @return array Menu item IDs
     */
    public function getRecommendations(int $userId, int $limit = 10, ?array $excludeItems = null): array
    {
        // Check if AI service is enabled in config
        $aiEnabled = config('services.ai_recommender.enabled', true);

        if ($aiEnabled) {
            try {
                $requestData = [
                    'user_id' => $userId,
                    'limit' => $limit,
                ];

                if ($excludeItems !== null) {
                    $requestData['exclude_items'] = $excludeItems;
                }

                $response = Http::timeout($this->timeout)
                    ->post("{$this->baseUrl}/recommend", $requestData);

                if ($response->successful()) {
                    $data = $response->json();

                    // Extract menu item IDs from recommendations
                    if (isset($data['recommendations']) && is_array($data['recommendations'])) {
                        $aiRecommendations = array_map(function($item) {
                            return $item['menu_item_id'];
                        }, $data['recommendations']);

                        Log::info('AI recommendations successful', [
                            'user_id' => $userId,
                            'count' => count($aiRecommendations)
                        ]);

                        return $aiRecommendations;
                    }
                }

                Log::warning('AI recommendation failed, falling back to smart rules', [
                    'status' => $response->status(),
                    'user_id' => $userId
                ]);

            } catch (Exception $e) {
                Log::warning('AI service unavailable, falling back to smart rules', [
                    'message' => $e->getMessage(),
                    'user_id' => $userId,
                ]);
            }
        }

        // Fallback to smart rule-based recommendations
        Log::info('Using smart rule-based recommendations', ['user_id' => $userId]);
        return $this->simpleRecommender->getRecommendations($userId, $limit, $excludeItems);
    }

    /**
     * Get recommendations with detailed info (scores included)
     *
     * @param int $userId User ID to get recommendations for
     * @param int $limit Number of recommendations to return
     * @param array|null $excludeItems Menu item IDs to exclude
     * @return array Array with detailed recommendation info including scores
     */
    public function getRecommendationsWithScores(int $userId, int $limit = 10, ?array $excludeItems = null): array
    {
        try {
            $requestData = [
                'user_id' => $userId,
                'limit' => $limit,
            ];

            if ($excludeItems !== null) {
                $requestData['exclude_items'] = $excludeItems;
            }

            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/recommend", $requestData);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'success' => false,
                'recommendations' => [],
                'fallback' => true
            ];

        } catch (Exception $e) {
            Log::error('Recommendation service error', [
                'message' => $e->getMessage(),
                'user_id' => $userId,
            ]);

            return [
                'success' => false,
                'recommendations' => [],
                'fallback' => true
            ];
        }
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
                ->get("{$this->baseUrl}/health");

            return $response->successful();

        } catch (Exception $e) {
            Log::warning('AI service health check failed', [
                'message' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Get recommendations with reasoning (for debugging/display)
     */
    public function getRecommendationsWithReasons(int $userId, int $limit = 10, ?array $excludeItems = null): array
    {
        // Always use simple recommender for detailed reasons
        // AI doesn't provide human-readable reasoning
        return $this->simpleRecommender->getRecommendationsWithReasons($userId, $limit, $excludeItems);
    }

}