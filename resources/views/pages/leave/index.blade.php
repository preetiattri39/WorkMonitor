<x-layouts.app-shell title="Leave Management" subtitle="Submit leave requests, review approval queues, and maintain workforce availability visibility.">
    <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
        <section class="space-y-6">
            <div class="overflow-hidden rounded-[2rem] border border-white/70 bg-white shadow-xl shadow-slate-100">
                <div class="border-b border-slate-100 px-6 py-5">
                    <h3 class="text-lg font-semibold text-slate-950">Leave Queue</h3>
                    <p class="text-sm text-slate-500">Current leave requests with approver status and request windows.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50/80 text-left text-slate-500">
                            <tr>
                                <th class="px-6 py-4 font-medium">Employee</th>
                                <th class="px-6 py-4 font-medium">Type</th>
                                <th class="px-6 py-4 font-medium">Dates</th>
                                <th class="px-6 py-4 font-medium">Status</th>
                                <th class="px-6 py-4 font-medium">Approver</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($leaveRequests as $leaveRequest)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-slate-900">{{ $leaveRequest->user?->name }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $leaveRequest->leave_type }}</td>
                                    <td class="px-6 py-4 text-slate-600">{{ $leaveRequest->start_date->format('M d') }} - {{ $leaveRequest->end_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4"><span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-600">{{ $leaveRequest->status->value }}</span></td>
                                    <td class="px-6 py-4 text-slate-600">{{ $leaveRequest->approver?->name ?? 'Pending' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $leaveRequests->links() }}
                </div>
            </div>
        </section>

        <aside class="space-y-6">
            <section class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-xl shadow-slate-100">
                <h3 class="text-lg font-semibold text-slate-950">Submit Leave Request</h3>
                <form method="POST" action="{{ route('leave-requests.store') }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="leave_type" class="block text-sm font-medium text-slate-700">Leave Type</label>
                        <input id="leave_type" name="leave_type" type="text" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm" placeholder="Annual, Sick, Emergency">
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
                        <label for="reason" class="block text-sm font-medium text-slate-700">Reason</label>
                        <textarea id="reason" name="reason" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm"></textarea>
                    </div>

                    <button type="submit" class="w-full rounded-2xl bg-slate-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Submit Request</button>
                </form>
            </section>

            @if (auth()->user()->role->value !== 'employee')
                <section class="rounded-[2rem] border border-white/70 bg-white p-6 shadow-xl shadow-slate-100">
                    <h3 class="text-lg font-semibold text-slate-950">Approve or Reject</h3>
                    <div class="mt-6 space-y-4">
                        @foreach ($leaveRequests->getCollection()->take(4) as $leaveRequest)
                            <form method="POST" action="{{ route('leave-requests.update', $leaveRequest) }}" class="rounded-2xl border border-slate-100 p-4">
                                @csrf
                                @method('PUT')
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-slate-950">{{ $leaveRequest->user?->name }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $leaveRequest->leave_type }} · {{ $leaveRequest->start_date->format('M d') }} - {{ $leaveRequest->end_date->format('M d') }}</p>
                                    </div>
                                    <select name="status" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                                        @foreach (['pending', 'approved', 'rejected', 'cancelled'] as $status)
                                            <option value="{{ $status }}" @selected($leaveRequest->status->value === $status)>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="mt-4 w-full rounded-xl bg-sky-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-sky-500">Update Status</button>
                            </form>
                        @endforeach
                    </div>
                </section>
            @endif
        </aside>
    </div>
</x-layouts.app-shell>
