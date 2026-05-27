<x-layouts.app-shell title="Audit Logs" subtitle="Review system changes, actors, payload diffs, and request metadata for governance and troubleshooting.">
    <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white shadow-xl shadow-slate-100">
        <div class="border-b border-slate-100 px-6 py-5">
            <h3 class="text-lg font-semibold text-slate-950">Recent Audit Events</h3>
            <p class="text-sm text-slate-500">CRUD actions across tasks, projects, attendance, work logs, and leave workflows.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 text-sm">
                <thead class="bg-slate-50/80 text-left text-slate-500">
                    <tr>
                        <th class="px-6 py-4 font-medium">Timestamp</th>
                        <th class="px-6 py-4 font-medium">Actor</th>
                        <th class="px-6 py-4 font-medium">Action</th>
                        <th class="px-6 py-4 font-medium">Entity</th>
                        <th class="px-6 py-4 font-medium">Network</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($logs as $log)
                        <tr class="align-top">
                            <td class="px-6 py-4 text-slate-600">{{ $log->created_at?->format('M d, Y H:i:s') }}</td>
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $log->actor?->name ?? 'System' }}</td>
                            <td class="px-6 py-4"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">{{ $log->action }}</span></td>
                            <td class="px-6 py-4 text-slate-600">
                                <p>{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</p>
                                @if ($log->new_values)
                                    <pre class="mt-3 overflow-x-auto rounded-2xl bg-slate-950 p-3 text-xs text-slate-200">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-slate-600">
                                <p>{{ $log->ip_address ?? 'n/a' }}</p>
                                <p class="mt-2 max-w-xs break-words text-xs text-slate-500">{{ $log->user_agent }}</p>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 px-6 py-4">
            {{ $logs->links() }}
        </div>
    </div>
</x-layouts.app-shell>
