<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Smart Travel Planner</title>

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
    <body class="antialiased font-sans bg-surface text-foreground theme-transition">
        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            <nav class="relative z-10 px-6 py-4">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <x-application-logo class="h-10 w-10 text-primary" />
                        <span class="text-xl font-bold text-foreground">Smart Travel Planner</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <!-- Dark Mode Toggle -->
                        <button
                            x-data="{ dark: document.documentElement.classList.contains('dark') }"
                            @click="dark = !dark; localStorage.theme = dark ? 'dark' : 'light'; document.documentElement.classList.toggle('dark', dark)"
                            class="p-2 rounded-lg text-foreground-muted hover:text-foreground hover:bg-surface-muted transition-colors cursor-pointer"
                            aria-label="Toggle dark mode"
                        >
                            <svg x-show="!dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                            <svg x-show="dark" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        </button>

                        @if (Route::has('login'))
                            <livewire:welcome.navigation />
                        @endif
                    </div>
                </div>
            </nav>

            <!-- Hero Section -->
            <section class="relative flex-1 flex items-center overflow-hidden">
                <!-- Background Gradient -->
                <div class="absolute inset-0 bg-gradient-to-br from-teal-600 via-teal-500 to-emerald-400 dark:from-teal-950 dark:via-teal-900 dark:to-emerald-950"></div>

                <!-- Decorative Elements -->
                <div class="absolute inset-0 overflow-hidden">
                    <svg class="absolute -top-10 -right-10 w-96 h-96 text-white/10" viewBox="0 0 200 200" fill="currentColor">
                        <circle cx="100" cy="100" r="80"/>
                    </svg>
                    <svg class="absolute bottom-0 left-0 w-64 h-64 text-white/5" viewBox="0 0 200 200" fill="currentColor">
                        <polygon points="100,10 40,198 190,78 10,78 160,198"/>
                    </svg>
                    <svg class="absolute top-1/3 left-1/4 w-48 h-48 text-white/5" viewBox="0 0 100 100" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="50" cy="50" r="45"/>
                        <line x1="50" y1="5" x2="50" y2="95"/>
                        <line x1="5" y1="50" x2="95" y2="50"/>
                        <ellipse cx="50" cy="50" rx="20" ry="45"/>
                    </svg>
                </div>

                <div class="relative max-w-7xl mx-auto px-6 py-24 sm:py-32 lg:py-40 w-full">
                    <div class="max-w-2xl">
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-white leading-tight">
                            Plan Your
                            <span class="text-amber-300">Perfect Journey</span>
                        </h1>
                        <p class="mt-6 text-lg sm:text-xl text-teal-50/90 leading-relaxed">
                            Organize trips, track weather forecasts, discover destinations, and pack smart — all in one place.
                        </p>
                        <div class="mt-10 flex flex-col sm:flex-row gap-4">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-white text-teal-700 rounded-xl font-semibold text-lg hover:bg-teal-50 transition-colors shadow-lg cursor-pointer">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                    Go to Dashboard
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-white text-teal-700 rounded-xl font-semibold text-lg hover:bg-teal-50 transition-colors shadow-lg cursor-pointer">
                                    Get Started Free
                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-white/10 text-white border border-white/30 rounded-xl font-semibold text-lg hover:bg-white/20 transition-colors backdrop-blur-sm cursor-pointer">
                                    Sign In
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="py-20 px-6 bg-surface-card dark:bg-surface">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-16">
                        <h2 class="text-3xl sm:text-4xl font-bold text-foreground">Everything You Need to Travel Smart</h2>
                        <p class="mt-4 text-lg text-foreground-muted max-w-2xl mx-auto">Plan, organize, and enjoy your trips with powerful tools designed for modern travelers.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <!-- Weather Forecasts -->
                        <div class="card card-hover p-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-6 bg-primary/10 rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-foreground mb-3">Weather Forecasts</h3>
                            <p class="text-foreground-muted">Check real-time weather and 7-day forecasts for any destination before you travel.</p>
                        </div>

                        <!-- Smart Packing -->
                        <div class="card card-hover p-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-6 bg-accent/10 rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-foreground mb-3">Smart Packing Lists</h3>
                            <p class="text-foreground-muted">Auto-generated packing checklists based on your destination, weather, and trip type.</p>
                        </div>

                        <!-- Destination Discovery -->
                        <div class="card card-hover p-8 text-center">
                            <div class="w-16 h-16 mx-auto mb-6 bg-emerald-500/10 rounded-2xl flex items-center justify-center">
                                <svg class="w-8 h-8 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-foreground mb-3">Discover Destinations</h3>
                            <p class="text-foreground-muted">Explore countries, learn about cultures, holidays, and find your next adventure.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="py-8 px-6 bg-surface border-t border-border">
                <div class="max-w-7xl mx-auto text-center">
                    <p class="text-sm text-foreground-subtle">
                        &copy; {{ date('Y') }} Smart Travel Planner. Built with Laravel.
                    </p>
                </div>
            </footer>
        </div>
    </body>
</html>
