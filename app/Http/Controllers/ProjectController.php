<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Project;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $user = request()->user();
        $projects = Project::withCount('tasks')
            ->with('owner')
            ->when(
                $user->role === RoleEnum::EMPLOYEE,
                fn ($query) => $query->whereHas('tasks', fn ($taskQuery) => $taskQuery->where('assigned_to', $user->id))
            )
            ->latest()
            ->paginate();

        return view('pages.projects.index', compact('projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:projects,code'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:planning,active,on_hold,completed'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
        ]);

        $project = Project::create($payload + ['owner_id' => $request->user()->id]);
        AuditLogger::forRequest($request)->created($project);

        return back()->with('status', 'Project created.');
    }

    public function show(Project $project): View
    {
        $project->load(['owner', 'tasks.assignee']);

        return view('pages.projects.show', compact('project'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $payload = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'client_name' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:planning,active,on_hold,completed'],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
        ]);

        $original = $project->getOriginal();
        $project->update($payload);
        AuditLogger::forRequest($request)->updated($project, $original);

        return back()->with('status', 'Project updated.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return back()->with('status', 'Project archived.');
    }
}
