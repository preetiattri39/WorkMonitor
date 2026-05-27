<x-layouts.app-shell title="Task Management" subtitle="Manage assignments, priorities, due dates, and delivery progress across the workforce.">
    <div class="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <section class="space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-[1.75rem] border border-white/70 bg-white p-5 shadow-lg shadow-slate-100">
                    <p class="text-sm text-slate-500">Total Tasks</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $tasks->total() }}</p>
                </div>
                <div class="rounded-[1.75rem] border border-white/70 bg-white p-5 shadow-lg shadow-slate-100">
                    <p class="text-sm text-slate-500">In Progress</p>
                    <p class="mt-2 text-3xl font-semibold text-amber-500">{{ $tasks->getCollection()->where('status.value', 'in_progress')->count() }}</p>
                </div>
                <div class="rounded-[1.75rem] border border-white/70 bg-white p-5 shadow-lg shadow-slate-100">
                    <p class="text-sm text-slate-500">Completed</p>
                    <p class="mt-2 text-3xl font-semibold text-emerald-600">{{ $tasks->getCollection()->where('status.value', 'done')->count() }}</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white shadow-xl shadow-slate-100">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-950">Task Pipeline</h3>
                        <p class="text-sm text-slate-500">Open assignments with project, assignee, progress, and due date context.</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50/80 text-left text-slate-500">
                            <tr>
                                <th class="px-6 py-4 font-medium">Task</th>
                                <th class="px-6 py-4 font-medium">Project</th>
                                <th class="px-6 py-4 font-medium">Assignee</th>
                                <th class="px-6 py-4 font-medium">Status</th>
                                <th class="px-6 py-4 font-medium">Progress</th>
                                <th class="px-6 py-4 font-medium">Due</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($tasks as $task)
                                <tr class="align-top">
                                    <td class="px-6 py-4">
                                        <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-slate-900 transition hover:text-sky-700">{{ $task->title }}</a>
                                        <p class="mt-1 text-xs uppercase tracking-[0.2em] text-slate-400">{{ strtoupper($task->priority) }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">{{ $task->project?->name }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $task->assignee?->name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">{{ str_replace('_', ' ', $task->status->value) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="h-2 w-32 overflow-hidden rounded-full bg-slate-100">
                                            <div class="h-full rounded-full bg-sky-500" style="width: {{ $task->completion_percentage }}%"></div>
                                        </div>
                                        <p class="mt-2 text-xs text-slate-500">{{ $task->completion_percentage }}% complete</p>
                                    </td>
                                    <td class="px-6 py-4 text-slate-600">{{ $task->due_at?->format('M d, Y H:i') ?? 'No deadline' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $tasks->links() }}
                </div>
            </div>
        </section>

        <aside class="space-y-6">
            @if (auth()->user()->role->value !== 'employee')
                <section class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-xl shadow-slate-100">
                    <h3 class="text-lg font-semibold text-slate-950">Create Task</h3>
                    <p class="mt-1 text-sm text-slate-500">Assign work with ownership, timeline, and measurable completion targets.</p>

                    <form method="POST" action="{{ route('tasks.store') }}" class="mt-6 space-y-4">
                        @csrf

                        <div>
                            <label for="project_id" class="block text-sm font-medium text-slate-700">Project</label>
                            <select id="project_id" name="project_id" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->name }} ({{ $project->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="assigned_to" class="block text-sm font-medium text-slate-700">Assign To</label>
                            <select id="assigned_to" name="assigned_to" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }} ({{ $employee->role->value }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="title" class="block text-sm font-medium text-slate-700">Title</label>
                            <input id="title" name="title" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" required>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                            <textarea id="description" name="description" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></textarea>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="priority" class="block text-sm font-medium text-slate-700">Priority</label>
                                <select id="priority" name="priority" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                                    @foreach (['low', 'medium', 'high', 'critical'] as $priority)
                                        <option value="{{ $priority }}">{{ ucfirst($priority) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                                <select id="status" name="status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                                    @foreach (['todo', 'in_progress', 'review', 'blocked', 'done'] as $status)
                                        <option value="{{ $status }}">{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="due_at" class="block text-sm font-medium text-slate-700">Due At</label>
                                <input id="due_at" name="due_at" type="datetime-local" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            </div>
                            <div>
                                <label for="estimated_minutes" class="block text-sm font-medium text-slate-700">Estimated Minutes</label>
                                <input id="estimated_minutes" name="estimated_minutes" type="number" min="0" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" value="60">
                            </div>
                        </div>

                        <div>
                            <label for="completion_percentage" class="block text-sm font-medium text-slate-700">Completion Percentage</label>
                            <input id="completion_percentage" name="completion_percentage" type="number" min="0" max="100" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" value="0">
                        </div>

                        <button type="submit" class="w-full rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Create Task</button>
                    </form>
                </section>
            @endif

            <section class="rounded-[2rem] border border-white/70 bg-slate-950 p-6 text-white shadow-xl shadow-slate-200">
                <h3 class="text-lg font-semibold">Operational Guidance</h3>
                <div class="mt-4 space-y-3 text-sm text-slate-300">
                    <p>Use task detail pages to update progress, reassign ownership, and link work logs to execution history.</p>
                    <p>Realtime task updates broadcast through Reverb to assignees and managers for immediate dashboard refresh.</p>
                </div>
            </section>
        </aside>
    </div>
</x-layouts.app-shell>
