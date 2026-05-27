<?php

namespace App\Services;

class ProductivityScoringService
{
    public function calculate(array $metrics): float
    {
        $focused = ($metrics['focused_ratio'] ?? 0) * config('monitoring.productivity.focused_weight');
        $attendance = ($metrics['attendance_ratio'] ?? 0) * config('monitoring.productivity.attendance_weight');
        $task = ($metrics['task_completion_ratio'] ?? 0) * config('monitoring.productivity.task_weight');
        $activity = ($metrics['activity_ratio'] ?? 0) * config('monitoring.productivity.activity_weight');

        return round(($focused + $attendance + $task + $activity) * 100, 2);
    }
}
