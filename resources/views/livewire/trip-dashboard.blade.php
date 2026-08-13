<div class="w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-foreground mb-2">Travel Dashboard</h1>
                <p class="text-foreground-muted">Overview of your travel adventures and upcoming trips</p>
            </div>
            <div class="flex space-x-3">
                <button
                    wire:click="searchDestinations"
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 dark:bg-emerald-500 text-white rounded-lg hover:bg-emerald-700 dark:hover:bg-emerald-600 font-medium transition-colors cursor-pointer"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search Destinations
                </button>
                <button
                    wire:click="createTrip"
                    class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary-dark font-medium transition-colors cursor-pointer"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Plan New Trip
                </button>
            </div>
        </div>
    </div>

    <!-- Travel Statistics Section -->
    <div class="mb-8">
        <livewire:travel-stats />
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Trips -->
        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-3 bg-primary/10 rounded-lg">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-foreground-muted">Total Trips</p>
                    <p class="text-2xl font-bold text-foreground">{{ $stats['total_trips'] }}</p>
                </div>
            </div>
        </div>

        <!-- Countries Visited -->
        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-foreground-muted">Countries Visited</p>
                    <p class="text-2xl font-bold text-foreground">{{ $stats['countries_visited'] }}</p>
                </div>
            </div>
        </div>

        <!-- Upcoming Trips -->
        <div class="card p-6">
            <div class="flex items-center">
                <div class="p-3 bg-accent/10 rounded-lg">
                    <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-foreground-muted">Upcoming Trips</p>
                    <p class="text-2xl font-bold text-foreground">{{ $stats['upcoming_count'] }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Trips Timeline -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-foreground">Upcoming Trips</h2>
            <a href="{{ route('trips.listing') }}" class="text-primary hover:text-primary-dark text-sm font-medium">
                View All Trips →
            </a>
        </div>

        @if($upcomingTrips->count() > 0)
            <div class="space-y-4">
                @foreach($upcomingTrips as $trip)
                    <div class="flex items-start space-x-4 p-4 bg-surface-muted dark:bg-surface rounded-lg">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium text-foreground">{{ $trip->destination }}</h3>
                                <x-status-badge :status="$trip->status" />
                            </div>
                            <p class="text-sm text-foreground-muted mt-1">{{ $trip->country_code }}</p>
                            <div class="flex items-center mt-2 text-sm text-foreground-muted">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $trip->start_date->format('M j, Y') }} - {{ $trip->end_date->format('M j, Y') }}
                                @if($trip->budget)
                                    <span class="ml-4 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                        PKR {{ number_format($trip->budget, 0) }}
                                    </span>
                                @endif
                            </div>
                            @if($trip->notes)
                                <p class="text-sm text-foreground-muted mt-2">{{ Str::limit($trip->notes, 150) }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-foreground-subtle mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-foreground mb-2">No upcoming trips</h3>
                <p class="text-foreground-muted mb-6">Plan your next adventure to see it here.</p>
                <button
                    wire:click="createTrip"
                    class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary-dark font-medium transition-colors cursor-pointer"
                >
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Plan Your First Trip
                </button>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Weather Check -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-foreground mb-1">Check Weather</h3>
                    <p class="text-foreground-muted">Get weather updates for your destinations</p>
                </div>
                <a href="{{ route('weather') }}" class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary-dark font-medium transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                    View Weather
                </a>
            </div>
        </div>

        <!-- Holiday Calendar -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-foreground mb-1">Holiday Calendar</h3>
                    <p class="text-foreground-muted">Discover holidays and festivals</p>
                </div>
                <a href="{{ route('country.info') }}" class="inline-flex items-center px-4 py-2 bg-accent text-accent-foreground rounded-lg hover:bg-accent-light font-medium transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    View Holidays
                </a>
            </div>
        </div>
    </div>
</div>
