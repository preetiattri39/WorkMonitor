<x-layouts.app-shell title="Daily Work Logs" subtitle="Capture day-level execution summaries with task links, time windows, and billable visibility.">
    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <section class="space-y-6">
            <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white shadow-xl shadow-slate-100">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="text-lg font-semibold text-slate-950">Work Log Register</h3>
                    <p class="text-sm text-slate-500">Employee work summaries with duration, linked task, project, and billable markers.</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @foreach ($workLogs as $workLog)
                        <div class="flex flex-col gap-4 px-6 py-5 lg:flex-row lg:items-start lg:justify-between">
                            <div class="max-w-3xl">
                                <div class="flex flex-wrap items-center gap-3">
                                    <p class="font-semibold text-slate-950">{{ $workLog->user?->name }}</p>
                                    @if ($workLog->billable)
                                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-emerald-700">Billable</span>
                                    @endif
                                </div>
                                <p class="mt-3 text-sm text-slate-600">{{ $workLog->summary }}</p>
                                <div class="mt-3 flex flex-wrap gap-4 text-xs uppercase tracking-[0.2em] text-slate-400">
                                    <span>{{ $workLog->project?->name ?? 'No project' }}</span>
                                    <span>{{ $workLog->task?->title ?? 'No task' }}</span>
                                    <span>{{ $workLog->screenshots_count }} screenshots</span>
                                </div>
                            </div>
                            <div class="space-y-3 text-sm text-slate-500">
                                <p>{{ $workLog->work_date->format('M d, Y') }}</p>
                                <p>{{ $workLog->started_at->format('H:i') }} - {{ $workLog->ended_at?->format('H:i') ?? 'Open' }}</p>
                                <p>{{ $workLog->duration_minutes }} minutes</p>
                                <form method="POST" action="{{ route('work-logs.destroy', $workLog) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-xl border border-rose-200 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">Delete</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $workLogs->links() }}
                </div>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-xl shadow-slate-100">
                <h3 class="text-lg font-semibold text-slate-950">Create Work Log</h3>
                <form method="POST" action="{{ route('work-logs.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="work_date" class="block text-sm font-medium text-slate-700">Work Date</label>
                        <input id="work_date" name="work_date" type="date" value="{{ now()->toDateString() }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="started_at" class="block text-sm font-medium text-slate-700">Started At</label>
                            <input id="started_at" name="started_at" type="datetime-local" value="{{ now()->subHour()->format('Y-m-d\TH:i') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label for="ended_at" class="block text-sm font-medium text-slate-700">Ended At</label>
                            <input id="ended_at" name="ended_at" type="datetime-local" value="{{ now()->format('Y-m-d\TH:i') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="project_id" class="block text-sm font-medium text-slate-700">Project</label>
                            <select id="project_id" name="project_id" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                                <option value="">No project</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="task_id" class="block text-sm font-medium text-slate-700">Task</label>
                            <select id="task_id" name="task_id" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                                <option value="">No task</option>
                                @foreach ($tasks as $task)
                                    <option value="{{ $task->id }}">{{ $task->title }}{{ $task->project ? ' · '.$task->project->name : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label for="duration_minutes" class="block text-sm font-medium text-slate-700">Duration Minutes</label>
                        <input id="duration_minutes" name="duration_minutes" type="number" min="1" value="60" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label for="summary" class="block text-sm font-medium text-slate-700">Summary</label>
                        <textarea id="summary" name="summary" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" required></textarea>
                    </div>

                    <label class="flex items-center gap-3 text-sm text-slate-600">
                        <input type="checkbox" name="billable" value="1" class="h-4 w-4 rounded border-slate-300 text-sky-600">
                        <span>Mark as billable</span>
                    </label>

                    <button type="submit" class="w-full rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Save Work Log</button>
                </form>
            </section>
        </aside>
    </div>
</x-layouts.app-shell>
