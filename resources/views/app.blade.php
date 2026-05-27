<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="user-id" content="{{ auth()->id() }}">
        <meta name="user-name" content="{{ auth()->user()?->name }}">
        <meta name="user-role" content="{{ auth()->user()?->role->value }}">
        <title>MonitorFlow Dashboard</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <script id="dashboard-overview" type="application/json">@json($overview)</script>
        <div id="app"></div>
        @livewire('dashboard.presence-widget')
        @livewireScripts
    </body>
</html>
