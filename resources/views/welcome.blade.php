<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>MonitorFlow</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-slate-950 text-white">
        <main class="mx-auto flex min-h-screen max-w-7xl flex-col justify-center px-6 py-16">
            <div class="grid gap-10 lg:grid-cols-[1.3fr_0.7fr] lg:items-center">
                <section class="space-y-8">
                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-sky-200">Employee Monitoring and Productivity Management</span>
                    <div class="space-y-5">
                        <h1 class="max-w-3xl text-5xl font-semibold tracking-tight text-white md:text-6xl">SaaS-grade workforce intelligence for attendance, task execution, and productivity.</h1>
                        <p class="max-w-2xl text-lg text-slate-300">MonitorFlow centralizes time tracking, work logs, real-time activity capture, analytics, leave workflows, and performance monitoring for distributed teams.</p>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-2xl bg-brand-500 px-6 py-3 font-medium text-white shadow-panel transition hover:bg-brand-400">Open dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="rounded-2xl bg-brand-500 px-6 py-3 font-medium text-white shadow-panel transition hover:bg-brand-400">Sign in</a>
                        @endauth
                        <a href="#features" class="rounded-2xl border border-white/10 bg-white/5 px-6 py-3 font-medium text-white transition hover:bg-white/10">View features</a>
                    </div>
                </section>
                <aside class="rounded-[2rem] border border-white/10 bg-gradient-to-br from-brand-500/20 via-slate-900 to-emerald-500/10 p-6 shadow-panel">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl bg-white/10 p-4">
                            <p class="text-sm text-slate-300">Attendance Today</p>
                            <p class="mt-2 text-3xl font-semibold">94.3%</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-4">
                            <p class="text-sm text-slate-300">Productivity Avg</p>
                            <p class="mt-2 text-3xl font-semibold">82.1</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-4">
                            <p class="text-sm text-slate-300">Active Projects</p>
                            <p class="mt-2 text-3xl font-semibold">16</p>
                        </div>
                        <div class="rounded-2xl bg-white/10 p-4">
                            <p class="text-sm text-slate-300">Realtime Alerts</p>
                            <p class="mt-2 text-3xl font-semibold">Live</p>
                        </div>
                    </div>
                </aside>
            </div>
        </main>
    </body>
</html>
