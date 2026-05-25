<?php

namespace App\Services;

use App\Enums\Priority;
use App\Models\SlaConfig;
use Carbon\Carbon;

class SlaService
{
    /**
     * Calculate SLA deadline based on category and priority
     */
    public function calculateDeadline(int $categoryId, Priority $priority): Carbon
    {
        $hours = SlaConfig::getResolutionHours($categoryId, $priority->value);

        if (!$hours) {
            // Fallback to default SLA hours from priority enum
            $hours = $priority->slaHoursDefault();
        }

        return now()->addHours($hours);
    }

    /**
     * Check if a ticket has breached its SLA
     */
    public function isBreached(?Carbon $slaDeadline): bool
    {
        if (!$slaDeadline) {
            return false;
        }

        return now()->greaterThan($slaDeadline);
    }

    /**
     * Get remaining time until SLA breach
     */
    public function getRemainingTime(?Carbon $slaDeadline): ?string
    {
        if (!$slaDeadline) {
            return null;
        }

        if ($this->isBreached($slaDeadline)) {
            $diff = $slaDeadline->diff(now());
            return '-' . $this->formatDiff($diff);
        }

        $diff = now()->diff($slaDeadline);
        return $this->formatDiff($diff);
    }

    /**
     * Get SLA percentage (time elapsed / total time)
     */
    public function getSlaPercentage(Carbon $createdAt, ?Carbon $slaDeadline): float
    {
        if (!$slaDeadline) {
            return 0;
        }

        $totalSeconds = $createdAt->diffInSeconds($slaDeadline);
        $elapsedSeconds = $createdAt->diffInSeconds(now());

        if ($totalSeconds <= 0) {
            return 100;
        }

        return min(100, round(($elapsedSeconds / $totalSeconds) * 100, 1));
    }

    protected function formatDiff(\DateInterval $diff): string
    {
        if ($diff->days > 0) {
            return $diff->days . 'd ' . $diff->h . 'h';
        }
        if ($diff->h > 0) {
            return $diff->h . 'h ' . $diff->i . 'm';
        }
        return $diff->i . 'm';
    }
}
