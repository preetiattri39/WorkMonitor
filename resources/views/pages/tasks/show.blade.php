<x-layouts.app-shell title="Task Details" subtitle="Review assignment context, progress, work logs, and update the delivery plan from a single page.">
    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <section class="space-y-6">
            <div class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-xl shadow-slate-100">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-700">{{ strtoupper($task->priority) }} priority</p>
                        <h3 class="mt-3 text-2xl font-semibold text-slate-950">{{ $task->title }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $task->description ?: 'No task description has been provided yet.' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        <p><span class="font-semibold text-slate-950">Project:</span> {{ $task->project?->name }}</p>
                        <p class="mt-2"><span class="font-semibold text-slate-950">Assignee:</span> {{ $task->assignee?->name }}</p>
                        <p class="mt-2"><span class="font-semibold text-slate-950">Due:</span> {{ $task->due_at?->format('M d, Y H:i') ?? 'Not scheduled' }}</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Status</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950">{{ ucwords(str_replace('_', ' ', $task->status->value)) }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Completion</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950">{{ $task->completion_percentage }}%</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Estimated Time</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950">{{ $task->estimated_minutes }} mins</p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white shadow-xl shadow-slate-100">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="text-lg font-semibold text-slate-950">Linked Work Logs</h3>
                    <p class="text-sm text-slate-500">Execution history captured against this task.</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($task->workLogs as $log)
                        <div class="px-6 py-4">
                            <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                                <div>
                                    <p class="font-semibold text-slate-950">{{ $log->user?->name }}</p>
                                    <p class="mt-1 text-sm text-slate-600">{{ $log->summary }}</p>
                                </div>
                                <div class="text-sm text-slate-500">
                                    <p>{{ $log->work_date->format('M d, Y') }}</p>
                                    <p class="mt-1">{{ $log->duration_minutes }} minutes</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-sm text-slate-500">No work logs linked to this task yet.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-xl shadow-slate-100">
                <h3 class="text-lg font-semibold text-slate-950">Update Task</h3>
                <form method="POST" action="{{ route('tasks.update', $task) }}" class="mt-6 space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="project_id" class="block text-sm font-medium text-slate-700">Project</label>
                        <select id="project_id" name="project_id" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" @selected($task->project_id === $project->id)>{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="assigned_to" class="block text-sm font-medium text-slate-700">Assignee</label>
                        <select id="assigned_to" name="assigned_to" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}" @selected($task->assigned_to === $employee->id)>{{ $employee->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-700">Title</label>
                        <input id="title" name="title" type="text" value="{{ old('title', $task->title) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                        <textarea id="description" name="description" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">{{ old('description', $task->description) }}</textarea>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="priority" class="block text-sm font-medium text-slate-700">Priority</label>
                            <select id="priority" name="priority" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                                @foreach (['low', 'medium', 'high', 'critical'] as $priority)
                                    <option value="{{ $priority }}" @selected($task->priority === $priority)>{{ ucfirst($priority) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                            <select id="status" name="status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                                @foreach (['todo', 'in_progress', 'review', 'blocked', 'done'] as $status)
                                    <option value="{{ $status }}" @selected($task->status->value === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label for="due_at" class="block text-sm font-medium text-slate-700">Due At</label>
                            <input id="due_at" name="due_at" type="datetime-local" value="{{ optional($task->due_at)->format('Y-m-d\TH:i') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                        <div>
                            <label for="estimated_minutes" class="block text-sm font-medium text-slate-700">Estimated Minutes</label>
                            <input id="estimated_minutes" name="estimated_minutes" type="number" min="0" value="{{ old('estimated_minutes', $task->estimated_minutes) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>
                    </div>

                    <div>
                        <label for="completion_percentage" class="block text-sm font-medium text-slate-700">Completion Percentage</label>
                        <input id="completion_percentage" name="completion_percentage" type="number" min="0" max="100" value="{{ old('completion_percentage', $task->completion_percentage) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Save Changes</button>
                </form>
            </section>
        </aside>
    </div>
</x-layouts.app-shell>
