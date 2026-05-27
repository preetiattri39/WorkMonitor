<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Http\Requests\StoreWorkLogRequest;
use App\Models\Project;
use App\Models\Task;
use App\Models\WorkLog;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkLogController extends Controller
{
    public function index(Request $request): View
    {
        $workLogs = WorkLog::with(['task', 'project', 'user'])
            ->when($request->user()->role === RoleEnum::EMPLOYEE, fn ($query) => $query->where('user_id', $request->user()->id))
            ->latest('work_date')
            ->paginate();

        return view('pages.work-logs.index', [
            'workLogs' => $workLogs,
            'tasks' => Task::with('project')->orderBy('title')->get(),
            'projects' => Project::orderBy('name')->get(),
        ]);
    }

    public function store(StoreWorkLogRequest $request): RedirectResponse
    {
        $log = WorkLog::create($request->validated() + ['user_id' => $request->user()->id]);
        AuditLogger::forRequest($request)->created($log);

        return back()->with('status', 'Work log saved.');
    }

    public function update(StoreWorkLogRequest $request, WorkLog $workLog): RedirectResponse
    {
        $original = $workLog->getOriginal();
        $workLog->update($request->validated());
        AuditLogger::forRequest($request)->updated($workLog, $original);

        return back()->with('status', 'Work log updated.');
    }

    public function destroy(Request $request, WorkLog $workLog): RedirectResponse
    {
        $snapshot = $workLog->toArray();
        $workLog->delete();
        AuditLogger::forRequest($request)->deleted($workLog, $snapshot);

        return back()->with('status', 'Work log deleted.');
    }
}
