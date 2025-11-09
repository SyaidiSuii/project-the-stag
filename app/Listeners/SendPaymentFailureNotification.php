<?php

namespace App\Listeners;

use App\Events\PaymentFailedEvent;
use App\Services\ProblemAlertService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendPaymentFailureNotification implements ShouldQueue
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
    public function handle(PaymentFailedEvent $event): void
    {
        $this->problemAlertService->sendPaymentFailureAlert(
            $event->order,
            $event->failureReason
        );
    }
}
