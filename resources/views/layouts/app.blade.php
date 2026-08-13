<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Smart Travel Planner') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|figtree:400,500,600&display=swap" rel="stylesheet"/>

    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="font-sans antialiased bg-surface text-foreground theme-transition">
<div class="min-h-screen flex flex-col">
    <livewire:layout.navigation/>

    @if (isset($header))
        <header class="bg-surface-card shadow-card border-b border-border">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                {{ $header }}
            </div>
        </header>
    @endif

    <main class="flex-1 py-6 px-4 sm:px-6 lg:px-8">
        {{ $slot }}
    </main>
</div>
@stack('scripts')
@livewireScripts
</body>
</html>
