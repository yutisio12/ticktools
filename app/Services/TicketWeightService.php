<?php

namespace App\Services;

use App\Enums\Impact;
use App\Enums\Priority;
use App\Models\Category;

class TicketWeightService
{
    /**
     * Calculate ticket weight based on:
     * Ticket Weight = Base Category Score + Priority Score + Impact Score
     */
    public function calculate(Category $category, Priority $priority, Impact $impact): float
    {
        $baseScore = $category->base_score;
        $priorityScore = $priority->score();
        $impactScore = $impact->score();

        return $baseScore + $priorityScore + $impactScore;
    }
}
