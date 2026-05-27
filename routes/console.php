<?php

use App\Models\Attendance;
use App\Models\ProductivityMetric;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkLog;
use App\Services\ProductivityScoringService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('monitoring:aggregate-daily-metrics', function (ProductivityScoringService $scoring): void {
    User::query()->each(function (User $user) use ($scoring): void {
        $attendanceMinutes = (int) Attendance::where('user_id', $user->id)
            ->whereDate('date', today())
            ->sum('worked_minutes');

        $productiveMinutes = (int) WorkLog::where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->sum('duration_minutes');

        $idleMinutes = (int) round(
            WorkLog::where('user_id', $user->id)->whereDate('work_date', today())->count() > 0
                ? 30
                : 0
        );

        $tasksCompleted = Task::where('assigned_to', $user->id)
            ->where('status', 'done')
            ->whereDate('updated_at', today())
            ->count();

        $score = $scoring->calculate([
            'focused_ratio' => min(1, $productiveMinutes / 480),
            'attendance_ratio' => min(1, $attendanceMinutes / 480),
            'task_completion_ratio' => min(1, $tasksCompleted / 5),
            'activity_ratio' => max(0, min(1, 1 - ($idleMinutes / 120))),
        ]);

        ProductivityMetric::updateOrCreate(
            ['user_id' => $user->id, 'metric_date' => today()],
            [
                'focused_minutes' => $productiveMinutes,
                'productive_minutes' => $productiveMinutes,
                'idle_minutes' => $idleMinutes,
                'attendance_minutes' => $attendanceMinutes,
                'tasks_completed' => $tasksCompleted,
                'score' => $score,
            ]
        );
    });
});

Schedule::command('monitoring:aggregate-daily-metrics')->dailyAt('23:55');
