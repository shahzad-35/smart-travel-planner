<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Smart Travel Planner') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|figtree:400,500,600&display=swap" rel="stylesheet" />

        <script>
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-foreground antialiased theme-transition bg-surface">
        <div class="min-h-screen grid lg:grid-cols-2">
            <!-- Brand panel (desktop) -->
            <div class="hidden lg:flex aurora-bg relative flex-col justify-between p-10 xl:p-14 bg-surface border-r border-border overflow-hidden">
                <a href="/" wire:navigate class="relative z-10 flex items-center gap-3 w-fit">
                    <x-application-logo class="w-10 h-10 text-primary" />
                    <span class="text-lg font-extrabold text-foreground tracking-tight">Smart Travel Planner</span>
                </a>

                <div class="relative z-10 max-w-md">
                    <h1 class="text-4xl xl:text-5xl font-extrabold text-foreground leading-[1.1] tracking-tight" style="text-wrap: balance">
                        Every trip,
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-primary-light to-accent">beautifully organized</span>
                    </h1>
                    <p class="mt-5 text-lg text-foreground-muted leading-relaxed">
                        Forecasts, holidays, and a packing list that writes itself — for every destination on Earth.
                    </p>

                    <!-- Destination snapshots -->
                    <div class="mt-10 flex -space-x-4" aria-hidden="true">
                        <img src="{{ asset('images/hero/h11.jpg') }}" alt="" class="w-24 h-32 object-cover rounded-xl shadow-card-hover border border-border -rotate-6">
                        <img src="{{ asset('images/hero/h1.jpg') }}" alt="" class="w-24 h-32 object-cover rounded-xl shadow-card-hover border border-border rotate-2 translate-y-2">
                        <img src="{{ asset('images/hero/h15.jpg') }}" alt="" class="w-24 h-32 object-cover rounded-xl shadow-card-hover border border-border -rotate-3">
                        <img src="{{ asset('images/hero/h13.jpg') }}" alt="" class="w-24 h-32 object-cover rounded-xl shadow-card-hover border border-border rotate-6 translate-y-1">
                    </div>
                </div>

                <dl class="relative z-10 flex gap-10">
                    <div>
                        <dt class="text-2xl font-extrabold text-foreground tabular-nums">250</dt>
                        <dd class="text-sm text-foreground-muted">countries</dd>
                    </div>
                    <div>
                        <dt class="text-2xl font-extrabold text-foreground tabular-nums">153k</dt>
                        <dd class="text-sm text-foreground-muted">cities</dd>
                    </div>
                    <div>
                        <dt class="text-2xl font-extrabold text-foreground">7-day</dt>
                        <dd class="text-sm text-foreground-muted">forecasts</dd>
                    </div>
                </dl>
            </div>

            <!-- Form panel -->
            <div class="flex flex-col items-center justify-center px-6 py-10 sm:px-10">
                <a href="/" wire:navigate class="lg:hidden mb-8 flex items-center gap-3">
                    <x-application-logo class="w-10 h-10 text-primary" />
                    <span class="text-lg font-extrabold text-foreground tracking-tight">Smart Travel Planner</span>
                </a>

                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
