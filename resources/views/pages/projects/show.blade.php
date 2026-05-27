<x-layouts.app-shell title="Project Details" subtitle="Review project scope, schedule, budget, and the tasks driving execution.">
    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <section class="space-y-6">
            <div class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-xl shadow-slate-100">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-sky-700">{{ $project->code }}</p>
                        <h3 class="mt-3 text-2xl font-semibold text-slate-950">{{ $project->name }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $project->description ?: 'No project description provided.' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        <p><span class="font-semibold text-slate-950">Owner:</span> {{ $project->owner?->name }}</p>
                        <p class="mt-2"><span class="font-semibold text-slate-950">Client:</span> {{ $project->client_name ?: 'Internal' }}</p>
                        <p class="mt-2"><span class="font-semibold text-slate-950">Status:</span> {{ ucwords(str_replace('_', ' ', $project->status)) }}</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Budget</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950">{{ $project->budget ? '$'.number_format((float) $project->budget, 2) : 'Not set' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">Start</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950">{{ $project->start_date?->format('M d, Y') ?? 'TBD' }}</p>
                    </div>
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <p class="text-sm text-slate-500">End</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950">{{ $project->end_date?->format('M d, Y') ?? 'TBD' }}</p>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white shadow-xl shadow-slate-100">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="text-lg font-semibold text-slate-950">Project Tasks</h3>
                    <p class="text-sm text-slate-500">Assignments and completion progress tied to this project.</p>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse ($project->tasks as $task)
                        <div class="flex flex-col gap-3 px-6 py-4 lg:flex-row lg:items-center lg:justify-between">
                            <div>
                                <a href="{{ route('tasks.show', $task) }}" class="font-semibold text-slate-950 transition hover:text-sky-700">{{ $task->title }}</a>
                                <p class="mt-1 text-sm text-slate-500">{{ $task->assignee?->name }} · {{ ucwords(str_replace('_', ' ', $task->status->value)) }}</p>
                            </div>
                            <div class="w-full max-w-xs">
                                <div class="h-2 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-sky-500" style="width: {{ $task->completion_percentage }}%"></div>
                                </div>
                                <p class="mt-2 text-right text-xs text-slate-500">{{ $task->completion_percentage }}%</p>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-8 text-sm text-slate-500">No tasks have been assigned to this project yet.</div>
                    @endforelse
                </div>
            </div>
        </section>

        <aside class="space-y-6">
            @if (auth()->user()->role->value !== 'employee')
                <section class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-xl shadow-slate-100">
                    <h3 class="text-lg font-semibold text-slate-950">Update Project</h3>
                    <form method="POST" action="{{ route('projects.update', $project) }}" class="mt-6 space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700">Project Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $project->name) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>

                        <div>
                            <label for="client_name" class="block text-sm font-medium text-slate-700">Client</label>
                            <input id="client_name" name="client_name" type="text" value="{{ old('client_name', $project->client_name) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                            <select id="status" name="status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                                @foreach (['planning', 'active', 'on_hold', 'completed'] as $status)
                                    <option value="{{ $status }}" @selected($project->status === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                            <textarea id="description" name="description" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">{{ old('description', $project->description) }}</textarea>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-slate-700">Start Date</label>
                                <input id="start_date" name="start_date" type="date" value="{{ optional($project->start_date)->format('Y-m-d') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-slate-700">End Date</label>
                                <input id="end_date" name="end_date" type="date" value="{{ optional($project->end_date)->format('Y-m-d') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            </div>
                        </div>

                        <div>
                            <label for="budget" class="block text-sm font-medium text-slate-700">Budget</label>
                            <input id="budget" name="budget" type="number" min="0" step="0.01" value="{{ old('budget', $project->budget) }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>

                        <button type="submit" class="w-full rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Save Project</button>
                    </form>
                </section>
            @endif
        </aside>
    </div>
</x-layouts.app-shell>
