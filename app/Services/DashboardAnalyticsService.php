<?php

namespace App\Services;

use App\Enums\RoleEnum;
use App\Models\ActivityLog;
use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Models\ProductivityMetric;
use App\Models\Project;
use App\Models\ScreenshotCapture;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class DashboardAnalyticsService
{
    public function overview(User $user): array
    {
        return [
            'summary' => $this->summary($user),
            'charts' => $this->charts($user),
            'recentTasks' => $this->recentTasks($user),
            'projectPortfolio' => $this->projectPortfolio($user),
            'attendanceSnapshots' => $this->attendanceSnapshots($user),
            'leaveQueue' => $this->leaveQueue($user),
            'performanceBoard' => $this->performanceBoard($user),
            'monitoringFeed' => $this->monitoringFeed($user),
            'screenshotReview' => $this->screenshotReview($user),
        ];
    }

    public function summary(User $user): array
    {
        $taskQuery = $this->taskScope($user);
        $logQuery = $this->workLogScope($user);
        $attendanceQuery = $this->attendanceScope($user);
        $metricQuery = $this->metricScope($user);
        $projectQuery = $this->projectScope($user);
        $leaveQuery = $this->leaveScope($user);

        return [
            'employees' => $user->role === RoleEnum::EMPLOYEE
                ? 1
                : User::where('is_active', true)->count(),
            'active_projects' => (clone $projectQuery)->where('status', 'active')->count(),
            'open_tasks' => (clone $taskQuery)->whereIn('status', ['todo', 'in_progress', 'review', 'blocked'])->count(),
            'completed_tasks_today' => (clone $taskQuery)->where('status', 'done')->whereDate('updated_at', today())->count(),
            'hours_logged_today' => round(((clone $logQuery)->whereDate('work_date', today())->sum('duration_minutes') / 60), 2),
            'avg_productivity_score' => round((float) ((clone $metricQuery)->where('metric_date', '>=', now()->subDays(7)->toDateString())->avg('score') ?? 0), 1),
            'attendance_rate' => round((float) ((clone $attendanceQuery)->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->count() > 0
                ? ((clone $attendanceQuery)->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->where('status', '!=', 'absent')->count() / (clone $attendanceQuery)->whereBetween('date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->count()) * 100
                : 0), 1),
            'pending_leave_requests' => (clone $leaveQuery)->where('status', 'pending')->count(),
        ];
    }

    public function charts(User $user): array
    {
        $metrics = $this->metricScope($user)
            ->where('metric_date', '>=', now()->subDays(7)->toDateString())
            ->orderBy('metric_date')
            ->get();

        $taskQuery = $this->taskScope($user);

        return [
            'productivityTrend' => [
                'labels' => $metrics->pluck('metric_date')->map(fn ($date) => $date->format('M d'))->values(),
                'datasets' => [
                    [
                        'label' => 'Productivity Score',
                        'data' => $metrics->pluck('score')->values(),
                    ],
                    [
                        'label' => 'Focused Minutes',
                        'data' => $metrics->pluck('focused_minutes')->values(),
                    ],
                ],
            ],
            'taskStatusBreakdown' => [
                'labels' => ['Todo', 'In Progress', 'Review', 'Blocked', 'Done'],
                'datasets' => [[
                    'data' => [
                        (clone $taskQuery)->where('status', 'todo')->count(),
                        (clone $taskQuery)->where('status', 'in_progress')->count(),
                        (clone $taskQuery)->where('status', 'review')->count(),
                        (clone $taskQuery)->where('status', 'blocked')->count(),
                        (clone $taskQuery)->where('status', 'done')->count(),
                    ],
                ]],
            ],
        ];
    }

    private function recentTasks(User $user): array
    {
        return $this->taskScope($user)
            ->with(['project:id,name,code', 'assignee:id,name'])
            ->latest('updated_at')
            ->limit(6)
            ->get()
            ->map(fn (Task $task) => [
                'id' => $task->id,
                'title' => $task->title,
                'status' => $task->status->value,
                'priority' => $task->priority,
                'project' => $task->project?->name,
                'project_code' => $task->project?->code,
                'assignee' => $task->assignee?->name,
                'completion_percentage' => $task->completion_percentage,
                'due_at' => $task->due_at?->format('M d, Y H:i'),
                'url' => route('tasks.show', $task),
            ])
            ->all();
    }

    private function projectPortfolio(User $user): array
    {
        return $this->projectScope($user)
            ->with(['owner:id,name'])
            ->withCount([
                'tasks',
                'tasks as completed_tasks_count' => fn (Builder $query) => $query->where('status', 'done'),
            ])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Project $project) => [
                'id' => $project->id,
                'name' => $project->name,
                'code' => $project->code,
                'status' => $project->status,
                'client_name' => $project->client_name,
                'owner' => $project->owner?->name,
                'tasks_count' => $project->tasks_count,
                'completed_tasks_count' => $project->completed_tasks_count,
                'budget' => $project->budget !== null ? number_format((float) $project->budget, 2) : null,
                'url' => route('projects.show', $project),
            ])
            ->all();
    }

    private function attendanceSnapshots(User $user): array
    {
        return $this->attendanceScope($user)
            ->with('user:id,name')
            ->whereDate('date', today())
            ->latest('clock_in_at')
            ->limit($user->role === RoleEnum::EMPLOYEE ? 1 : 6)
            ->get()
            ->map(fn (Attendance $attendance) => [
                'employee' => $attendance->user?->name,
                'status' => $attendance->status->value,
                'clock_in_at' => $attendance->clock_in_at?->format('H:i'),
                'clock_out_at' => $attendance->clock_out_at?->format('H:i'),
                'worked_minutes' => $attendance->worked_minutes,
                'notes' => $attendance->notes,
            ])
            ->all();
    }

    private function leaveQueue(User $user): array
    {
        return $this->leaveScope($user)
            ->with(['user:id,name', 'approver:id,name'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (LeaveRequest $leave) => [
                'employee' => $leave->user?->name,
                'type' => $leave->leave_type,
                'status' => $leave->status->value,
                'dates' => $leave->start_date->format('M d').' - '.$leave->end_date->format('M d'),
                'approver' => $leave->approver?->name,
            ])
            ->all();
    }

    private function performanceBoard(User $user): array
    {
        if ($user->role === RoleEnum::EMPLOYEE) {
            return $this->metricScope($user)
                ->where('metric_date', '>=', now()->subDays(7)->toDateString())
                ->orderByDesc('metric_date')
                ->limit(5)
                ->get()
                ->map(fn (ProductivityMetric $metric) => [
                    'employee' => $user->name,
                    'score' => (float) $metric->score,
                    'focused_minutes' => $metric->focused_minutes,
                    'productive_minutes' => $metric->productive_minutes,
                    'day' => $metric->metric_date->format('M d'),
                ])
                ->all();
        }

        return ProductivityMetric::query()
            ->selectRaw('user_id, AVG(score) as avg_score, SUM(focused_minutes) as focused_minutes, SUM(productive_minutes) as productive_minutes')
            ->with('user:id,name')
            ->where('metric_date', '>=', now()->subDays(7)->toDateString())
            ->groupBy('user_id')
            ->orderByDesc('avg_score')
            ->limit(5)
            ->get()
            ->map(fn (ProductivityMetric $metric) => [
                'employee' => $metric->user?->name,
                'score' => round((float) $metric->avg_score, 1),
                'focused_minutes' => (int) $metric->focused_minutes,
                'productive_minutes' => (int) $metric->productive_minutes,
                'day' => 'Last 7 days',
            ])
            ->all();
    }

    private function monitoringFeed(User $user): array
    {
        return $this->activityScope($user)
            ->with('user:id,name')
            ->latest('logged_at')
            ->limit(6)
            ->get()
            ->map(fn (ActivityLog $activity) => [
                'employee' => $activity->user?->name,
                'score' => $activity->activity_score,
                'keystrokes' => $activity->keystrokes,
                'mouse_clicks' => $activity->mouse_clicks,
                'idle_seconds' => $activity->idle_seconds,
                'window' => $activity->active_window_title,
                'logged_at' => $activity->logged_at->format('M d, H:i'),
            ])
            ->all();
    }

    private function screenshotReview(User $user): array
    {
        return $this->screenshotScope($user)
            ->with('user:id,name')
            ->latest('captured_at')
            ->limit(4)
            ->get()
            ->map(fn (ScreenshotCapture $capture) => [
                'employee' => $capture->user?->name,
                'captured_at' => $capture->captured_at->format('M d, H:i'),
                'label' => $capture->productivity_label,
                'blur_sensitive' => $capture->blur_sensitive,
                'image_url' => Storage::disk(config('monitoring.screenshots.disk'))->url($capture->image_path),
            ])
            ->all();
    }

    private function taskScope(User $user): Builder
    {
        return Task::query()
            ->when($user->role === RoleEnum::EMPLOYEE, fn (Builder $query) => $query->where('assigned_to', $user->id));
    }

    private function workLogScope(User $user): Builder
    {
        return WorkLog::query()
            ->when($user->role === RoleEnum::EMPLOYEE, fn (Builder $query) => $query->where('user_id', $user->id));
    }

    private function attendanceScope(User $user): Builder
    {
        return Attendance::query()
            ->when($user->role === RoleEnum::EMPLOYEE, fn (Builder $query) => $query->where('user_id', $user->id));
    }

    private function metricScope(User $user): Builder
    {
        return ProductivityMetric::query()
            ->when($user->role === RoleEnum::EMPLOYEE, fn (Builder $query) => $query->where('user_id', $user->id));
    }

    private function leaveScope(User $user): Builder
    {
        return LeaveRequest::query()
            ->when($user->role === RoleEnum::EMPLOYEE, fn (Builder $query) => $query->where('user_id', $user->id));
    }

    private function projectScope(User $user): Builder
    {
        return Project::query()
            ->when(
                $user->role === RoleEnum::EMPLOYEE,
                fn (Builder $query) => $query->whereHas('tasks', fn (Builder $taskQuery) => $taskQuery->where('assigned_to', $user->id))
            );
    }

    private function activityScope(User $user): Builder
    {
        return ActivityLog::query()
            ->when($user->role === RoleEnum::EMPLOYEE, fn (Builder $query) => $query->where('user_id', $user->id));
    }

    private function screenshotScope(User $user): Builder
    {
        return ScreenshotCapture::query()
            ->when($user->role === RoleEnum::EMPLOYEE, fn (Builder $query) => $query->where('user_id', $user->id));
    }
}
