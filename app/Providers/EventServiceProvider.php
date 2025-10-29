<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\PromotionUsedEvent;
use App\Events\RewardRedeemedEvent;
use App\Events\TableBookingCreatedEvent;
use App\Events\AnalyticsRefreshEvent;
use App\Listeners\UpdateAnalyticsOnPromotionUsed;
use App\Listeners\UpdateAnalyticsOnRewardRedeemed;
use App\Listeners\UpdateAnalyticsOnTableBooking;
use App\Listeners\RefreshAnalyticsData;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        // Registered::class => [
        //     SendEmailVerificationNotification::class,
        // ],
        PromotionUsedEvent::class => [
            UpdateAnalyticsOnPromotionUsed::class,
        ],
        RewardRedeemedEvent::class => [
            UpdateAnalyticsOnRewardRedeemed::class,
        ],
        TableBookingCreatedEvent::class => [
            UpdateAnalyticsOnTableBooking::class,
        ],
        // 🔥 GENERIC ANALYTICS REFRESH EVENT - handles all revenue-affecting scenarios
        AnalyticsRefreshEvent::class => [
            RefreshAnalyticsData::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
