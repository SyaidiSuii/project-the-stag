<?php

namespace App\Events\Loyalty;

use App\Models\User;
use App\Models\CustomerVoucher;
use App\Models\VoucherTemplate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * PHASE 5: Voucher Issued Event
 *
 * Dispatched when a voucher is issued to a customer.
 * Sources: reward redemption, collection, promotion, manual
 *
 * Triggers:
 * - Email with voucher code
 * - Push notification
 * - Analytics tracking
 */
class VoucherIssued
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public CustomerVoucher $voucher;
    public VoucherTemplate $template;
    public string $source;

    /**
     * Create a new event instance.
     */
    public function __construct(
        User $user,
        CustomerVoucher $voucher,
        VoucherTemplate $template,
        string $source = 'manual'
    ) {
        $this->user = $user;
        $this->voucher = $voucher;
        $this->template = $template;
        $this->source = $source;
    }

    /**
     * Check if voucher was from reward redemption
     */
    public function isFromReward(): bool
    {
        return $this->source === 'reward';
    }

    /**
     * Check if voucher was collected by customer
     */
    public function isFromCollection(): bool
    {
        return $this->source === 'collection';
    }

    /**
     * Check if voucher was from promotion
     */
    public function isFromPromotion(): bool
    {
        return $this->source === 'promotion';
    }

    /**
     * Get voucher code
     */
    public function getVoucherCode(): string
    {
        return $this->voucher->voucher_code ?? '';
    }

    /**
     * Get discount amount/percentage
     */
    public function getDiscountValue(): float
    {
        return $this->template->discount_value;
    }

    /**
     * Get discount type
     */
    public function getDiscountType(): string
    {
        return $this->template->discount_type; // 'fixed' or 'percentage'
    }
}
