<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('client_name')->nullable();
            $table->string('status')->default('planning');
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assigned_by')->constrained('users');
            $table->foreignId('assigned_to')->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium');
            $table->string('status')->default('todo')->index();
            $table->timestamp('due_at')->nullable();
            $table->unsignedInteger('estimated_minutes')->default(0);
            $table->unsignedInteger('actual_minutes')->default(0);
            $table->unsignedTinyInteger('completion_percentage')->default(0);
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date')->index();
            $table->timestamp('clock_in_at')->nullable();
            $table->timestamp('clock_out_at')->nullable();
            $table->unsignedInteger('worked_minutes')->default(0);
            $table->string('status')->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'date']);
        });

        Schema::create('work_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('task_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();
            $table->date('work_date')->index();
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->unsignedInteger('duration_minutes');
            $table->text('summary');
            $table->boolean('billable')->default(false);
            $table->unsignedInteger('screenshots_count')->default(0);
            $table->timestamps();
        });

        Schema::create('leave_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('leave_type');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->string('status')->default('pending')->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('logged_at')->index();
            $table->unsignedInteger('keystrokes')->default(0);
            $table->unsignedInteger('mouse_clicks')->default(0);
            $table->string('active_window_title')->nullable();
            $table->string('active_window_url', 2048)->nullable();
            $table->unsignedInteger('idle_seconds')->default(0);
            $table->unsignedTinyInteger('activity_score')->default(0);
        });

        Schema::create('screenshot_captures', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('captured_at')->index();
            $table->string('image_path');
            $table->boolean('blur_sensitive')->default(false);
            $table->string('productivity_label')->default('unclassified');
            $table->json('metadata')->nullable();
        });

        Schema::create('productivity_metrics', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('metric_date')->index();
            $table->unsignedInteger('focused_minutes')->default(0);
            $table->unsignedInteger('productive_minutes')->default(0);
            $table->unsignedInteger('idle_minutes')->default(0);
            $table->unsignedInteger('attendance_minutes')->default(0);
            $table->unsignedInteger('tasks_completed')->default(0);
            $table->decimal('score', 5, 2)->default(0);
            $table->timestamps();
            $table->unique(['user_id', 'metric_date']);
        });

        Schema::create('audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('action');
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('productivity_metrics');
        Schema::dropIfExists('screenshot_captures');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('work_logs');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('projects');
    }
};
