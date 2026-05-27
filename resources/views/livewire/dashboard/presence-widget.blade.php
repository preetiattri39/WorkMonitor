<div class="fixed bottom-6 right-6 hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-panel xl:block">
    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Today</p>
    <div class="mt-3 grid grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-slate-500">Present</p>
            <p class="text-2xl font-semibold text-emerald-600">{{ $presentCount }}</p>
        </div>
        <div>
            <p class="text-sm text-slate-500">Absent</p>
            <p class="text-2xl font-semibold text-rose-600">{{ $absentCount }}</p>
        </div>
    </div>
</div>
