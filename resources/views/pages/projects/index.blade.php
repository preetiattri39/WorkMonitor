<x-layouts.app-shell title="Project Tracking" subtitle="Track client work, project budgets, owners, and delivery health across active initiatives.">
    <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
        <section class="space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-[1.75rem] border border-white/70 bg-white p-5 shadow-lg shadow-slate-100">
                    <p class="text-sm text-slate-500">Projects</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $projects->total() }}</p>
                </div>
                <div class="rounded-[1.75rem] border border-white/70 bg-white p-5 shadow-lg shadow-slate-100">
                    <p class="text-sm text-slate-500">Active</p>
                    <p class="mt-2 text-3xl font-semibold text-emerald-600">{{ $projects->getCollection()->where('status', 'active')->count() }}</p>
                </div>
                <div class="rounded-[1.75rem] border border-white/70 bg-white p-5 shadow-lg shadow-slate-100">
                    <p class="text-sm text-slate-500">Planning / Hold</p>
                    <p class="mt-2 text-3xl font-semibold text-amber-500">{{ $projects->getCollection()->whereIn('status', ['planning', 'on_hold'])->count() }}</p>
                </div>
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                @foreach ($projects as $project)
                    <article class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-xl shadow-slate-100">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-sky-700">{{ $project->code }}</p>
                                <h3 class="mt-3 text-xl font-semibold text-slate-950">{{ $project->name }}</h3>
                                <p class="mt-2 text-sm text-slate-600">{{ $project->description ?: 'No project description provided.' }}</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">{{ str_replace('_', ' ', $project->status) }}</span>
                        </div>
                        <div class="mt-6 grid gap-3 text-sm text-slate-600 sm:grid-cols-2">
                            <p><span class="font-semibold text-slate-950">Owner:</span> {{ $project->owner?->name }}</p>
                            <p><span class="font-semibold text-slate-950">Client:</span> {{ $project->client_name ?: 'Internal' }}</p>
                            <p><span class="font-semibold text-slate-950">Tasks:</span> {{ $project->tasks_count }}</p>
                            <p><span class="font-semibold text-slate-950">Budget:</span> {{ $project->budget ? '$'.number_format((float) $project->budget, 2) : 'Not set' }}</p>
                        </div>
                        <div class="mt-6">
                            <a href="{{ route('projects.show', $project) }}" class="inline-flex rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Open project</a>
                        </div>
                    </article>
                @endforeach
            </div>

            <div>
                {{ $projects->links() }}
            </div>
        </section>

        <aside class="space-y-6">
            @if (auth()->user()->role->value !== 'employee')
                <section class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-xl shadow-slate-100">
                    <h3 class="text-lg font-semibold text-slate-950">Create Project</h3>
                    <form method="POST" action="{{ route('projects.store') }}" class="mt-6 space-y-4">
                        @csrf

                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700">Project Name</label>
                            <input id="name" name="name" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" required>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="code" class="block text-sm font-medium text-slate-700">Project Code</label>
                                <input id="code" name="code" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" required>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                                <select id="status" name="status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                                    @foreach (['planning', 'active', 'on_hold', 'completed'] as $status)
                                        <option value="{{ $status }}">{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="client_name" class="block text-sm font-medium text-slate-700">Client</label>
                            <input id="client_name" name="client_name" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-slate-700">Description</label>
                            <textarea id="description" name="description" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></textarea>
                        </div>

                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-slate-700">Start Date</label>
                                <input id="start_date" name="start_date" type="date" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-slate-700">End Date</label>
                                <input id="end_date" name="end_date" type="date" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                            </div>
                        </div>

                        <div>
                            <label for="budget" class="block text-sm font-medium text-slate-700">Budget</label>
                            <input id="budget" name="budget" type="number" min="0" step="0.01" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>

                        <button type="submit" class="w-full rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Create Project</button>
                    </form>
                </section>
            @endif
        </aside>
    </div>
</x-layouts.app-shell>
