<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login | MonitorFlow</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="min-h-screen bg-slate-950 text-white">
        <main class="mx-auto flex min-h-screen max-w-7xl items-center px-6 py-16">
            <div class="grid w-full gap-10 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
                <section class="space-y-6">
                    <span class="inline-flex rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-sky-200">MonitorFlow Access</span>
                    <div class="space-y-4">
                        <h1 class="max-w-3xl text-4xl font-semibold tracking-tight text-white md:text-5xl">Sign in to the monitoring dashboard.</h1>
                        <p class="max-w-2xl text-lg text-slate-300">Use one of the seeded accounts to access attendance, projects, work logs, and analytics.</p>
                    </div>
                    <div class="rounded-[2rem] border border-white/10 bg-white/5 p-6 text-slate-200 shadow-xl shadow-slate-950/30">
                        <p class="text-sm uppercase tracking-[0.2em] text-slate-400">Seeded credentials</p>
                        <div class="mt-4 space-y-2 text-sm">
                            <p><span class="font-semibold text-white">Admin:</span> admin@monitorflow.test</p>
                            <p><span class="font-semibold text-white">Manager:</span> manager@monitorflow.test</p>
                            <p><span class="font-semibold text-white">Password:</span> password</p>
                        </div>
                    </div>
                </section>
                <section class="mx-auto w-full max-w-xl rounded-[2rem] border border-white/10 bg-slate-900/80 p-8 shadow-2xl shadow-slate-950/40 backdrop-blur">
                    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-6">
                        @csrf

                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-medium text-slate-200">Email</label>
                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                class="block w-full rounded-2xl border border-white/10 bg-slate-950/80 px-4 py-3 text-base text-white outline-none transition placeholder:text-slate-500 focus:border-sky-400"
                            >
                            @error('email')
                                <p class="text-sm text-rose-300">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-medium text-slate-200">Password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                class="block w-full rounded-2xl border border-white/10 bg-slate-950/80 px-4 py-3 text-base text-white outline-none transition focus:border-sky-400"
                            >
                        </div>

                        <label class="flex items-center gap-3 text-sm text-slate-300">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-white/10 bg-slate-950/80 text-sky-400 focus:ring-sky-400">
                            <span>Keep me signed in</span>
                        </label>

                        <button type="submit" class="block w-full rounded-2xl bg-sky-500 px-6 py-3 text-base font-medium text-white transition hover:bg-sky-400">
                            Sign in
                        </button>
                    </form>
                </section>
            </div>
        </main>
    </body>
</html>
