<?php

namespace App\Services;

use App\Models\SaleAnalytics;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Report Audit Service
 * Tracks calculation changes, discrepancies, and data corrections
 */
class ReportAuditService
{
    /**
     * Log analytics calculation
     *
     * @param Carbon $date
     * @param array $calculatedData
     * @param string $source
     * @return void
     */
    public function logCalculation(Carbon $date, array $calculatedData, string $source = 'manual'): void
    {
        Log::channel('analytics')->info('Analytics calculated', [
            'date' => $date->toDateString(),
            'source' => $source,
            'total_sales' => $calculatedData['total_sales'] ?? 0,
            'total_orders' => $calculatedData['total_orders'] ?? 0,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Log analytics update/correction
     *
     * @param Carbon $date
     * @param array $oldData
     * @param array $newData
     * @param string $reason
     * @return void
     */
    public function logUpdate(Carbon $date, array $oldData, array $newData, string $reason = 'correction'): void
    {
        $changes = $this->detectChanges($oldData, $newData);

        if (empty($changes)) {
            return;
        }

        Log::channel('analytics')->warning('Analytics data updated', [
            'date' => $date->toDateString(),
            'reason' => $reason,
            'changes' => $changes,
            'timestamp' => now()->toDateTimeString(),
        ]);

        // Also log to database for audit trail
        DB::table('analytics_audit_log')->insert([
            'date' => $date->toDateString(),
            'action' => 'update',
            'reason' => $reason,
            'old_values' => json_encode($oldData),
            'new_values' => json_encode($newData),
            'changes' => json_encode($changes),
            'created_at' => now(),
        ]);
    }

    /**
     * Log discrepancy detection
     *
     * @param Carbon $date
     * @param array $discrepancies
     * @return void
     */
    public function logDiscrepancy(Carbon $date, array $discrepancies): void
    {
        if (empty($discrepancies)) {
            return;
        }

        $severity = $this->calculateOverallSeverity($discrepancies);

        Log::error('Analytics discrepancy detected', [
            'date' => $date->toDateString(),
            'severity' => $severity,
            'discrepancies_count' => count($discrepancies),
            'details' => $discrepancies,
            'timestamp' => now()->toDateTimeString(),
        ]);

        // Log to database
        DB::table('analytics_audit_log')->insert([
            'date' => $date->toDateString(),
            'action' => 'discrepancy_detected',
            'reason' => 'Data validation failed',
            'severity' => $severity,
            'changes' => json_encode($discrepancies),
            'created_at' => now(),
        ]);
    }

    /**
     * Log auto-fix action
     *
     * @param Carbon $date
     * @param array $discrepancies
     * @return void
     */
    public function logAutoFix(Carbon $date, array $discrepancies): void
    {
        Log::info('Analytics auto-fixed', [
            'date' => $date->toDateString(),
            'fixed_fields' => count($discrepancies),
            'details' => $discrepancies,
            'timestamp' => now()->toDateTimeString(),
        ]);

        DB::table('analytics_audit_log')->insert([
            'date' => $date->toDateString(),
            'action' => 'auto_fix',
            'reason' => 'Automated correction',
            'changes' => json_encode($discrepancies),
            'created_at' => now(),
        ]);
    }

    /**
     * Get audit trail for a specific date
     *
     * @param Carbon $date
     * @return array
     */
    public function getAuditTrail(Carbon $date): array
    {
        $logs = DB::table('analytics_audit_log')
            ->where('date', $date->toDateString())
            ->orderBy('created_at', 'desc')
            ->get();

        return $logs->map(function ($log) {
            return [
                'action' => $log->action,
                'reason' => $log->reason,
                'severity' => $log->severity ?? null,
                'changes' => json_decode($log->changes, true),
                'timestamp' => $log->created_at,
            ];
        })->toArray();
    }

    /**
     * Get audit summary for date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getAuditSummary(Carbon $startDate, Carbon $endDate): array
    {
        $logs = DB::table('analytics_audit_log')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->get();

        return [
            'total_events' => $logs->count(),
            'calculations' => $logs->where('action', 'calculate')->count(),
            'updates' => $logs->where('action', 'update')->count(),
            'discrepancies' => $logs->where('action', 'discrepancy_detected')->count(),
            'auto_fixes' => $logs->where('action', 'auto_fix')->count(),
            'critical_issues' => $logs->where('severity', 'critical')->count(),
            'recent_activity' => $logs->sortByDesc('created_at')->take(10)->values()->toArray(),
        ];
    }

    /**
     * Detect changes between old and new data
     *
     * @param array $oldData
     * @param array $newData
     * @return array
     */
    private function detectChanges(array $oldData, array $newData): array
    {
        $changes = [];

        foreach ($newData as $key => $newValue) {
            $oldValue = $oldData[$key] ?? null;

            // Skip if values are the same
            if ($oldValue == $newValue) {
                continue;
            }

            // Skip metadata fields
            if (in_array($key, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            $changes[] = [
                'field' => $key,
                'old_value' => $oldValue,
                'new_value' => $newValue,
                'difference' => is_numeric($oldValue) && is_numeric($newValue)
                    ? $newValue - $oldValue
                    : null,
            ];
        }

        return $changes;
    }

    /**
     * Calculate overall severity from discrepancies
     *
     * @param array $discrepancies
     * @return string
     */
    private function calculateOverallSeverity(array $discrepancies): string
    {
        $severities = collect($discrepancies)->pluck('severity');

        if ($severities->contains('critical')) {
            return 'critical';
        } elseif ($severities->contains('high')) {
            return 'high';
        } elseif ($severities->contains('medium')) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Get data quality score for a date range
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    public function getDataQualityScore(Carbon $startDate, Carbon $endDate): array
    {
        $totalDays = $startDate->diffInDays($endDate) + 1;

        $discrepancyLogs = DB::table('analytics_audit_log')
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->where('action', 'discrepancy_detected')
            ->get();

        $daysWithIssues = $discrepancyLogs->unique('date')->count();
        $daysAccurate = $totalDays - $daysWithIssues;

        $qualityScore = $totalDays > 0 ? ($daysAccurate / $totalDays) * 100 : 100;

        return [
            'quality_score' => round($qualityScore, 2),
            'total_days_analyzed' => $totalDays,
            'days_accurate' => $daysAccurate,
            'days_with_issues' => $daysWithIssues,
            'critical_issues' => $discrepancyLogs->where('severity', 'critical')->count(),
            'grade' => $this->getQualityGrade($qualityScore),
        ];
    }

    /**
     * Get quality grade based on score
     *
     * @param float $score
     * @return string
     */
    private function getQualityGrade(float $score): string
    {
        if ($score >= 99) {
            return 'A+';
        } elseif ($score >= 95) {
            return 'A';
        } elseif ($score >= 90) {
            return 'B+';
        } elseif ($score >= 85) {
            return 'B';
        } elseif ($score >= 80) {
            return 'C+';
        } elseif ($score >= 75) {
            return 'C';
        } else {
            return 'D';
        }
    }

    /**
     * Clean old audit logs (retention policy)
     *
     * @param int $daysToKeep
     * @return int Number of deleted records
     */
    public function cleanOldLogs(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);

        $deleted = DB::table('analytics_audit_log')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        Log::info('Audit logs cleaned', [
            'records_deleted' => $deleted,
            'cutoff_date' => $cutoffDate->toDateString(),
        ]);

        return $deleted;
    }
}
