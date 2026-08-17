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
<a href="#main-content" class="skip-link">Skip to content</a>
<div class="min-h-screen">
    <livewire:layout.navigation/>

    <div class="lg:pl-64 flex flex-col min-h-[calc(100vh-4rem)]">
        @if (isset($header))
            <header class="bg-surface-card shadow-card border-b border-border">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    {{ $header }}
                </div>
            </header>
        @endif

        <main id="main-content" class="flex-1 py-8 px-4 sm:px-6 lg:px-8 pb-24 lg:pb-8">
            {{ $slot }}
        </main>
    </div>

    <x-ui.toast-hub />
</div>
<script>
    // Stagger grids: reveal children with indexed delays when they enter the
    // viewport. Grids are "armed" (hidden) only here, so content that arrives
    // before/without JS stays visible. Re-runs after every Livewire commit so
    // lazily-loaded components get picked up too.
    (() => {
        const boot = () => {
            document.querySelectorAll('.stagger-grid:not([data-armed])').forEach((grid) => {
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                [...grid.children].forEach((child, i) => child.style.setProperty('--stagger-i', i));
                if (!('IntersectionObserver' in window)) return;
                grid.setAttribute('data-armed', '');
                const io = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (entry.isIntersecting) { entry.target.classList.add('in-view'); io.unobserve(entry.target); }
                    });
                }, { threshold: 0.1 });
                io.observe(grid);
            });

            // Count-up numbers: animate [data-countup] once when visible
            document.querySelectorAll('[data-countup]:not([data-counted])').forEach((el) => {
                el.setAttribute('data-counted', '');
                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
                const target = parseFloat(el.getAttribute('data-countup'));
                if (!isFinite(target) || target <= 0) return;
                const io = new IntersectionObserver((entries) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) return;
                        io.unobserve(el);
                        const start = performance.now(), dur = 900;
                        const step = (now) => {
                            const p = Math.min((now - start) / dur, 1);
                            const eased = 1 - Math.pow(1 - p, 3);
                            el.textContent = Math.round(target * eased).toLocaleString();
                            if (p < 1) requestAnimationFrame(step);
                        };
                        requestAnimationFrame(step);
                    });
                }, { threshold: 0.4 });
                io.observe(el);
            });
        };
        document.addEventListener('livewire:init', () => {
            boot();
            if (window.Livewire?.hook) {
                Livewire.hook('commit', ({ succeed }) => succeed(() => setTimeout(boot, 60)));
            }
        });
        document.addEventListener('livewire:navigated', boot);
        boot();
    })();
</script>
@stack('scripts')
@livewireScripts
</body>
</html>
