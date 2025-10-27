<?php

namespace App\Providers;

use App\Services\RecommendationService;
use App\Services\SimpleRecommendationService;
use Illuminate\Support\ServiceProvider;

class RecommendationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register SimpleRecommendationService first
        $this->app->singleton(SimpleRecommendationService::class, function ($app) {
            return new SimpleRecommendationService();
        });

        // Register RecommendationService with SimpleRecommendationService dependency
        $this->app->singleton(RecommendationService::class, function ($app) {
            return new RecommendationService($app->make(SimpleRecommendationService::class));
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}