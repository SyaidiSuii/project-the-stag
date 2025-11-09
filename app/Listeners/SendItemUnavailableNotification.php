<?php

namespace App\Listeners;

use App\Events\OrderItemUnavailableEvent;
use App\Services\ProblemAlertService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendItemUnavailableNotification implements ShouldQueue
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
    public function handle(OrderItemUnavailableEvent $event): void
    {
        $this->problemAlertService->sendItemUnavailableAlert(
            $event->order,
            $event->unavailableItems,
            $event->reportedBy
        );
    }
}
