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

        <style>
            /* ===== Morph hero (ported from 21st.dev "Scroll Morph Hero") ===== */
            #mhCards { perspective: 1000px; }
            .mh-card {
                position: absolute;
                left: 50%;
                top: 50%;
                width: 64px;
                height: 90px;
                margin-left: -32px;
                margin-top: -45px;
                will-change: transform, opacity;
                cursor: pointer;
            }
            .mh-flip {
                position: relative;
                width: 100%;
                height: 100%;
                transform-style: preserve-3d;
                transition: transform 0.6s cubic-bezier(0.2, 0.8, 0.3, 1);
            }
            .mh-card:hover .mh-flip { transform: rotateY(180deg); }
            .mh-face {
                position: absolute;
                inset: 0;
                border-radius: 0.75rem;
                overflow: hidden;
                backface-visibility: hidden;
                box-shadow: 0 8px 20px -6px rgba(0,0,0,0.35);
            }
            .mh-face img { width: 100%; height: 100%; object-fit: cover; display: block; }
            .mh-face--front::after {
                content: '';
                position: absolute; inset: 0;
                background: rgba(0,0,0,0.12);
                transition: background 0.3s;
            }
            .mh-card:hover .mh-face--front::after { background: transparent; }
            .mh-face--back {
                transform: rotateY(180deg);
                background: var(--color-card);
                border: 1px solid var(--color-border-strong);
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                padding: 6px;
            }
            .mh-back-label { font-size: 10px; font-weight: 700; color: var(--color-primary); line-height: 1.25; }

            #mhIntro, #mhArcContent { transition: none; will-change: opacity, transform; }
            .mh-noevents { pointer-events: none !important; }

            /* Reduced motion / no-JS fallback: simple static strip */
            .mh-static #mhCards { display: none; }
            .mh-static #mhIntro { opacity: 1 !important; transform: none !important; }

            /* Mobile: text sits above a self-rotating bottom arc */
            @media (max-width: 767px) {
                #mhIntro { justify-content: flex-start; padding-top: 2.5rem; }
                #mhHint { display: none; }
            }

            /* ===== Destination marquee ===== */
            .marquee {
                overflow: hidden;
                -webkit-mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
                mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
            }
            .marquee-track {
                display: flex;
                gap: 1rem;
                width: max-content;
                animation: marquee-scroll 45s linear infinite;
            }
            .marquee:hover .marquee-track { animation-play-state: paused; }
            @keyframes marquee-scroll {
                from { transform: translateX(0); }
                to   { transform: translateX(-50%); }
            }
            @media (prefers-reduced-motion: reduce) {
                .marquee-track { animation: none; }
                .marquee { -webkit-mask-image: none; mask-image: none; overflow-x: auto; }
            }

            /* ===== Scroll reveal ===== */
            .reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.7s ease-out, transform 0.7s ease-out; }
            .reveal.revealed { opacity: 1; transform: none; }
            @media (prefers-reduced-motion: reduce) {
                .reveal { opacity: 1; transform: none; transition: none; }
            }
        </style>
    </head>
    <body class="antialiased font-sans bg-surface text-foreground theme-transition">
        <div class="min-h-screen flex flex-col">
            <!-- Navigation -->
            <nav class="relative z-20 px-6 py-4 bg-surface/80 backdrop-blur-md">
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

            @php
                $heroCards = [
                    ['img' => 'h1.jpg',  'name' => 'Paris'],
                    ['img' => 'h2.jpg',  'name' => 'Yosemite'],
                    ['img' => 'h3.jpg',  'name' => 'Highlands'],
                    ['img' => 'h4.jpg',  'name' => 'Black Forest'],
                    ['img' => 'h5.jpg',  'name' => 'Ireland'],
                    ['img' => 'h6.jpg',  'name' => 'Amalfi Coast'],
                    ['img' => 'h7.jpg',  'name' => 'Stonehenge'],
                    ['img' => 'h8.jpg',  'name' => 'Dubai'],
                    ['img' => 'h9.jpg',  'name' => 'Tuscany'],
                    ['img' => 'h10.jpg', 'name' => 'Serengeti'],
                    ['img' => 'h11.jpg', 'name' => 'Banff'],
                    ['img' => 'h12.jpg', 'name' => 'Bavaria'],
                    ['img' => 'h13.jpg', 'name' => 'Rio de Janeiro'],
                    ['img' => 'h14.jpg', 'name' => 'Borneo'],
                    ['img' => 'h15.jpg', 'name' => 'Kyoto'],
                    ['img' => 'h16.jpg', 'name' => 'Shanghai'],
                ];
            @endphp

            <!-- ===== Morph Hero ===== -->
            <section id="morphHero" class="relative bg-surface overflow-hidden select-none"
                style="height: calc(100vh - 4.5rem); min-height: 640px;">

                <!-- Cards -->
                <div id="mhCards" class="absolute inset-0 z-0">
                    @foreach($heroCards as $i => $card)
                        <div class="mh-card" data-i="{{ $i }}" style="opacity: 0">
                            <div class="mh-flip">
                                <div class="mh-face mh-face--front">
                                    <img src="{{ asset('images/hero/' . $card['img']) }}" alt="{{ $card['name'] }}" loading="eager" draggable="false">
                                </div>
                                <div class="mh-face mh-face--back">
                                    <span class="mh-back-label">{{ $card['name'] }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Center intro content (circle phase) -->
                <div id="mhIntro" class="absolute inset-0 z-10 flex flex-col items-center justify-center text-center px-6">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-primary/10 text-primary border border-primary/20">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Weather-aware trip planning
                    </span>
                    <h1 class="mt-5 text-4xl sm:text-5xl lg:text-6xl font-extrabold text-foreground leading-[1.08] tracking-tight max-w-2xl" style="text-wrap: balance">
                        Plan your
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary via-primary-light to-accent">perfect journey</span>
                    </h1>
                    <p id="mhSubtitle" class="mt-5 text-lg sm:text-xl text-foreground-muted leading-relaxed max-w-lg">
                        Organize trips, track forecasts, discover destinations, and pack smart — all in one place.
                    </p>
                    {{-- md:mt-36 opens a band between the copy and the CTAs where
                         the intro filmstrip parades through (desktop only). --}}
                    <div id="mhCtas" class="mt-8 md:mt-32 flex flex-col sm:flex-row gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-primary text-primary-foreground rounded-xl font-semibold text-lg hover:bg-primary-dark transition-colors shadow-lg shadow-primary/25 cursor-pointer focus-ring">
                                Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-primary text-primary-foreground rounded-xl font-semibold text-lg hover:bg-primary-dark transition-colors shadow-lg shadow-primary/25 cursor-pointer focus-ring">
                                Get Started Free
                            </a>
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-surface-card/70 text-foreground border border-border-strong rounded-xl font-semibold text-lg hover:bg-surface-card transition-colors backdrop-blur-sm cursor-pointer focus-ring">
                                Sign In
                            </a>
                        @endauth
                    </div>
                    <p id="mhHint" class="mt-10 text-[11px] font-bold tracking-[0.25em] text-foreground-subtle uppercase">Scroll to explore</p>
                </div>

                <!-- Arc-phase content (fades in after morph) -->
                <div id="mhArcContent" class="mh-noevents absolute top-[8%] inset-x-0 z-10 flex flex-col items-center text-center px-6" style="opacity: 0">
                    <h2 class="text-3xl md:text-5xl font-extrabold text-foreground tracking-tight mb-4" style="text-wrap: balance">
                        The world, <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-accent">organized</span>
                    </h2>
                    <p class="text-sm md:text-base text-foreground-muted max-w-lg leading-relaxed mb-6">
                        250 countries · 153,000 cities · live forecasts and holidays for every trip window — with a packing list that writes itself.
                    </p>
                    <a href="{{ Route::has('register') && !auth()->check() ? route('register') : route('dashboard') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-primary-foreground rounded-xl font-semibold hover:bg-primary-dark transition-colors shadow-lg shadow-primary/25 cursor-pointer">
                        Start planning
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                    </a>
                </div>
            </section>

            <!-- ===== Destination marquee ===== -->
            <section class="py-10 bg-surface-card dark:bg-surface border-y border-border overflow-hidden">
                <div class="marquee" aria-label="Popular destinations">
                    <div class="marquee-track">
                        @foreach(array_merge($heroCards, $heroCards) as $card)
                            <div class="flex items-center gap-3 shrink-0 pl-2 pr-5 py-2 rounded-full bg-surface dark:bg-surface-muted border border-border">
                                <img src="{{ asset('images/hero/' . $card['img']) }}" alt="" aria-hidden="true"
                                    class="w-9 h-9 rounded-full object-cover" loading="lazy" draggable="false">
                                <span class="text-sm font-semibold text-foreground whitespace-nowrap">{{ $card['name'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="py-20 px-6 bg-surface-card dark:bg-surface">
                <div class="max-w-7xl mx-auto">
                    <div class="text-center mb-14 reveal">
                        <h2 class="text-3xl sm:text-4xl font-bold text-foreground tracking-tight">Everything you need to travel smart</h2>
                        <p class="mt-4 text-lg text-foreground-muted max-w-2xl mx-auto">Plan, organize, and enjoy your trips with powerful tools designed for modern travelers.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Weather Forecasts -->
                        <div class="card card-hover p-8 group reveal">
                            <div class="w-12 h-12 mb-5 bg-gradient-to-br from-primary to-primary-dark rounded-xl flex items-center justify-center shadow-lg shadow-primary/20 group-hover:scale-105 transition-transform duration-200">
                                <svg class="w-6 h-6 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-foreground mb-2">Weather Forecasts</h3>
                            <p class="text-foreground-muted leading-relaxed">Real-time conditions and multi-day forecasts for any destination — compared side by side, in your preferred unit.</p>
                        </div>

                        <!-- Smart Packing -->
                        <div class="card card-hover p-8 group reveal" style="transition-delay: 90ms">
                            <div class="w-12 h-12 mb-5 bg-gradient-to-br from-accent to-accent-light rounded-xl flex items-center justify-center shadow-lg shadow-accent/20 group-hover:scale-105 transition-transform duration-200">
                                <svg class="w-6 h-6 text-accent-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-foreground mb-2">Smart Packing Lists</h3>
                            <p class="text-foreground-muted leading-relaxed">Checklists generated automatically from your destination's forecast — shareable with anyone, no account needed.</p>
                        </div>

                        <!-- Destination Discovery -->
                        <div class="card card-hover p-8 group reveal" style="transition-delay: 180ms">
                            <div class="w-12 h-12 mb-5 bg-gradient-to-br from-secondary to-primary rounded-xl flex items-center justify-center shadow-lg shadow-secondary/20 group-hover:scale-105 transition-transform duration-200">
                                <svg class="w-6 h-6 text-secondary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/>
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-foreground mb-2">Discover Destinations</h3>
                            <p class="text-foreground-muted leading-relaxed">Search countries, provinces, and 153,000 cities. Explore cultures, currencies, and public holidays.</p>
                        </div>
                    </div>

                    <!-- Screenshot showcase -->
                    <div class="mt-16 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="rounded-2xl border border-border shadow-card overflow-hidden bg-surface-card reveal">
                            <img src="{{ asset('images/showcase/screenshot-destination-search.png') }}"
                                alt="Destination search with a province expanded into its major cities"
                                class="w-full" loading="lazy">
                            <p class="px-5 py-3 text-sm text-foreground-muted border-t border-border">Search any level — provinces expand into their major cities.</p>
                        </div>
                        <div class="rounded-2xl border border-border shadow-card overflow-hidden bg-surface-card reveal" style="transition-delay: 120ms">
                            <img src="{{ asset('images/showcase/screenshot-trip-wizard-search.png') }}"
                                alt="Trip creation wizard with destination search"
                                class="w-full" loading="lazy">
                            <p class="px-5 py-3 text-sm text-foreground-muted border-t border-border">A four-step wizard with live weather previews for your destination.</p>
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

        <script>
        (() => {
            'use strict';
            const hero = document.getElementById('morphHero');
            if (!hero) return;

            const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const cards = [...hero.querySelectorAll('.mh-card')];
            const intro = document.getElementById('mhIntro');
            const arcContent = document.getElementById('mhArcContent');
            const headlineEl = intro?.querySelector('h1');
            const subtitleEl = document.getElementById('mhSubtitle');
            const ctasEl = document.getElementById('mhCtas');
            const TOTAL = cards.length;
            const MAX_SCROLL = 2200;
            const MORPH_END = 600;

            if (reduced || TOTAL === 0) {
                hero.classList.add('mh-static');
                hero.style.height = 'auto';
                hero.style.minHeight = '0';
                intro.style.position = 'relative';
                intro.style.padding = '5rem 1.5rem';
                return;
            }

            // --- state ---
            const mobileMode = window.matchMedia('(max-width: 767px)').matches;
            let phase = 'scatter';            // scatter -> line -> circle
            let virtual = 0;                  // virtual scroll 0..MAX_SCROLL
            let morphS = 0, rotateS = 0, parallaxS = 0; // smoothed values
            let W = hero.offsetWidth, H = hero.offsetHeight;
            let parallaxTarget = 0;

            // per-card eased state
            const st = cards.map(() => ({
                x: (Math.random() - 0.5) * 1500,
                y: (Math.random() - 0.5) * 1000,
                r: (Math.random() - 0.5) * 180,
                s: 0.6,
                o: 0,
            }));

            // --- intro geometry, measured from the real layout ---
            // bandY: the gap between the subtitle and the CTAs, where the intro
            // filmstrip parades. ringRX/ringRY: a TRUE circle big enough to clear
            // the copy; it only stretches into an ellipse when the hero is too
            // short for a circle that both clears the text and stays in frame.
            let bandY = 0, ringRX = 320, ringRY = 320, safeBoxes = [];

            // Per-LINE boxes of an element (a centred headline is much
            // narrower on its short lines than its block width suggests).
            const lineRects = (el) => {
                if (!el) return [];
                try {
                    const range = document.createRange();
                    range.selectNodeContents(el);
                    const rects = [...range.getClientRects()].filter((r) => r.width > 1 && r.height > 1);
                    if (rects.length) return rects;
                } catch (e) { /* fall through */ }
                return [el.getBoundingClientRect()];
            };

            // Ring cards are rotated, so clearance uses their circumradius
            // (half-diagonal), not half width/height.
            const CIRCLE_SCALE = 0.8;
            const CARD_R = Math.hypot(32 * CIRCLE_SCALE, 45 * CIRCLE_SCALE) + 8;

            const measureIntro = () => {
                const hr = hero.getBoundingClientRect();
                const cy = hr.top + hr.height / 2;

                const sub = lineRects(subtitleEl);
                const cta = ctasEl ? [ctasEl.getBoundingClientRect()] : [];
                const subLast = sub[sub.length - 1];
                bandY = (subLast && cta[0]) ? ((subLast.bottom + cta[0].top) / 2) - cy : hr.height / 2 - 70;

                // Smallest circle whose cards clear every line of copy: for each
                // line box, the card must sit outside its inflated corner.
                let need = 0;
                [...lineRects(headlineEl), ...sub, ...cta].forEach((r) => {
                    const a = r.width / 2 + CARD_R;
                    const y = Math.max(Math.abs(r.top - cy), Math.abs(r.bottom - cy)) + CARD_R;
                    need = Math.max(need, Math.hypot(a, y));
                });

                // A TRUE circle (rx === ry), capped to stay inside the hero.
                const cap = Math.min(hr.width / 2 - 40, hr.height / 2 - 10);
                ringRX = ringRY = Math.min(Math.max(need, 240), cap);
                // Keep-out boxes (one per line of copy, inflated by the card's
                // circumradius) used as a hard render-time guarantee below.
                safeBoxes = [...lineRects(headlineEl), ...sub, ...cta].map((r) => ({
                    halfW: r.width / 2,
                    y1: r.top - cy,
                    y2: r.bottom - cy,
                }));
                hero.dataset.band = Math.round(bandY);
                hero.dataset.ring = Math.round(ringRX);
            };

            const heroRO = new ResizeObserver((entries) => {
                for (const e of entries) {
                    if (e.target === hero) { W = e.contentRect.width; H = e.contentRect.height; }
                }
                measureIntro();
            });
            heroRO.observe(hero);
            // Also track the copy itself: fonts, wrapping and CSS arriving late
            // all move these lines, and the keep-out boxes must follow them.
            [headlineEl, subtitleEl, ctasEl].forEach((el) => el && heroRO.observe(el));
            measureIntro();
            window.addEventListener('load', measureIntro);
            if (document.fonts?.ready) document.fonts.ready.then(measureIntro);
            // Late layout shifts (web-font swap, images) move the copy after the
            // observers have already fired once — re-measure briefly to be sure.
            const settleTimer = setInterval(measureIntro, 200);
            setTimeout(() => clearInterval(settleTimer), 5000);

            // --- intro sequence (scatter -> line -> circle) ---
            let phaseSince = 0;
            setTimeout(() => { phase = 'line'; phaseSince = performance.now(); }, 500);
            setTimeout(() => { phase = 'circle'; phaseSince = performance.now(); }, 3000);

            // --- virtual scroll: hijack only while the hero dominates the viewport
            //     (its top may sit below the fixed nav), release at both ends so
            //     the page keeps scrolling naturally ---
            const heroInView = () => {
                const r = hero.getBoundingClientRect();
                return r.top > -64 && r.top < 160;
            };

            hero.addEventListener('wheel', (e) => {
                if (mobileMode || phase !== 'circle' || !heroInView()) return;
                const dy = e.deltaY;
                if ((virtual >= MAX_SCROLL && dy > 0) || (virtual <= 0 && dy < 0)) return; // release
                e.preventDefault();
                virtual = Math.min(Math.max(virtual + dy, 0), MAX_SCROLL);
            }, { passive: false });

            let touchY = 0;
            hero.addEventListener('touchstart', (e) => { touchY = e.touches[0].clientY; }, { passive: true });
            hero.addEventListener('touchmove', (e) => {
                if (mobileMode || phase !== 'circle' || !heroInView()) return;
                const y = e.touches[0].clientY;
                const dy = touchY - y;
                touchY = y;
                if ((virtual >= MAX_SCROLL && dy > 0) || (virtual <= 0 && dy < 0)) return;
                e.preventDefault();
                virtual = Math.min(Math.max(virtual + dy * 2, 0), MAX_SCROLL);
            }, { passive: false });

            hero.addEventListener('mousemove', (e) => {
                const rect = hero.getBoundingClientRect();
                const nx = ((e.clientX - rect.left) / rect.width) * 2 - 1;
                parallaxTarget = nx * 100;
            });

            // Returning to the very top of the page (scrollbar, keyboard,
            // momentum — paths the hero's own wheel handler never sees)
            // unwinds the morph so the cards settle back into the original
            // circle. During the wheel-hijack the page is pinned at 0 and no
            // scroll events fire, so this cannot fight the forward animation.
            let lastY = window.scrollY;
            window.addEventListener('scroll', () => {
                const y = window.scrollY;
                if (y <= 2 && lastY > 2 && !mobileMode && phase === 'circle') {
                    virtual = 0; // smoothing lerps the cards home
                }
                lastY = y;
            }, { passive: true });

            const lerp = (a, b, t) => a * (1 - t) + b * t;

            // --- render loop ---
            const tick = () => {
                // Mobile: no scroll hijack — cards settle into the bottom arc and
                // drift back and forth on their own; the intro text stays put.
                const morphTarget = mobileMode
                    ? (phase === 'circle' ? 1 : 0)
                    : Math.min(Math.max(virtual / MORPH_END, 0), 1);
                const scrollProgress = mobileMode
                    ? 0.5 + Math.sin(performance.now() / 5000) * 0.4
                    : Math.min(Math.max((virtual - MORPH_END) / (MAX_SCROLL - MORPH_END), 0), 1);
                morphS += (morphTarget - morphS) * 0.07;
                rotateS += (scrollProgress - rotateS) * 0.07;
                parallaxS += (parallaxTarget - parallaxS) * 0.06;

                const isMobile = W < 768;
                // Ring radii come from measureIntro() — a true circle whenever
                // the hero is tall enough to fit one that clears the copy.
                const circleRadiusX = ringRX;
                const circleRadiusY = ringRY;
                const baseRadius = Math.min(W, H * 1.5);
                const arcRadius = baseRadius * (isMobile ? 1.4 : 1.1);
                // Apex low enough that the arc (cards scaled 1.8, ~81px half-
                // height) clears the arc-phase headline/CTA block, which ends
                // around 35% of the hero height.
                const arcApexY = H * (isMobile ? 0.72 : 0.55) - H / 2; // relative to center
                const arcCenterY = arcApexY + arcRadius;
                const spread = isMobile ? 100 : 130;
                const startAngle = -90 - spread / 2;
                const step = spread / (TOTAL - 1);
                // Desktop sweeps one way with scroll; mobile oscillates around center
                const boundedRotation = isMobile
                    ? (rotateS - 0.5) * spread * 0.5
                    : -rotateS * spread * 0.8;

                const introReadable = mobileMode || morphS < 0.5;

                for (let i = 0; i < TOTAL; i++) {
                    const s = st[i];
                    let tx, ty, tr, tsc, top;

                    // Cross-dissolve through each formation change: cards fade
                    // out, travel, and fade back in — they never sweep visibly
                    // across the headline, subtitle or buttons.
                    const settling = phaseSince && (performance.now() - phaseSince) < 380;

                    if (phase === 'scatter') {
                        tx = s.x; ty = s.y; tr = s.r; tsc = 0.6; top = 0;
                        // keep scatter targets as-is (cards stay hidden/scattered)
                    } else if (phase === 'line') {
                        const spacing = Math.min(74, (W - 80) / TOTAL);
                        tx = i * spacing - (TOTAL * spacing) / 2;
                        // Filmstrip parades through the band between the
                        // subtitle and the CTAs, then opens into the ring.
                        ty = bandY; tr = 0; tsc = 0.72; top = settling ? 0 : 1;
                    } else {
                        // circle position
                        const cAng = (i / TOTAL) * 360;
                        const cRad = (cAng * Math.PI) / 180;
                        const cx = Math.cos(cRad) * circleRadiusX;
                        const cy = Math.sin(cRad) * circleRadiusY;
                        const crot = cAng + 90;
                        // arc position
                        const aAng = startAngle + i * step + boundedRotation;
                        const aRad = (aAng * Math.PI) / 180;
                        const ax = Math.cos(aRad) * arcRadius + parallaxS;
                        const ay = Math.sin(aRad) * arcRadius + arcCenterY;
                        const arot = aAng + 90;
                        const asc = isMobile ? 1.4 : 1.8;

                        tx = lerp(cx, ax, morphS);
                        ty = lerp(cy, ay, morphS);
                        tr = lerp(crot, arot, morphS);
                        tsc = lerp(CIRCLE_SCALE, asc, morphS);
                        top = (settling && morphS < 0.05) ? 0 : 1;
                    }

                    // ease toward target (spring-ish)
                    s.x += (tx - s.x) * 0.085;
                    s.y += (ty - s.y) * 0.085;
                    s.r += (tr - s.r) * 0.085;
                    s.s += (tsc - s.s) * 0.085;
                    s.o += (top - s.o) * 0.32;   // opacity snaps faster than motion,
                                                 // so the dissolve hides the travel

                    // Hard guarantee: while the intro copy is on screen, a card
                    // is never painted over a line of it — nudged out by the
                    // shortest distance if a tween would take it there.
                    let px = s.x, py = s.y;
                    if (introReadable && safeBoxes.length) {
                        // The card's real footprint at its current scale AND
                        // rotation (a rotated card covers more than 64x90).
                        const rad = (s.r * Math.PI) / 180;
                        const ac = Math.abs(Math.cos(rad)), as = Math.abs(Math.sin(rad));
                        const ex = (ac * 32 + as * 45) * s.s + 14;
                        const ey = (as * 32 + ac * 45) * s.s + 14;
                        for (let pass = 0; pass < 2; pass++) {
                            for (const b of safeBoxes) {
                                if (Math.abs(px) < b.halfW + ex && py > b.y1 - ey && py < b.y2 + ey) {
                                    const dx = (b.halfW + ex) - Math.abs(px);
                                    const dUp = py - (b.y1 - ey), dDown = (b.y2 + ey) - py;
                                    const dy = Math.min(dUp, dDown);
                                    if (dx <= dy) px += (px < 0 ? -dx : dx);
                                    else py += (dUp < dDown) ? -dy : dy;
                                }
                            }
                        }
                    }

                    cards[i].style.transform = `translate(${px}px, ${py}px) rotate(${s.r}deg) scale(${s.s})`;
                    cards[i].style.opacity = s.o.toFixed(3);
                }

                // content cross-fade (desktop only — mobile keeps the intro text)
                if (!mobileMode) {
                    const introOp = Math.max(0, 1 - morphS * 2);
                    intro.style.opacity = introOp.toFixed(3);
                    intro.style.transform = `translateY(${-morphS * 30}px)`;
                    intro.classList.toggle('mh-noevents', introOp < 0.35);

                    const arcOp = Math.min(Math.max((morphS - 0.8) / 0.2, 0), 1);
                    arcContent.style.opacity = arcOp.toFixed(3);
                    arcContent.style.transform = `translateY(${(1 - arcOp) * 20}px)`;
                    arcContent.classList.toggle('mh-noevents', arcOp < 0.5);
                }

                requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
        })();

        // Scroll-triggered reveals
        (() => {
            const els = document.querySelectorAll('.reveal');
            if (!('IntersectionObserver' in window)) {
                els.forEach((el) => el.classList.add('revealed'));
                return;
            }
            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });
            els.forEach((el) => io.observe(el));
        })();
        </script>
    </body>
</html>
