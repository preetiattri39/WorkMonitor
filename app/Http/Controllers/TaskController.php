<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Events\TaskUpdated;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $tasks = Task::with(['project', 'assignee', 'assigner'])
            ->when($user->role === RoleEnum::EMPLOYEE, fn ($query) => $query->where('assigned_to', $user->id))
            ->latest()
            ->paginate();

        return view('pages.tasks.index', [
            'tasks' => $tasks,
            'projects' => Project::orderBy('name')->get(),
            'employees' => User::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $task = Task::create($request->validated() + [
            'assigned_by' => $request->user()->id,
            'status' => $request->validated()['status'] ?? 'todo',
        ]);

        $assignee = User::findOrFail($task->assigned_to);
        $assignee->notify(new TaskAssignedNotification($task));
        broadcast(new TaskUpdated($task))->toOthers();
        AuditLogger::forRequest($request)->created($task);

        return back()->with('status', 'Task created.');
    }

    public function show(Task $task): View
    {
        $task->load(['project', 'assignee', 'workLogs.user']);

        return view('pages.tasks.show', [
            'task' => $task,
            'projects' => Project::orderBy('name')->get(),
            'employees' => User::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(StoreTaskRequest $request, Task $task): RedirectResponse
    {
        $original = $task->getOriginal();
        $task->update($request->validated());
        broadcast(new TaskUpdated($task->fresh()))->toOthers();
        AuditLogger::forRequest($request)->updated($task, $original);

        return back()->with('status', 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return back()->with('status', 'Task deleted.');
    }
}
