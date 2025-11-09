<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\PromotionUsedEvent;
use App\Events\RewardRedeemedEvent;
use App\Events\TableBookingCreatedEvent;
use App\Events\OrderStatusUpdatedEvent;
use App\Events\OrderCreatedEvent;
use App\Events\AnalyticsRefreshEvent;
use App\Events\QRSessionCompletedEvent;
use App\Listeners\UpdateAnalyticsOnPromotionUsed;
use App\Listeners\UpdateAnalyticsOnRewardRedeemed;
use App\Listeners\UpdateAnalyticsOnTableBooking;
use App\Listeners\UpdateAnalyticsOnQRSessionCompleted;
use App\Listeners\RefreshAnalyticsData;
use App\Listeners\SendOrderStatusNotification;
use App\Listeners\SendNewOrderNotification;
use App\Listeners\SendReservationNotification;
use App\Listeners\SendNewReservationNotificationToAdmin;

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

// Problem Alert Events and Listeners
use App\Events\OrderDelayedEvent;
use App\Events\OrderCancelledByKitchenEvent;
use App\Events\PaymentFailedEvent;
use App\Events\OrderItemUnavailableEvent;
use App\Listeners\SendOrderDelayNotification;
use App\Listeners\SendKitchenCancellationNotification;
use App\Listeners\SendPaymentFailureNotification;
use App\Listeners\SendItemUnavailableNotification;

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
            SendReservationNotification::class,
            SendNewReservationNotificationToAdmin::class,
        ],
        // 🔥 GENERIC ANALYTICS REFRESH EVENT - handles all revenue-affecting scenarios
        AnalyticsRefreshEvent::class => [
            RefreshAnalyticsData::class,
        ],

        // FCM Notifications
        OrderStatusUpdatedEvent::class => [
            SendOrderStatusNotification::class,
        ],
        OrderCreatedEvent::class => [
            SendNewOrderNotification::class,
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

        // Problem Alert System
        OrderDelayedEvent::class => [
            SendOrderDelayNotification::class,
        ],
        OrderCancelledByKitchenEvent::class => [
            SendKitchenCancellationNotification::class,
        ],
        PaymentFailedEvent::class => [
            SendPaymentFailureNotification::class,
        ],
        OrderItemUnavailableEvent::class => [
            SendItemUnavailableNotification::class,
        ],
        
        // QR Session Events
        QRSessionCompletedEvent::class => [
            UpdateAnalyticsOnQRSessionCompleted::class,
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
