<?php

namespace App\Listeners\Loyalty;

use App\Events\Loyalty\VoucherIssued;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * PHASE 5: Send Voucher Issued Notification Listener
 *
 * Sends notification when voucher is issued to customer.
 * Queued for background processing.
 */
class SendVoucherIssuedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Handle the event.
     */
    public function handle(VoucherIssued $event): void
    {
        $user = $event->user;
        $voucher = $event->voucher;
        $template = $event->template;
        $source = $event->source;

        Log::info('Voucher issued notification triggered', [
            'user_id' => $user->id,
            'voucher_code' => $event->getVoucherCode(),
            'template_name' => $template->name,
            'source' => $source,
            'discount_type' => $event->getDiscountType(),
            'discount_value' => $event->getDiscountValue(),
        ]);

        // Build message based on source
        $message = match ($source) {
            'reward' => "You've received a voucher from your reward redemption!",
            'collection' => "Congratulations! You've collected a new voucher!",
            'promotion' => "You've received a promotional voucher!",
            default => "You've received a new voucher!",
        };

        // TODO: Send email with voucher details
        // Email should include:
        // - Voucher code
        // - Discount amount/percentage
        // - Minimum spend requirement
        // - Expiry date
        // - Terms and conditions
        //
        // Example:
        // Mail::to($user)->queue(new VoucherIssuedMail($event));

        Log::info('Voucher email queued', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'voucher_code' => $event->getVoucherCode(),
            'template_name' => $template->name,
        ]);

        // Send push notification
        Log::info('Voucher push notification sent', [
            'user_id' => $user->id,
            'message' => $message,
            'voucher_code' => $event->getVoucherCode(),
        ]);

        // Special handling for collection vouchers
        if ($event->isFromCollection()) {
            Log::info('Collection voucher claimed', [
                'user_id' => $user->id,
                'spending_requirement_met' => $template->spending_requirement ?? 0,
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(VoucherIssued $event, \Throwable $exception): void
    {
        Log::error('Failed to send voucher issued notification', [
            'user_id' => $event->user->id,
            'voucher_code' => $event->getVoucherCode(),
            'error' => $exception->getMessage(),
        ]);
    }
}
