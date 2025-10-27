<?php

namespace App\Services\Promotions;

use App\Models\Promotion;
use App\Models\PromotionUsageLog;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PromotionUsageLogger
{
    /**
     * Log promotion usage when applied to an order
     *
     * @param Promotion $promotion
     * @param Order $order
     * @param float $discountAmount
     * @param User|null $user
     * @return PromotionUsageLog|null
     */
    public function logUsage(
        Promotion $promotion,
        Order $order,
        float $discountAmount,
        ?User $user = null
    ): ?PromotionUsageLog {
        try {
            DB::beginTransaction();

            // Create usage log entry
            $log = PromotionUsageLog::create([
                'promotion_id' => $promotion->id,
                'user_id' => $user?->id,
                'order_id' => $order->id,
                'discount_amount' => $discountAmount,
                'order_subtotal' => $order->subtotal ?? 0,
                'order_total' => $order->total_amount,
                'promo_code' => $promotion->promo_code,
                'session_id' => session()->getId(),
                'ip_address' => request()->ip(),
                'used_at' => now(),
            ]);

            // Increment the promotion's usage counter
            $promotion->increment('current_usage_count');

            DB::commit();

            Log::info('Promotion usage logged', [
                'promotion_id' => $promotion->id,
                'order_id' => $order->id,
                'user_id' => $user?->id,
                'discount_amount' => $discountAmount,
            ]);

            return $log;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to log promotion usage', [
                'promotion_id' => $promotion->id,
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get the number of times a user has used a promotion
     *
     * @param Promotion $promotion
     * @param int $userId
     * @return int
     */
    public function getUserUsageCount(Promotion $promotion, int $userId): int
    {
        return PromotionUsageLog::where('promotion_id', $promotion->id)
            ->where('user_id', $userId)
            ->count();
    }

    /**
     * Check if a user can use a promotion (comprehensive validation)
     *
     * @param Promotion $promotion
     * @param int|null $userId
     * @param array $context Additional context (cart_items, current_time, etc.)
     * @return array ['can_use' => bool, 'reason' => string, 'details' => array]
     */
    public function canUserUsePromotion(
        Promotion $promotion,
        ?int $userId = null,
        array $context = []
    ): array {
        // Check if promotion is active
        if (!$promotion->is_active) {
            return [
                'can_use' => false,
                'reason' => 'This promotion is currently inactive.',
                'error_type' => 'inactive',
            ];
        }

        // Check date range
        $now = now();
        if ($promotion->start_date && $promotion->start_date->isFuture()) {
            return [
                'can_use' => false,
                'reason' => 'This promotion has not started yet. Available from ' . $promotion->start_date->format('M d, Y'),
                'error_type' => 'not_started',
                'start_date' => $promotion->start_date->toDateTimeString(),
            ];
        }

        if ($promotion->end_date && $promotion->end_date->isPast()) {
            return [
                'can_use' => false,
                'reason' => 'This promotion has expired on ' . $promotion->end_date->format('M d, Y'),
                'error_type' => 'expired',
                'end_date' => $promotion->end_date->toDateTimeString(),
            ];
        }

        // Check total usage limit
        if ($promotion->total_usage_limit && $promotion->current_usage_count >= $promotion->total_usage_limit) {
            return [
                'can_use' => false,
                'reason' => 'This promotion has reached its maximum usage limit.',
                'error_type' => 'total_limit_reached',
                'total_used' => $promotion->current_usage_count,
                'total_limit' => $promotion->total_usage_limit,
            ];
        }

        // Check per-user usage limit (only if user is logged in)
        if ($userId && $promotion->usage_limit_per_customer) {
            $userUsageCount = $this->getUserUsageCount($promotion, $userId);

            if ($userUsageCount >= $promotion->usage_limit_per_customer) {
                return [
                    'can_use' => false,
                    'reason' => sprintf(
                        'You have already used this promotion %d time%s (maximum allowed: %d)',
                        $userUsageCount,
                        $userUsageCount === 1 ? '' : 's',
                        $promotion->usage_limit_per_customer
                    ),
                    'error_type' => 'user_limit_reached',
                    'user_used' => $userUsageCount,
                    'user_limit' => $promotion->usage_limit_per_customer,
                ];
            }
        }

        // Check day-of-week restrictions
        if ($promotion->applicable_days && count($promotion->applicable_days) > 0) {
            $currentDay = strtolower($now->format('l')); // e.g., "monday"

            if (!in_array($currentDay, $promotion->applicable_days)) {
                $availableDays = implode(', ', array_map('ucfirst', $promotion->applicable_days));
                return [
                    'can_use' => false,
                    'reason' => sprintf(
                        'This promotion is only available on: %s. Today is %s.',
                        $availableDays,
                        ucfirst($currentDay)
                    ),
                    'error_type' => 'wrong_day',
                    'available_days' => $promotion->applicable_days,
                    'current_day' => $currentDay,
                ];
            }
        }

        // Check time-of-day restrictions
        if ($promotion->applicable_start_time && $promotion->applicable_end_time) {
            $currentTime = $context['current_time'] ?? $now->format('H:i:s');
            $startTime = $promotion->applicable_start_time;
            $endTime = $promotion->applicable_end_time;

            if ($currentTime < $startTime || $currentTime > $endTime) {
                return [
                    'can_use' => false,
                    'reason' => sprintf(
                        'This promotion is only available from %s to %s. Current time: %s',
                        date('g:i A', strtotime($startTime)),
                        date('g:i A', strtotime($endTime)),
                        date('g:i A', strtotime($currentTime))
                    ),
                    'error_type' => 'outside_time_range',
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'current_time' => $currentTime,
                ];
            }
        }

        // All checks passed
        $remainingUses = null;
        if ($promotion->total_usage_limit) {
            $remainingUses = $promotion->total_usage_limit - $promotion->current_usage_count;
        }

        $userRemainingUses = null;
        if ($userId && $promotion->usage_limit_per_customer) {
            $userUsageCount = $this->getUserUsageCount($promotion, $userId);
            $userRemainingUses = $promotion->usage_limit_per_customer - $userUsageCount;
        }

        return [
            'can_use' => true,
            'reason' => 'Promotion is valid and can be used.',
            'remaining_uses' => $remainingUses,
            'user_remaining_uses' => $userRemainingUses,
        ];
    }

    /**
     * Get remaining uses for display purposes
     *
     * @param Promotion $promotion
     * @param int|null $userId
     * @return array
     */
    public function getRemainingUses(Promotion $promotion, ?int $userId = null): array
    {
        $result = [
            'total_remaining' => null,
            'user_remaining' => null,
            'is_limited' => false,
            'is_almost_depleted' => false, // >80% used
        ];

        if ($promotion->total_usage_limit) {
            $result['is_limited'] = true;
            $result['total_remaining'] = max(0, $promotion->total_usage_limit - $promotion->current_usage_count);

            $percentageUsed = ($promotion->current_usage_count / $promotion->total_usage_limit) * 100;
            $result['is_almost_depleted'] = $percentageUsed >= 80;
        }

        if ($userId && $promotion->usage_limit_per_customer) {
            $userUsageCount = $this->getUserUsageCount($promotion, $userId);
            $result['user_remaining'] = max(0, $promotion->usage_limit_per_customer - $userUsageCount);
        }

        return $result;
    }

    /**
     * Get usage statistics for a promotion
     *
     * @param Promotion $promotion
     * @return array
     */
    public function getUsageStatistics(Promotion $promotion): array
    {
        $totalUses = $promotion->current_usage_count;
        $uniqueUsers = PromotionUsageLog::where('promotion_id', $promotion->id)
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count('user_id');

        $totalDiscount = PromotionUsageLog::where('promotion_id', $promotion->id)
            ->sum('discount_amount');

        $totalRevenue = PromotionUsageLog::where('promotion_id', $promotion->id)
            ->sum('order_total');

        return [
            'total_uses' => $totalUses,
            'unique_users' => $uniqueUsers,
            'total_discount_given' => $totalDiscount,
            'total_revenue_generated' => $totalRevenue,
            'average_discount_per_use' => $totalUses > 0 ? $totalDiscount / $totalUses : 0,
        ];
    }
}
