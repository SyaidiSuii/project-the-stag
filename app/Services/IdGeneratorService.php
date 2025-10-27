<?php

namespace App\Services;

use App\Models\User;
use App\Models\StaffProfile;
use App\Models\CustomerProfile;
use Illuminate\Support\Facades\DB;

class IdGeneratorService
{
    /**
     * Position code mapping for staff IDs
     */
    const POSITION_CODES = [
        'waiter' => 'WTR',
        'waitress' => 'WTR',
        'chef' => 'CHF',
        'sous chef' => 'CHF',
        'head chef' => 'CHF',
        'cashier' => 'CSH',
        'manager' => 'MGR',
        'assistant manager' => 'MGR',
        'supervisor' => 'SPV',
        'kitchen staff' => 'KTC',
        'bartender' => 'BAR',
        'hostess' => 'HST',
        'host' => 'HST',
        'delivery' => 'DLV',
        'cleaner' => 'CLN',
    ];

    /**
     * Generate User ID: USR-25-0001
     * Format: USR-[YEAR]-[AUTO_ID_PADDED]
     *
     * @return string
     */
    public function generateUserId(): string
    {
        $year = date('y'); // 2-digit year
        $maxRetries = 10;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            // Get the last user_id for current year
            $lastUser = User::where('user_id', 'like', "USR-{$year}-%")
                ->orderByRaw('CAST(SUBSTRING(user_id, -4) AS UNSIGNED) DESC')
                ->first();

            // Extract sequence number
            if ($lastUser && $lastUser->user_id) {
                $lastSequence = (int) substr($lastUser->user_id, -4);
                $newSequence = $lastSequence + 1;
            } else {
                $newSequence = 1;
            }

            // Pad to 4 digits
            $paddedSequence = str_pad($newSequence, 4, '0', STR_PAD_LEFT);
            $userId = "USR-{$year}-{$paddedSequence}";

            // Check uniqueness
            if (!User::where('user_id', $userId)->exists()) {
                return $userId;
            }
        }

        // Fallback: use timestamp if collision persists
        return "USR-{$year}-" . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Staff ID: STG-WTR-25-45-01
     * Format: STG-[POSITION_CODE]-[YEAR]-[IC_LAST_2]-[AUTO_ID_PADDED]
     *
     * @param string $position Staff position
     * @param string|null $icNumber IC number (e.g., 010203040045)
     * @return string
     */
    public function generateStaffId(string $position, ?string $icNumber = null): string
    {
        $year = date('y'); // 2-digit year
        $positionCode = $this->getPositionCode($position);
        $icLast2 = $icNumber ? substr($icNumber, -2) : '00';
        $maxRetries = 10;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            // Get the last staff_id for current year and position
            $lastStaff = StaffProfile::where('staff_id', 'like', "STG-{$positionCode}-{$year}-{$icLast2}-%")
                ->orderByRaw('CAST(SUBSTRING(staff_id, -2) AS UNSIGNED) DESC')
                ->first();

            // Extract sequence number
            if ($lastStaff && $lastStaff->staff_id) {
                $lastSequence = (int) substr($lastStaff->staff_id, -2);
                $newSequence = $lastSequence + 1;
            } else {
                $newSequence = 1;
            }

            // Pad to 2 digits
            $paddedSequence = str_pad($newSequence, 2, '0', STR_PAD_LEFT);
            $staffId = "STG-{$positionCode}-{$year}-{$icLast2}-{$paddedSequence}";

            // Check uniqueness
            if (!StaffProfile::where('staff_id', $staffId)->exists()) {
                return $staffId;
            }
        }

        // Fallback: use random number if collision persists
        $randomSeq = str_pad(rand(0, 99), 2, '0', STR_PAD_LEFT);
        return "STG-{$positionCode}-{$year}-{$icLast2}-{$randomSeq}";
    }

    /**
     * Generate Customer ID: CST-25-4821
     * Format: CST-[YEAR]-[4DIGIT_RANDOM]
     *
     * @return string
     */
    public function generateCustomerId(): string
    {
        $year = date('y'); // 2-digit year
        $maxRetries = 50;

        for ($attempt = 0; $attempt < $maxRetries; $attempt++) {
            // Generate 4-digit random number
            $randomNumber = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $customerId = "CST-{$year}-{$randomNumber}";

            // Check uniqueness
            if (!CustomerProfile::where('customer_id', $customerId)->exists()) {
                return $customerId;
            }
        }

        // Fallback: append timestamp milliseconds
        $timestamp = substr(microtime(true) * 1000, -4);
        return "CST-{$year}-{$timestamp}";
    }

    /**
     * Get position code from position name
     *
     * @param string $position
     * @return string
     */
    public function getPositionCode(string $position): string
    {
        $normalizedPosition = strtolower(trim($position));

        // Check exact match first
        if (isset(self::POSITION_CODES[$normalizedPosition])) {
            return self::POSITION_CODES[$normalizedPosition];
        }

        // Check partial match
        foreach (self::POSITION_CODES as $key => $code) {
            if (str_contains($normalizedPosition, $key)) {
                return $code;
            }
        }

        // Default code for unknown positions
        return 'STF'; // Generic staff
    }

    /**
     * Validate user_id format
     *
     * @param string $userId
     * @return bool
     */
    public function validateUserId(string $userId): bool
    {
        return preg_match('/^USR-\d{2}-\d{4}$/', $userId) === 1;
    }

    /**
     * Validate staff_id format
     *
     * @param string $staffId
     * @return bool
     */
    public function validateStaffId(string $staffId): bool
    {
        return preg_match('/^STG-[A-Z]{3}-\d{2}-\d{2}-\d{2}$/', $staffId) === 1;
    }

    /**
     * Validate customer_id format
     *
     * @param string $customerId
     * @return bool
     */
    public function validateCustomerId(string $customerId): bool
    {
        return preg_match('/^CST-\d{2}-\d{4}$/', $customerId) === 1;
    }
}
