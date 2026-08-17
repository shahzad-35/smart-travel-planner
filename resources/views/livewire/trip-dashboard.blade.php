<div class="w-full">
    @php
        $hour = (int) now()->format('H');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
        $firstName = explode(' ', trim(auth()->user()->name))[0];
        $nextTrip = $upcomingTrips->first();
        $readiness = $this->nextTripReadiness;
    @endphp

    <!-- Greeting header -->
    <div class="mb-8 fade-up">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="eyebrow mb-1.5">{{ now()->format('l, F j') }}</p>
                <h1 class="page-title">{{ $greeting }}, {{ $firstName }}</h1>
            </div>
            <div class="flex space-x-3">
                <button
                    wire:click="searchDestinations"
                    class="btn-ghost px-4 py-2.5"
                >
                    <svg class="w-5 h-5 text-foreground-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search Destinations
                </button>
                <button
                    wire:click="createTrip"
                    class="btn-primary px-4 py-2.5"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Plan New Trip
                </button>
            </div>
        </div>
    </div>

    @if($nextTrip)
        <!-- Bento: next adventure + readiness -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Next adventure banner (spans 2) -->
            <a href="{{ route('trips.show', $nextTrip->id) }}" wire:navigate
                class="lg:col-span-2 relative block overflow-hidden rounded-2xl group border border-border shadow-card hover:shadow-card-hover transition-shadow duration-200 min-h-[220px]">
                @if($nextTrip->country_code)
                    <img src="https://flagcdn.com/w1280/{{ strtolower($nextTrip->country_code) }}.png" alt="" aria-hidden="true"
                        class="absolute inset-0 w-full h-full object-cover scale-105 brightness-[0.65] saturate-[1.1] group-hover:scale-110 transition-transform duration-700">
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-primary to-primary-dark"></div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent" aria-hidden="true"></div>
                <div class="relative h-full px-6 py-7 sm:px-9 flex flex-col justify-between gap-5">
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-white/70">Next adventure</p>
                    <div>
                        <div class="flex items-center gap-4">
                            @if($nextTrip->country_code)
                                <img src="https://flagcdn.com/{{ strtolower($nextTrip->country_code) }}.svg" alt="{{ $nextTrip->country_code }} flag"
                                    class="w-12 h-9 object-cover rounded-lg shadow-lg ring-1 ring-white/30">
                            @endif
                            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight drop-shadow-sm">{{ $nextTrip->destination }}</h2>
                        </div>
                        <p class="mt-2.5 text-white/80 font-medium">
                            {{ $nextTrip->start_date->format('M j') }} – {{ $nextTrip->end_date->format('M j, Y') }}
                            · {{ ucfirst($nextTrip->type) }}
                            · {{ $nextTrip->travelers }} {{ Str::plural('traveler', $nextTrip->travelers) }}
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        @php($daysLeft = intval(now()->startOfDay()->diffInDays($nextTrip->start_date, false)))
                        @if($daysLeft > 0)
                            <div class="flex items-baseline gap-2 text-white">
                                <span class="text-4xl font-extrabold tabular-nums drop-shadow-sm" data-countup="{{ $daysLeft }}">{{ $daysLeft }}</span>
                                <span class="text-white/75 font-semibold">{{ Str::plural('day', $daysLeft) }} to go</span>
                            </div>
                        @else
                            <span class="chip bg-white/20 text-white backdrop-blur-sm">Departed</span>
                        @endif
                        <span class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/15 backdrop-blur-sm border border-white/25 text-white font-semibold group-hover:bg-white/25 transition-colors">
                            Open trip
                            <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </div>
            </a>

            <!-- Trip readiness -->
            <div class="card p-6 flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="section-title text-lg">Trip readiness</h3>
                    <x-ui.chip color="{{ ($readiness['percent'] ?? 0) >= 100 ? 'primary' : 'accent' }}">
                        {{ $readiness['percent'] ?? 0 }}%
                    </x-ui.chip>
                </div>

                <div class="flex-1 flex items-center justify-center py-2">
                    @php($pct = $readiness['percent'] ?? 0)
                    @php($dash = round(2 * M_PI * 42, 1))
                    <div class="relative w-36 h-36">
                        <svg class="w-36 h-36 -rotate-90" viewBox="0 0 100 100" role="img" aria-label="Packing {{ $pct }} percent complete">
                            <circle cx="50" cy="50" r="42" fill="none" stroke="currentColor" stroke-width="9" class="text-surface-muted dark:text-surface"/>
                            <circle cx="50" cy="50" r="42" fill="none" stroke="url(#readinessGradient)" stroke-width="9" stroke-linecap="round"
                                stroke-dasharray="{{ $dash }}" stroke-dashoffset="{{ round($dash * (1 - $pct / 100), 1) }}"
                                style="transition: stroke-dashoffset 900ms cubic-bezier(0.34, 1, 0.64, 1)"/>
                            <defs>
                                <linearGradient id="readinessGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color: var(--color-primary)"/>
                                    <stop offset="100%" style="stop-color: var(--color-primary-light)"/>
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-3xl font-extrabold text-foreground tabular-nums">{{ $pct }}%</span>
                            <span class="text-xs text-foreground-subtle font-medium">packed</span>
                        </div>
                    </div>
                </div>

                <p class="text-sm text-foreground-muted text-center mb-4">
                    @if(($readiness['total'] ?? 0) > 0)
                        <span class="font-semibold text-foreground tabular-nums">{{ $readiness['packed'] }}</span> of
                        <span class="font-semibold text-foreground tabular-nums">{{ $readiness['total'] }}</span> items packed for {{ $nextTrip->destination }}
                    @else
                        Your packing list is being prepared…
                    @endif
                </p>
                <a href="{{ route('packing-checklist.show', $nextTrip->id) }}" wire:navigate class="btn-ghost w-full text-sm">
                    Open checklist
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>
    @endif

    <!-- Travel Statistics (lazy, skeleton placeholder) -->
    <div class="mb-8">
        <livewire:travel-stats lazy />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Upcoming Trips (spans 2) -->
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <h2 class="section-title">Upcoming Trips</h2>
                    @if($stats['upcoming_count'] > 0)
                        <x-ui.chip color="accent">{{ $stats['upcoming_count'] }}</x-ui.chip>
                    @endif
                </div>
                <a href="{{ route('trips.listing') }}" wire:navigate class="inline-flex items-center gap-1 text-primary hover:text-primary-dark text-sm font-semibold transition-colors">
                    View all
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            @if($upcomingTrips->count() > 0)
                <div class="space-y-3">
                    @foreach($upcomingTrips as $trip)
                        <a href="{{ route('trips.show', $trip->id) }}" wire:navigate wire:key="upcoming-{{ $trip->id }}"
                            class="list-row group cursor-pointer">
                            <div class="flex-shrink-0">
                                @if($trip->country_code)
                                    <img src="https://flagcdn.com/{{ strtolower($trip->country_code) }}.svg"
                                        alt="{{ $trip->country_code }} flag"
                                        class="w-12 h-9 object-cover rounded-lg shadow-sm" loading="lazy">
                                @else
                                    <div class="w-12 h-9 bg-primary/10 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-3">
                                    <h3 class="text-base font-bold text-foreground group-hover:text-primary transition-colors truncate">{{ $trip->destination }}</h3>
                                    <x-status-badge :status="$trip->status" />
                                </div>
                                <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-1 text-sm text-foreground-muted">
                                    <span class="whitespace-nowrap">{{ $trip->start_date->format('M j') }} – {{ $trip->end_date->format('M j, Y') }}</span>
                                    @php($d = intval(now()->startOfDay()->diffInDays($trip->start_date, false)))
                                    @if($d > 0)
                                        <span class="text-foreground-subtle">in {{ $d }} {{ Str::plural('day', $d) }}</span>
                                    @endif
                                </div>
                            </div>
                            <svg class="w-5 h-5 text-foreground-subtle group-hover:text-primary group-hover:translate-x-0.5 transition-all self-center shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endforeach
                </div>
            @else
                <x-ui.empty-state title="No upcoming trips"
                    description="Your next adventure is a few clicks away — pick a destination and we'll handle the weather, holidays, and packing list.">
                    <x-slot name="icon">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </x-slot>
                    <button wire:click="createTrip" class="btn-primary px-5 py-2.5">
                        Plan Your First Trip
                    </button>
                </x-ui.empty-state>
            @endif
        </div>

        <!-- Quick actions (stacked) -->
        <div class="space-y-6 stagger-grid">
            <a href="{{ route('weather') }}" wire:navigate class="relative overflow-hidden card card-hover p-6 group cursor-pointer block">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/10 via-transparent to-transparent" aria-hidden="true"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-12 h-12 shrink-0 bg-gradient-to-br from-primary to-primary-dark rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-foreground group-hover:text-primary transition-colors">Check Weather</h3>
                        <p class="text-sm text-foreground-muted">Forecasts for your destinations</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('country.info') }}" wire:navigate class="relative overflow-hidden card card-hover p-6 group cursor-pointer block">
                <div class="absolute inset-0 bg-gradient-to-br from-accent/10 via-transparent to-transparent" aria-hidden="true"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-12 h-12 shrink-0 bg-gradient-to-br from-accent to-accent-light rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-accent-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-foreground group-hover:text-accent transition-colors">Holiday Calendar</h3>
                        <p class="text-sm text-foreground-muted">Festivals and public holidays</p>
                    </div>
                </div>
            </a>

            <a href="{{ route('destinations') }}" wire:navigate class="relative overflow-hidden card card-hover p-6 group cursor-pointer block">
                <div class="absolute inset-0 bg-gradient-to-br from-secondary/10 via-transparent to-transparent" aria-hidden="true"></div>
                <div class="relative flex items-center gap-4">
                    <div class="w-12 h-12 shrink-0 bg-gradient-to-br from-secondary to-primary rounded-xl flex items-center justify-center shadow-lg group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6 text-secondary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-foreground group-hover:text-secondary transition-colors">Discover Destinations</h3>
                        <p class="text-sm text-foreground-muted">250 countries · 153k cities</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
