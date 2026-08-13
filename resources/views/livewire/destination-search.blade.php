<div class="w-full max-w-5xl mx-auto">
    <!-- Search Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-foreground mb-1">Discover Destinations</h1>
        <p class="text-foreground-muted">Search and explore amazing places around the world</p>
    </div>

    <!-- Search Card -->
    <div class="mb-6 rounded-2xl card glass">
        <div class="relative sm:p-5">
            <div class="relative">
                <button
                    type="button"
                    wire:click="triggerSearch"
                    class="absolute inset-y-0 left-0 pl-4 pr-2 flex items-center text-foreground-subtle hover:text-foreground-muted cursor-pointer"
                    aria-label="Search"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchQuery"
                    wire:keydown.enter="triggerSearch"
                    class="block w-full rounded-xl py-4 pr-4 pl-12 border border-border bg-surface-card shadow-sm focus:ring-2 focus:ring-primary focus:border-primary text-base text-foreground"
                    placeholder="Search for countries, cities, states or provinces..."
                    autocomplete="off"
                >
                @if($searchQuery)
                    <button
                        wire:click="clearSearch"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-foreground-subtle hover:text-foreground-muted cursor-pointer"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                @endif
            </div>
            <p class="mt-2 text-xs text-foreground-subtle">Tip: enter at least 4 characters. Press Enter or click the search icon.</p>
        </div>
    </div>

    <!-- Recent Searches -->
    @if($showRecentSearches && count($recentSearches) > 0)
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-foreground">Recent Searches</h3>
                <button
                    wire:click="clearHistory"
                    class="text-sm text-destructive hover:text-destructive/80 font-medium cursor-pointer"
                >
                    Clear History
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($recentSearches as $search)
                    <div
                        wire:click="selectCountry('{{ $search['code'] }}')"
                        class="bg-surface-card rounded-lg border border-border p-4 hover:border-primary hover:shadow-md transition-all duration-200 cursor-pointer group"
                    >
                        <div class="flex items-center space-x-3">
                            @if($search['flag'])
                                <img
                                    src="{{ $search['flag'] }}"
                                    alt="{{ $search['name'] }} flag"
                                    class="w-8 h-6 object-cover rounded-sm"
                                >
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-foreground group-hover:text-primary truncate">
                                    {{ $search['name'] }}
                                    @if(($search['type'] ?? 'country') !== 'country')
                                        <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-medium bg-primary/10 text-primary capitalize">{{ $search['type'] }}</span>
                                    @endif
                                </h4>
                                <p class="text-xs text-foreground-subtle truncate">
                                    {{ trim(($search['capital'] ?? '') . (($search['capital'] ?? '') && ($search['region'] ?? '') ? ', ' : '') . ($search['region'] ?? '')) }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Loading State -->
    @if($isLoading)
        <div class="space-y-4">
            <div class="flex items-center space-x-2 mb-4">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary"></div>
                <span class="text-foreground-muted">Searching destinations...</span>
            </div>

            <!-- Skeleton Loaders -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @for($i = 0; $i < 6; $i++)
                    <div class="bg-surface-card rounded-xl border border-border p-5 animate-pulse">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-8 bg-surface-muted dark:bg-surface rounded-sm"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-surface-muted dark:bg-surface rounded w-3/4"></div>
                                <div class="h-3 bg-surface-muted dark:bg-surface rounded w-1/2"></div>
                                <div class="h-3 bg-surface-muted dark:bg-surface rounded w-2/3"></div>
                            </div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="h-3 bg-surface-muted dark:bg-surface rounded w-full"></div>
                            <div class="h-3 bg-surface-muted dark:bg-surface rounded w-4/5"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    @endif

    <!-- Search Results -->
    @if(!$isLoading && $this->resultCount > 0)
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-foreground">
                    Search Results ({{ $this->resultCount }} found)
                </h3>
            </div>

            @if(count($searchResults['countries']) > 0)
            <div>
            <h4 class="text-sm font-semibold text-foreground-subtle uppercase tracking-wide mb-3">Countries</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($searchResults['countries'] as $country)
                    <div
                        wire:click="selectCountry('{{ $country['code'] }}')"
                        class="bg-surface-card rounded-xl border border-border p-5 hover:border-primary hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 cursor-pointer group"
                    >
                        <!-- Country Header -->
                        <div class="flex items-start space-x-4 mb-4">
                            @if($country['flag'])
                                <img
                                    src="{{ $country['flag'] }}"
                                    alt="{{ $country['name'] }} flag"
                                    class="w-12 h-8 object-cover rounded-sm shadow-sm"
                                >
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-lg font-semibold text-foreground group-hover:text-primary mb-1">
                                    {{ $country['name'] }}
                                </h4>
                                <div class="flex items-center gap-2 text-xs text-foreground-muted">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-surface-muted">{{ $country['code'] }}</span>
                                    <span>•</span>
                                    <span class="truncate">{{ $country['region'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Country Details -->
                        <div class="space-y-2">
                            @if($country['capital'])
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-foreground-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-sm text-foreground">{{ $country['capital'] }}</span>
                                </div>
                            @endif

                            @if($country['population'])
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-foreground-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <span class="text-sm text-foreground">{{ number_format($country['population']) }}</span>
                                </div>
                            @endif

                            @if($country['currency'])
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-foreground-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    <span class="text-sm text-foreground">{{ $country['currency'] }}</span>
                                </div>
                            @endif

                            @if($country['timezone'])
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-foreground-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm text-foreground">{{ $country['timezone'] }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Languages -->
                        @if(count($country['languages']) > 0)
                            <div class="mt-4 pt-4 border-t border-border">
                                <div class="flex items-center space-x-2 mb-2">
                                    <svg class="w-4 h-4 text-foreground-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                    <span class="text-xs font-medium text-foreground-subtle uppercase tracking-wide">Languages</span>
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($country['languages'], 0, 3) as $language)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                            {{ $language['name'] }}
                                        </span>
                                    @endforeach
                                    @if(count($country['languages']) > 3)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-surface-muted text-foreground-muted">
                                            +{{ count($country['languages']) - 3 }} more
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Action Button -->
                        <div class="mt-4 pt-4 border-t border-border">
                            <div class="flex items-center justify-center text-primary group-hover:text-primary-dark">
                                <a href="{{ route('country.info', ['code' => $country['code']]) }}" class="inline-flex items-center gap-2 px-3 py-2 border rounded bg-surface-card hover:bg-surface-muted cursor-pointer">
                                    <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                    <span class="text-sm font-medium">Select Destination</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            </div>
            @endif

            @if(count($searchResults['states']) > 0)
            <div>
            <h4 class="text-sm font-semibold text-foreground-subtle uppercase tracking-wide mb-3">States &amp; Provinces</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($searchResults['states'] as $index => $place)
                    <div
                        wire:key="state-{{ $index }}"
                        class="bg-surface-card rounded-xl border {{ $expandedStateIndex === $index ? 'border-primary shadow-md sm:col-span-2 lg:col-span-3' : 'border-border hover:border-primary hover:shadow-md' }} p-4 transition-all duration-200 group"
                    >
                        <div wire:click="toggleStateCities({{ $index }})" class="flex items-center space-x-3 cursor-pointer">
                            @if($place['flag'])
                                <img src="{{ $place['flag'] }}" alt="{{ $place['country_name'] }} flag" class="w-10 h-7 object-cover rounded-sm shadow-sm">
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-base font-semibold text-foreground group-hover:text-primary truncate">{{ $place['name'] }}</h4>
                                <div class="flex items-center gap-2 text-xs text-foreground-muted">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-accent/10 text-accent capitalize">{{ $place['type'] }}</span>
                                    <span class="truncate">{{ $place['country_name'] ?? $place['country_code'] }}</span>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-foreground-subtle transition-transform {{ $expandedStateIndex === $index ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>

                        @if($expandedStateIndex === $index)
                            <div class="mt-4 pt-4 border-t border-border">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-medium text-foreground-subtle uppercase tracking-wide">
                                        Cities in {{ $place['name'] }}
                                        @if($stateCityTotal > count($stateCities))
                                            <span class="normal-case font-normal">(top {{ count($stateCities) }} of {{ $stateCityTotal }})</span>
                                        @endif
                                    </span>
                                    <button
                                        wire:click="selectPlace('states', {{ $index }})"
                                        class="text-xs font-medium text-primary hover:text-primary-dark cursor-pointer"
                                    >
                                        Select entire {{ $place['type'] }} →
                                    </button>
                                </div>
                                @if(count($stateCities) > 0)
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($stateCities as $cityIndex => $city)
                                            <button
                                                wire:key="state-city-{{ $index }}-{{ $cityIndex }}"
                                                wire:click="selectStateCity({{ $cityIndex }})"
                                                class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-primary/10 text-primary hover:bg-primary hover:text-white transition-colors cursor-pointer"
                                            >
                                                {{ $city['name'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-sm text-foreground-subtle">No city data available for this {{ $place['type'] }}.</p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            </div>
            @endif

            @if(count($searchResults['cities']) > 0)
            <div>
            <h4 class="text-sm font-semibold text-foreground-subtle uppercase tracking-wide mb-3">Cities</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($searchResults['cities'] as $index => $place)
                    <div
                        wire:key="city-{{ $index }}"
                        wire:click="selectPlace('cities', {{ $index }})"
                        class="bg-surface-card rounded-xl border border-border p-4 hover:border-primary hover:shadow-md transition-all duration-200 cursor-pointer group"
                    >
                        <div class="flex items-center space-x-3">
                            @if($place['flag'])
                                <img src="{{ $place['flag'] }}" alt="{{ $place['country_code'] }} flag" class="w-10 h-7 object-cover rounded-sm shadow-sm">
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-base font-semibold text-foreground group-hover:text-primary truncate">{{ $place['name'] }}</h4>
                                <div class="flex items-center gap-2 text-xs text-foreground-muted">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-primary/10 text-primary">City</span>
                                    <span class="truncate">{{ trim(($place['state'] ? $place['state'] . ', ' : '') . $place['country_code']) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            </div>
            @endif
        </div>
    @endif

    <!-- No Results -->
    @if(!$isLoading && $searchQuery && $this->resultCount === 0)
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-foreground-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-foreground">No destinations found</h3>
            <p class="mt-1 text-sm text-foreground-subtle">Try searching with different keywords or check your spelling.</p>
        </div>
    @endif

    <!-- Empty State (when no search and no recent searches) -->
    @if(!$searchQuery && count($recentSearches) === 0)
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-foreground-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-foreground">Start exploring destinations</h3>
            <p class="mt-1 text-sm text-foreground-subtle">Search for countries, capitals, or regions to discover amazing places.</p>
        </div>
    @endif
</div>

