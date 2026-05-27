<x-layouts.app-shell title="Attendance Tracking" subtitle="Capture clock-ins, close shifts, and monitor workforce attendance quality across teams.">
    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <section class="space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-[1.75rem] border border-white/70 bg-white p-5 shadow-lg shadow-slate-100">
                    <p class="text-sm text-slate-500">Records</p>
                    <p class="mt-2 text-3xl font-semibold text-slate-950">{{ $attendances->total() }}</p>
                </div>
                <div class="rounded-[1.75rem] border border-white/70 bg-white p-5 shadow-lg shadow-slate-100">
                    <p class="text-sm text-slate-500">Present / Remote</p>
                    <p class="mt-2 text-3xl font-semibold text-emerald-600">{{ $attendances->getCollection()->filter(fn ($attendance) => in_array($attendance->status->value, ['present', 'remote'], true))->count() }}</p>
                </div>
                <div class="rounded-[1.75rem] border border-white/70 bg-white p-5 shadow-lg shadow-slate-100">
                    <p class="text-sm text-slate-500">Late / Absent</p>
                    <p class="mt-2 text-3xl font-semibold text-rose-500">{{ $attendances->getCollection()->filter(fn ($attendance) => in_array($attendance->status->value, ['late', 'absent'], true))->count() }}</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white shadow-xl shadow-slate-100">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="text-lg font-semibold text-slate-950">Attendance Log</h3>
                    <p class="text-sm text-slate-500">Shift records with times, worked minutes, status, and notes.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50/80 text-left text-slate-500">
                            <tr>
                                <th class="px-6 py-4 font-medium">Employee</th>
                                <th class="px-6 py-4 font-medium">Date</th>
                                <th class="px-6 py-4 font-medium">Status</th>
                                <th class="px-6 py-4 font-medium">Clock In</th>
                                <th class="px-6 py-4 font-medium">Clock Out</th>
                                <th class="px-6 py-4 font-medium">Worked</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($attendances as $attendance)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ $attendance->user?->name }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $attendance->date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">{{ str_replace('_', ' ', $attendance->status->value) }}</span></td>
                                    <td class="px-6 py-4 text-slate-600">{{ $attendance->clock_in_at?->format('H:i') ?? '-' }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $attendance->clock_out_at?->format('H:i') ?? '-' }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $attendance->worked_minutes }} mins</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $attendances->links() }}
                </div>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-xl shadow-slate-100">
                <h3 class="text-lg font-semibold text-slate-950">Log Attendance</h3>
                <form method="POST" action="{{ route('attendances.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="date" class="block text-sm font-medium text-slate-700">Date</label>
                        <input id="date" name="date" type="date" value="{{ now()->toDateString() }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label for="clock_in_at" class="block text-sm font-medium text-slate-700">Clock In</label>
                        <input id="clock_in_at" name="clock_in_at" type="datetime-local" value="{{ now()->format('Y-m-d\TH:i') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-slate-700">Status</label>
                        <select id="status" name="status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                            @foreach (['present', 'late', 'absent', 'half_day', 'remote', 'on_leave'] as $status)
                                <option value="{{ $status }}">{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-slate-700">Notes</label>
                        <textarea id="notes" name="notes" rows="3" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></textarea>
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Log Attendance</button>
                </form>
            </section>

            @if ($todayAttendance)
                <section class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-xl shadow-slate-100">
                    <h3 class="text-lg font-semibold text-slate-950">Close Today&apos;s Shift</h3>
                    <form method="POST" action="{{ route('attendances.update', $todayAttendance) }}" class="mt-6 space-y-4">
                        @csrf
                        @method('PUT')

                        <div>
                            <label for="clock_out_at" class="block text-sm font-medium text-slate-700">Clock Out</label>
                            <input id="clock_out_at" name="clock_out_at" type="datetime-local" value="{{ now()->format('Y-m-d\TH:i') }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>

                        <div>
                            <label for="worked_minutes" class="block text-sm font-medium text-slate-700">Worked Minutes</label>
                            <input id="worked_minutes" name="worked_minutes" type="number" min="0" value="{{ $todayAttendance->worked_minutes ?: 480 }}" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm">
                        </div>

                        <div>
                            <label for="update_status" class="block text-sm font-medium text-slate-700">Status</label>
                            <select id="update_status" name="status" class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm">
                                @foreach (['present', 'late', 'absent', 'half_day', 'remote', 'on_leave'] as $status)
                                    <option value="{{ $status }}" @selected($todayAttendance->status->value === $status)>{{ ucwords(str_replace('_', ' ', $status)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" class="w-full rounded-2xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-sky-500">Update Shift</button>
                    </form>
                </section>
            @endif
        </aside>
    </div>
</x-layouts.app-shell>
