<?php

namespace App\Listeners;

use App\Events\OrderDelayedEvent;
use App\Services\ProblemAlertService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendOrderDelayNotification implements ShouldQueue
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
    public function handle(OrderDelayedEvent $event): void
    {
        $this->problemAlertService->sendDelayAlert(
            $event->order,
            $event->delayMinutes
        );
    }
}
