@props(['title'])

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }}</title>
        @vite(['resources/css/app.css'])
    </head>
    <body class="bg-slate-100">
        <div class="mx-auto max-w-5xl px-6 py-16">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-10 shadow-panel">
                <h1 class="text-3xl font-semibold text-slate-900">{{ $title }}</h1>
                <p class="mt-3 text-slate-600">This section is intentionally light because the primary management experience is delivered through the Vue dashboard shell and APIs.</p>
            </div>
        </div>
    </body>
</html>
