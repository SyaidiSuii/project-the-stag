<?php

namespace App\Listeners;

use App\Events\OrderCancelledByKitchenEvent;
use App\Services\ProblemAlertService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendKitchenCancellationNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected ProblemAlertService $problemAlertService;

    /**
     * Create the event listener.
     */
    public function __construct(ProblemAlertService $problemAlertService)
    {
        $this->problemAlertService = $problemAlertService;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCancelledByKitchenEvent $event): void
    {
        $this->problemAlertService->sendKitchenCancellationAlert(
            $event->order,
            $event->reason,
            $event->cancelledBy
        );
    }
}
