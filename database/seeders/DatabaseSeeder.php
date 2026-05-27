<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\Attendance;
use App\Models\ProductivityMetric;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkLog;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@monitorflow.test',
            'role' => RoleEnum::ADMIN,
        ]);

        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@monitorflow.test',
            'role' => RoleEnum::MANAGER,
        ]);

        $employees = User::factory(8)->create([
            'role' => RoleEnum::EMPLOYEE,
        ]);

        $project = Project::create([
            'owner_id' => $manager->id,
            'name' => 'Productivity Suite',
            'code' => 'MF-001',
            'client_name' => 'Internal',
            'status' => 'active',
        ]);

        foreach ($employees as $employee) {
            $task = Task::create([
                'project_id' => $project->id,
                'assigned_by' => $manager->id,
                'assigned_to' => $employee->id,
                'title' => 'Daily operational review',
                'priority' => 'medium',
                'status' => 'in_progress',
                'estimated_minutes' => 90,
            ]);

            Attendance::create([
                'user_id' => $employee->id,
                'date' => today(),
                'clock_in_at' => now()->startOfDay()->addHours(9),
                'status' => 'present',
            ]);

            WorkLog::create([
                'user_id' => $employee->id,
                'task_id' => $task->id,
                'project_id' => $project->id,
                'work_date' => today(),
                'started_at' => now()->subHours(2),
                'ended_at' => now()->subHour(),
                'duration_minutes' => 60,
                'summary' => 'Completed queue triage and reporting.',
                'billable' => false,
            ]);

            ProductivityMetric::create([
                'user_id' => $employee->id,
                'metric_date' => today(),
                'focused_minutes' => 210,
                'productive_minutes' => 260,
                'idle_minutes' => 35,
                'attendance_minutes' => 420,
                'tasks_completed' => 2,
                'score' => 81,
            ]);
        }
    }
}
