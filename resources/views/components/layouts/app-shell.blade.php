@props([
    'title',
    'subtitle' => null,
])

@php
    $user = auth()->user();
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'dashboard', 'match' => 'dashboard*'],
        ['label' => 'Attendance', 'route' => 'attendances.index', 'match' => 'attendances*'],
        ['label' => 'Work Logs', 'route' => 'work-logs.index', 'match' => 'work-logs*'],
        ['label' => 'Tasks', 'route' => 'tasks.index', 'match' => 'tasks*'],
        ['label' => 'Projects', 'route' => 'projects.index', 'match' => 'projects*'],
        ['label' => 'Leave', 'route' => 'leave-requests.index', 'match' => 'leave-requests*'],
    ];

    if (in_array($user->role->value, ['admin', 'manager'], true)) {
        $navItems[] = ['label' => 'Audit Logs', 'route' => 'audit-logs.index', 'match' => 'audit-logs*'];
    }
@endphp

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ $title }} | MonitorFlow</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(38,132,255,0.12),_transparent_28%),linear-gradient(180deg,#f8fbff_0%,#edf3ff_100%)] text-slate-900">
        <div class="mx-auto flex min-h-screen max-w-7xl flex-col gap-6 px-4 py-4 sm:px-6 lg:flex-row lg:px-8 lg:py-6">
            <aside class="w-full shrink-0 rounded-[2rem] bg-slate-950 p-6 text-white shadow-2xl shadow-slate-950/30 lg:sticky lg:top-6 lg:h-[calc(100vh-3rem)] lg:w-80">
                <div class="flex h-full flex-col">
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-200">MonitorFlow</p>
                            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Employee operations cloud</h1>
                            <p class="mt-3 text-sm text-slate-300">Attendance, work intelligence, delivery tracking, auditability, and approvals in one secure workspace.</p>
                        </div>

                        <div class="rounded-[1.5rem] border border-white/10 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-[0.25em] text-slate-400">Signed in as</p>
                            <div class="mt-3 flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-lg font-semibold">{{ $user->name }}</p>
                                    <p class="text-sm text-slate-300">{{ $user->email }}</p>
                                </div>
                                <span class="rounded-full bg-sky-400/15 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-sky-200">{{ $user->role->value }}</span>
                            </div>
                        </div>
                    </div>

                    <nav class="mt-8 space-y-2">
                        @foreach ($navItems as $item)
                            <a
                                href="{{ route($item['route']) }}"
                                class="{{ request()->routeIs($item['match']) ? 'bg-white text-slate-950 shadow-lg shadow-white/10' : 'bg-white/5 text-slate-200 hover:bg-white/10' }} flex items-center justify-between rounded-2xl px-4 py-3 text-sm font-medium transition"
                            >
                                <span>{{ $item['label'] }}</span>
                                <span class="text-xs uppercase tracking-[0.2em] {{ request()->routeIs($item['match']) ? 'text-slate-500' : 'text-slate-400' }}">Open</span>
                            </a>
                        @endforeach
                    </nav>

                    <div class="mt-auto space-y-4 pt-8">
                        <div class="rounded-[1.5rem] border border-emerald-400/20 bg-emerald-400/10 p-4">
                            <p class="text-xs uppercase tracking-[0.25em] text-emerald-200">Realtime Stack</p>
                            <p class="mt-2 text-sm text-emerald-50">Laravel 12, Livewire, Vue, Reverb, audit logs, monitoring APIs, and role-aware workflows.</p>
                        </div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm font-medium text-white transition hover:bg-white/10">Sign out</button>
                        </form>
                    </div>
                </div>
            </aside>

            <main class="min-w-0 flex-1 space-y-6">
                <header class="rounded-[2rem] border border-white/70 bg-white/90 p-6 shadow-xl shadow-sky-100/60 backdrop-blur">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="space-y-2">
                            <p class="text-xs font-semibold uppercase tracking-[0.35em] text-sky-700">Operational Workspace</p>
                            <h2 class="text-3xl font-semibold tracking-tight text-slate-950">{{ $title }}</h2>
                            @if ($subtitle)
                                <p class="max-w-3xl text-sm text-slate-600">{{ $subtitle }}</p>
                            @endif
                        </div>
                        @if (isset($actions))
                            <div class="flex shrink-0 flex-wrap gap-3">
                                {{ $actions }}
                            </div>
                        @endif
                    </div>
                </header>

                @if (session('status'))
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700">
                        <p class="font-semibold">Please review the highlighted form inputs.</p>
                        <ul class="mt-2 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </body>
</html>
