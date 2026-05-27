<?php

namespace App\Http\Controllers\Api;

use App\Events\TaskUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TaskApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tasks = Task::with(['project', 'assignee'])
            ->when($request->user()->role->value === 'employee', fn ($query) => $query->where('assigned_to', $request->user()->id))
            ->latest()
            ->get();

        return response()->json($tasks);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated() + ['assigned_by' => $request->user()->id]);
        broadcast(new TaskUpdated($task))->toOthers();

        return response()->json($task->load(['project', 'assignee']), 201);
    }

    public function update(StoreTaskRequest $request, Task $task): JsonResponse
    {
        $task->update($request->validated());
        broadcast(new TaskUpdated($task->fresh()))->toOthers();

        return response()->json($task->load(['project', 'assignee']));
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->noContent();
    }
}
