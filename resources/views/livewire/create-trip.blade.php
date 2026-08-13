<div class="w-full max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-foreground mb-2">Create New Trip</h1>
        <p class="text-foreground-muted">Plan your next adventure with our smart travel planner</p>
    </div>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <!-- Step 1: Destination -->
            <div class="flex items-center flex-1">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-full {{ $currentStep >= 1 ? 'bg-primary text-primary-foreground' : 'bg-surface-muted dark:bg-surface text-foreground-muted' }} font-semibold">
                    @if($currentStep > 1)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    @else
                    1
                    @endif
                </div>
                <div class="ml-3 hidden sm:block">
                    <p class="text-sm font-medium {{ $currentStep >= 1 ? 'text-primary' : 'text-foreground-muted' }}">
                        Destination</p>
                </div>
            </div>
            <div class="flex-1 h-1 mx-2 {{ $currentStep > 1 ? 'bg-primary' : 'bg-surface-muted dark:bg-surface' }}"></div>

            <!-- Step 2: Dates -->
            <div class="flex items-center flex-1">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-full {{ $currentStep >= 2 ? 'bg-primary text-primary-foreground' : 'bg-surface-muted dark:bg-surface text-foreground-muted' }} font-semibold">
                    @if($currentStep > 2)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    @else
                    2
                    @endif
                </div>
                <div class="ml-3 hidden sm:block">
                    <p class="text-sm font-medium {{ $currentStep >= 2 ? 'text-primary' : 'text-foreground-muted' }}">Dates
                    </p>
                </div>
            </div>
            <div class="flex-1 h-1 mx-2 {{ $currentStep > 2 ? 'bg-primary' : 'bg-surface-muted dark:bg-surface' }}"></div>

            <!-- Step 3: Details -->
            <div class="flex items-center flex-1">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-full {{ $currentStep >= 3 ? 'bg-primary text-primary-foreground' : 'bg-surface-muted dark:bg-surface text-foreground-muted' }} font-semibold">
                    @if($currentStep > 3)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    @else
                    3
                    @endif
                </div>
                <div class="ml-3 hidden sm:block">
                    <p class="text-sm font-medium {{ $currentStep >= 3 ? 'text-primary' : 'text-foreground-muted' }}">Details
                    </p>
                </div>
            </div>
            <div class="flex-1 h-1 mx-2 {{ $currentStep > 3 ? 'bg-primary' : 'bg-surface-muted dark:bg-surface' }}"></div>

            <!-- Step 4: Confirm -->
            <div class="flex items-center flex-1">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-full {{ $currentStep >= 4 ? 'bg-primary text-primary-foreground' : 'bg-surface-muted dark:bg-surface text-foreground-muted' }} font-semibold">
                    4
                </div>
                <div class="ml-3 hidden sm:block">
                    <p class="text-sm font-medium {{ $currentStep >= 4 ? 'text-primary' : 'text-foreground-muted' }}">Confirm
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
    <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if (session()->has('error'))
    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-destructive px-4 py-3 rounded-lg">
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-destructive px-4 py-3 rounded-lg">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Form Card -->
    <div class="card p-6 md:p-8">
        <!-- Step 1: Destination -->
        @if($currentStep === 1)
        <div wire:key="step-1-content" class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-foreground mb-2">Where are you going?</h2>
                <p class="text-foreground-muted">Search for your destination</p>
            </div>

            <!-- Search Input -->
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="destination" wire:keydown.enter="searchDestinations"
                    class="block w-full rounded-xl p-4 border border-border bg-surface-card text-foreground focus:ring-2 focus:ring-primary focus:border-primary"
                    placeholder="Search for countries or cities..." autocomplete="off">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    @if($isSearching)
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-primary"></div>
                    @else
                    <svg class="h-5 w-5 text-foreground-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    @endif
                </div>
            </div>

            <!-- Search Results -->
            @if($this->searchResultCount > 0)
            <div class="space-y-4 max-h-96 overflow-y-auto">
                @if(count($searchResults['countries']) > 0)
                <div>
                    <h5 class="text-xs font-semibold text-foreground-subtle uppercase tracking-wide mb-2">Countries</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($searchResults['countries'] as $index => $country)
                        <div wire:key="country-{{ $country['code'] }}" wire:click="selectDestination('countries', {{ $index }})"
                            class="bg-surface-card border border-border rounded-lg p-4 hover:border-primary hover:shadow-md transition-all cursor-pointer group">
                            <div class="flex items-start space-x-3">
                                @if($country['flag'])
                                <img src="{{ $country['flag'] }}" alt="{{ $country['name'] }} flag"
                                    class="w-12 h-8 object-cover rounded-sm shadow-sm">
                                @endif
                                <div class="flex-1">
                                    <h4 class="font-semibold text-foreground group-hover:text-primary">
                                        {{ $country['name'] }}
                                    </h4>
                                    <p class="text-sm text-foreground-muted">{{ $country['capital'] }}</p>
                                    <p class="text-xs text-foreground-subtle">{{ $country['region'] }}</p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(count($searchResults['states']) > 0)
                <div>
                    <h5 class="text-xs font-semibold text-foreground-subtle uppercase tracking-wide mb-2">States &amp; Provinces</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($searchResults['states'] as $index => $place)
                        <div wire:key="state-{{ $index }}"
                            class="bg-surface-card border rounded-lg p-3 transition-all group {{ $expandedStateIndex === $index ? 'border-primary shadow-md md:col-span-2' : 'border-border hover:border-primary hover:shadow-md' }}">
                            <div wire:click="toggleStateCities({{ $index }})" class="flex items-center space-x-3 cursor-pointer">
                                @if($place['flag'])
                                <img src="{{ $place['flag'] }}" alt="{{ $place['country_name'] }} flag"
                                    class="w-10 h-7 object-cover rounded-sm shadow-sm">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-foreground group-hover:text-primary truncate">{{ $place['name'] }}</h4>
                                    <p class="text-xs text-foreground-subtle truncate">
                                        <span class="capitalize">{{ $place['type'] }}</span> · {{ $place['country_name'] ?? $place['country_code'] }}
                                    </p>
                                </div>
                                <svg class="w-4 h-4 text-foreground-subtle transition-transform {{ $expandedStateIndex === $index ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>

                            @if($expandedStateIndex === $index)
                            <div class="mt-3 pt-3 border-t border-border">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-xs font-medium text-foreground-subtle uppercase tracking-wide">
                                        Cities
                                        @if($stateCityTotal > count($stateCities))
                                            <span class="normal-case font-normal">(top {{ count($stateCities) }} of {{ $stateCityTotal }})</span>
                                        @endif
                                    </span>
                                    <button wire:click="selectDestination('states', {{ $index }})"
                                        class="text-xs font-medium text-primary hover:text-primary-dark cursor-pointer">
                                        Use entire {{ $place['type'] }} →
                                    </button>
                                </div>
                                @if(count($stateCities) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($stateCities as $cityIndex => $city)
                                    <button wire:key="state-city-{{ $index }}-{{ $cityIndex }}"
                                        wire:click="selectStateCity({{ $cityIndex }})"
                                        class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-primary/10 text-primary hover:bg-primary hover:text-white transition-colors cursor-pointer">
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
                    <h5 class="text-xs font-semibold text-foreground-subtle uppercase tracking-wide mb-2">Cities</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($searchResults['cities'] as $index => $place)
                        <div wire:key="city-{{ $index }}" wire:click="selectDestination('cities', {{ $index }})"
                            class="bg-surface-card border border-border rounded-lg p-3 hover:border-primary hover:shadow-md transition-all cursor-pointer group">
                            <div class="flex items-center space-x-3">
                                @if($place['flag'])
                                <img src="{{ $place['flag'] }}" alt="{{ $place['country_code'] }} flag"
                                    class="w-10 h-7 object-cover rounded-sm shadow-sm">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-foreground group-hover:text-primary truncate">{{ $place['name'] }}</h4>
                                    <p class="text-xs text-foreground-subtle truncate">
                                        City · {{ trim(($place['state'] ? $place['state'] . ', ' : '') . $place['country_code']) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endif

            <!-- Selected Destination -->
            @if($countryCode && $this->searchResultCount === 0)
            <div class="bg-primary/5 border border-primary/20 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        @if(isset($selectedCountry['flag']))
                        <img src="{{ $selectedCountry['flag'] }}" alt="{{ $destination }} flag"
                            class="w-12 h-8 object-cover rounded-sm">
                        @endif
                        <div>
                            <h4 class="font-semibold text-foreground">{{ $destination }}</h4>
                            @if(isset($selectedCountry['capital']))
                            <p class="text-sm text-foreground-muted">{{ $selectedCountry['capital'] }}</p>
                            @endif
                        </div>
                    </div>
                    <button wire:click="$set('countryCode', '')" class="text-destructive hover:text-destructive/80 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Weather Preview -->
                @if($showWeatherPreview && !empty($weatherPreview))
                <div class="mt-4 pt-4 border-t border-primary/20">
                    <h5 class="text-sm font-medium text-foreground mb-2">Current Weather</h5>
                    <div class="flex items-center space-x-4">
                        @if(isset($weatherPreview['icon']))
                        <img src="https://openweathermap.org/img/wn/{{ $weatherPreview['icon'] }}@2x.png"
                            alt="Weather icon" class="w-12 h-12">
                        @endif
                        <div>
                            <p class="text-2xl font-bold text-foreground">{{ round($weatherPreview['temperature']) }}°C
                            </p>
                            <p class="text-sm text-foreground-muted capitalize">{{ $weatherPreview['condition'] }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endif

        <!-- Step 2: Dates -->
        @if($currentStep === 2)
        <div wire:key="step-2-content" class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-foreground mb-2">When are you traveling?</h2>
                <p class="text-foreground-muted">Select your trip dates</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">Start Date</label>
                    <input type="date" wire:model.live="startDate" wire:change="checkConflicts"
                        min="{{ date('Y-m-d') }}"
                        class="block w-full rounded-lg p-3 border border-border bg-surface-card text-foreground focus:ring-2 focus:ring-primary focus:border-primary">
                    @error('startDate') <span class="text-destructive text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-foreground mb-2">End Date</label>
                    <input type="date" wire:model.live="endDate" wire:change="checkConflicts"
                        min="{{ $startDate ?: date('Y-m-d') }}"
                        class="block w-full rounded-lg p-3 border border-border bg-surface-card text-foreground focus:ring-2 focus:ring-primary focus:border-primary">
                    @error('endDate') <span class="text-destructive text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Trip Duration -->
            @if($startDate && $endDate)
            @php
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            $duration = $start->diff($end)->days + 1;
            @endphp
            <div class="bg-primary/5 border border-primary/20 rounded-lg p-4">
                <p class="text-sm text-primary dark:text-primary">
                    <span class="font-semibold">Trip Duration:</span> {{ $duration }} {{ $duration === 1 ? 'day' :
                    'days' }}
                </p>
            </div>
            @endif

            <!-- Conflicting Trips Warning -->
            @if(count($conflictingTrips) > 0)
            <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 mt-0.5 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-amber-800 dark:text-amber-300 mb-1">Conflicting Trips Detected</h4>
                        <p class="text-sm text-amber-700 dark:text-amber-400 mb-2">You have the following trips during these dates:</p>
                        <ul class="list-disc list-inside text-sm text-amber-700 dark:text-amber-400">
                            @foreach($conflictingTrips as $trip)
                            <li wire:key="conflict-{{ $trip['id'] }}">{{ $trip['destination'] }} ({{ date('M j', strtotime($trip['start_date'])) }} - {{
                                date('M j, Y', strtotime($trip['end_date'])) }})</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Step 3: Details -->
        @if($currentStep === 3)
        <div wire:key="step-3-content" class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-foreground mb-2">Trip Details</h2>
                <p class="text-foreground-muted">Tell us more about your trip</p>
            </div>

            <!-- Trip Type -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-3">Trip Type</label>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    @foreach($tripTypes as $typeKey => $typeInfo)
                    <button wire:key="type-{{ $typeKey }}" type="button" wire:click="$set('type', '{{ $typeKey }}')"
                        class="flex flex-col items-center justify-center p-4 border-2 rounded-lg transition-all cursor-pointer {{ $type === $typeKey ? 'border-primary bg-primary/5' : 'border-border hover:border-primary/50' }}">
                        <div class="w-8 h-8 mb-2 {{ $type === $typeKey ? 'text-primary' : 'text-foreground-muted' }}">
                            @if($typeKey === 'business')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            @elseif($typeKey === 'leisure')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            @elseif($typeKey === 'adventure')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l3.057 5.287L12 3l3.943 5.287L19 3v15a2 2 0 01-2 2H7a2 2 0 01-2-2V3z"/></svg>
                            @elseif($typeKey === 'family')
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @else
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                            @endif
                        </div>
                        <span
                            class="text-sm font-medium {{ $type === $typeKey ? 'text-primary' : 'text-foreground-muted' }}">
                            {{ $typeInfo['label'] }}
                        </span>
                    </button>
                    @endforeach
                </div>
                @error('type') <span class="text-destructive text-sm">{{ $message }}</span> @enderror
            </div>


            <!-- Travelers Count -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Number of Travelers</label>
                <div class="flex items-center space-x-4">
                    <button type="button" wire:click.prevent="decrementTravelers"
                        class="w-10 h-10 rounded-lg border border-border hover:bg-surface-muted flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-primary cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </button>
                    <div
                        class="w-20 text-center rounded-lg p-3 border border-border bg-surface-muted dark:bg-surface font-medium text-foreground">
                        {{ $travelers }}
                    </div>
                    <button type="button" wire:click.prevent="incrementTravelers"
                        class="w-10 h-10 rounded-lg border border-border hover:bg-surface-muted flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-primary cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                    </button>
                    <span class="text-sm text-foreground-muted">{{ $travelers === 1 ? 'traveler' : 'travelers' }}</span>
                </div>
                @error('travelers') <span class="text-destructive text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Budget -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Budget (Optional)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-foreground-muted">PKR</span>
                    <input type="number" wire:model="budget" min="0" step="0.01"
                        class="block w-full pl-14 rounded-lg p-3 border border-border bg-surface-card text-foreground focus:ring-2 focus:ring-primary focus:border-primary"
                        placeholder="0.00">
                </div>
                <p class="mt-1 text-sm text-foreground-subtle">Enter your estimated budget in Pakistani Rupees</p>
                @error('budget') <span class="text-destructive text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-2">Notes (Optional)</label>
                <textarea wire:model.live="notes" rows="4" maxlength="1000"
                    class="block w-full rounded-lg p-3 border border-border bg-surface-card text-foreground focus:ring-2 focus:ring-primary focus:border-primary"
                    placeholder="Add any additional notes about your trip..."></textarea>
                <p class="mt-1 text-sm text-foreground-subtle">{{ strlen($notes) }}/1000 characters</p>
                @error('notes') <span class="text-destructive text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        @endif

        <!-- Step 4: Confirm -->
        @if($currentStep === 4)
        <div wire:key="step-4-content" class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-foreground mb-2">Review Your Trip</h2>
                <p class="text-foreground-muted">Please review your trip details before creating</p>
            </div>

            <!-- Trip Summary -->
            <div class="bg-surface-muted dark:bg-surface rounded-lg p-6 space-y-4">
                <!-- Destination -->
                <div class="flex items-start justify-between pb-4 border-b border-border">
                    <div class="flex items-center space-x-3">
                        @if(isset($selectedCountry['flag']))
                        <img src="{{ $selectedCountry['flag'] }}" alt="{{ $destination }} flag"
                            class="w-12 h-8 object-cover rounded-sm">
                        @endif
                        <div>
                            <p class="text-sm text-foreground-muted">Destination</p>
                            <p class="text-lg font-semibold text-foreground">{{ $destination }}</p>
                        </div>
                    </div>
                    <button wire:click="goToStep(1)" class="text-primary hover:text-primary-dark text-sm font-medium cursor-pointer">
                        Edit
                    </button>
                </div>

                <!-- Dates -->
                <div class="flex items-start justify-between pb-4 border-b border-border">
                    <div>
                        <p class="text-sm text-foreground-muted mb-1">Travel Dates</p>
                        <p class="text-lg font-semibold text-foreground">
                            {{ date('M j, Y', strtotime($startDate)) }} - {{ date('M j, Y', strtotime($endDate)) }}
                        </p>
                        @php
                        $start = new DateTime($startDate);
                        $end = new DateTime($endDate);
                        $duration = $start->diff($end)->days + 1;
                        @endphp
                        <p class="text-sm text-foreground-muted">{{ $duration }} {{ $duration === 1 ? 'day' : 'days' }}</p>
                    </div>
                    <button wire:click="goToStep(2)" class="text-primary hover:text-primary-dark text-sm font-medium cursor-pointer">
                        Edit
                    </button>
                </div>

                <!-- Trip Details -->
                <div class="flex items-start justify-between">
                    <div class="space-y-3 flex-1">
                        <div>
                            <p class="text-sm text-foreground-muted">Trip Type</p>
                            <p class="text-lg font-semibold text-foreground capitalize">{{ $type }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-foreground-muted">Travelers</p>
                            <p class="text-lg font-semibold text-foreground">{{ $travelers }} {{ $travelers === 1 ?
                                'person' : 'people' }}</p>
                        </div>
                        @if($budget)
                        <div>
                            <p class="text-sm text-foreground-muted">Budget</p>
                            <p class="text-lg font-semibold text-foreground">PKR {{ number_format($budget, 2) }}</p>
                        </div>
                        @endif
                        @if($notes)
                        <div>
                            <p class="text-sm text-foreground-muted">Notes</p>
                            <p class="text-sm text-foreground">{{ $notes }}</p>
                        </div>
                        @endif
                    </div>
                    <button wire:click="goToStep(3)" class="text-primary hover:text-primary-dark text-sm font-medium cursor-pointer">
                        Edit
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Navigation Buttons -->
        <div wire:key="nav-buttons-step-{{ $currentStep }}" class="flex items-center justify-between mt-8 pt-6 border-t border-border">
            <div>
                @if($currentStep > 1)
                <button wire:key="prev-btn-{{ $currentStep }}" type="button" wire:click="previousStep"
                    class="inline-flex items-center px-6 py-3 border border-border rounded-lg text-foreground bg-surface-card hover:bg-surface-muted font-medium transition-colors cursor-pointer">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    Previous
                </button>
                @else
                <a href="{{ route('dashboard') }}" wire:navigate
                    class="inline-flex items-center px-6 py-3 border border-border rounded-lg text-foreground bg-surface-card hover:bg-surface-muted font-medium transition-colors">
                    Cancel
                </a>
                @endif
            </div>

            <div class="flex items-center space-x-3">
                @if($currentStep < 4)
                <button wire:key="next-btn-{{ $currentStep }}" type="button" wire:click="nextStep"
                    class="inline-flex items-center px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary-dark font-medium transition-colors cursor-pointer">
                    Next
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
                @else
                <button wire:key="create-btn" type="button" wire:click="createTrip" wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed"
                    class="inline-flex items-center px-8 py-3 bg-accent text-accent-foreground rounded-lg hover:bg-accent-light font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                    <svg wire:loading.remove wire:target="createTrip" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                    <svg wire:loading wire:target="createTrip" class="animate-spin w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="createTrip">Create Trip</span>
                    <span wire:loading wire:target="createTrip">Creating...</span>
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Help Text -->
    <div class="mt-6 text-center">
        <p class="text-sm text-foreground-subtle">
            Your progress is automatically saved. You can safely navigate away and return later.
        </p>
    </div>
</div>
