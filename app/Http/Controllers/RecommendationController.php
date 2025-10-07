<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Exception;

class RecommendationController extends Controller
{
    private RecommendationService $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Get recommendations for a user
     */
    public function getRecommendations(Request $request, int $userId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'alpha' => 'nullable|numeric|min:0|max:1',
                'topn' => 'nullable|integer|min:1|max:50'
            ]);

            $alpha = $validated['alpha'] ?? null;
            $topn = $validated['topn'] ?? 5;

            $recommendations = $this->recommendationService->getRecommendations(
                $userId,
                $alpha,
                $topn
            );

            return response()->json([
                'success' => true,
                'data' => $recommendations
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get recommendations with fallback
     */
    public function getRecommendationsWithFallback(Request $request, int $userId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'alpha' => 'nullable|numeric|min:0|max:1',
                'topn' => 'nullable|integer|min:1|max:50'
            ]);

            $alpha = $validated['alpha'] ?? null;
            $topn = $validated['topn'] ?? 5;

            $recommendations = $this->recommendationService->getRecommendationsWithFallback(
                $userId,
                $alpha,
                $topn
            );

            return response()->json([
                'success' => true,
                'data' => $recommendations
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Trigger model retraining
     */
    public function retrain(): JsonResponse
    {
        try {
            $result = $this->recommendationService->retrain();

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to trigger retraining',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check AI service health
     */
    public function health(): JsonResponse
    {
        $isHealthy = $this->recommendationService->healthCheck();

        return response()->json([
            'success' => true,
            'data' => [
                'ai_service_healthy' => $isHealthy,
                'status' => $isHealthy ? 'online' : 'offline'
            ]
        ], $isHealthy ? 200 : 503);
    }

    /**
     * Get user recommendations for authenticated user
     */
    public function getMyRecommendations(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $validated = $request->validate([
                'alpha' => 'nullable|numeric|min:0|max:1',
                'topn' => 'nullable|integer|min:1|max:50'
            ]);

            $alpha = $validated['alpha'] ?? null;
            $topn = $validated['topn'] ?? 5;

            $recommendations = $this->recommendationService->getRecommendationsWithFallback(
                $user->id,
                $alpha,
                $topn
            );

            return response()->json([
                'success' => true,
                'data' => $recommendations
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors()
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get recommendations',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get AI service status (Admin)
     */
    public function getServiceStatus(): JsonResponse
    {
        try {
            $status = $this->recommendationService->getServiceStatus();
            $isHealthy = $this->recommendationService->healthCheck();
            
            return response()->json([
                'success' => true,
                'data' => array_merge($status, [
                    'service_healthy' => $isHealthy,
                    'checked_at' => now()->toISOString()
                ])
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get service status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Force retrain AI model (Admin)
     */
    public function forceRetrain(): JsonResponse
    {
        try {
            $result = $this->recommendationService->forceRetrain();
            
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'data' => $result['result'] ?? null
            ], $result['success'] ? 200 : 500);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to force retrain',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test AI service connection (Admin)
     */
    public function testConnection(): JsonResponse
    {
        try {
            $isHealthy = $this->recommendationService->healthCheck();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'service_healthy' => $isHealthy,
                    'service_url' => config('services.ai_recommender.base_url'),
                    'enabled' => config('services.ai_recommender.enabled', true),
                    'tested_at' => now()->toISOString()
                ]
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Connection test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}