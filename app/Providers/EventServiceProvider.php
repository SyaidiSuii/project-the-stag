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

// PHASE 5: Loyalty Events and Listeners
use App\Events\Loyalty\PointsAwarded;
use App\Events\Loyalty\RewardRedeemed;
use App\Events\Loyalty\VoucherIssued;
use App\Listeners\Loyalty\SendPointsAwardedNotification;
use App\Listeners\Loyalty\SendRewardRedeemedNotification;
use App\Listeners\Loyalty\SendVoucherIssuedNotification;

// PHASE 7: Advanced Loyalty Events
use App\Events\Loyalty\TierUpgraded;
use App\Listeners\Loyalty\SendTierUpgradedNotification;

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

        // PHASE 5: Loyalty Program Events
        PointsAwarded::class => [
            SendPointsAwardedNotification::class,
        ],
        RewardRedeemed::class => [
            SendRewardRedeemedNotification::class,
        ],
        VoucherIssued::class => [
            SendVoucherIssuedNotification::class,
        ],

        // PHASE 7: Advanced Loyalty Features
        TierUpgraded::class => [
            SendTierUpgradedNotification::class,
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
