<div class="w-full max-w-6xl mx-auto"
     x-data="{ creating: false }"
     x-on:trip-create-failed.window="creating = false"
     x-on:livewire-request-failed.window="creating = false">

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="page-title mb-1.5">Create New Trip</h1>
        <p class="text-foreground-muted">Plan your next adventure with our smart travel planner</p>
    </div>

    @include('livewire.partials.wizard.stepper')

    <!-- Flash Messages -->
    @if ($errors->any())
    <x-ui.alert variant="error" class="mb-6">
        <ul class="list-disc list-inside space-y-0.5">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </x-ui.alert>
    @endif

    <!-- Mobile trip summary (the ticket rail is desktop-only) -->
    @if($countryCode)
    <div class="lg:hidden mb-6 card p-3 flex items-center gap-3">
        <img src="https://flagcdn.com/{{ strtolower($countryCode) }}.svg" alt="{{ strtoupper($countryCode) }} flag"
            class="w-9 h-6 rounded object-cover shadow-sm shrink-0">
        <div class="min-w-0 flex-1">
            <div class="text-sm font-bold text-foreground truncate">{{ $destination }}</div>
            <div class="text-xs text-foreground-muted tabular-nums">
                @if($startDate && $endDate)
                    {{ date('M j', strtotime($startDate)) }} – {{ date('M j, Y', strtotime($endDate)) }}
                @else
                    Dates not set
                @endif
            </div>
        </div>
        @if($showWeatherPreview && !empty($weatherPreview))
            <span class="inline-flex items-center gap-1 text-sm font-semibold text-foreground tabular-nums shrink-0">
                @if(!empty($weatherPreview['icon']))
                    <img src="https://openweathermap.org/img/wn/{{ $weatherPreview['icon'] }}.png" alt="" aria-hidden="true" class="w-7 h-7 -my-1">
                @endif
                {{ round($weatherPreview['temperature'] ?? 0) }}°C
            </span>
        @endif
    </div>
    @endif

    <div class="lg:grid lg:grid-cols-[minmax(0,1fr)_340px] lg:gap-8 lg:items-start">

    <!-- Form Card -->
    <div class="card p-6 md:p-8">
        <!-- Step 1: Destination -->
        @if($currentStep === 1)
        <div wire:key="step-1-content" class="space-y-6 fade-up">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-foreground mb-1.5">Where are you going?</h2>
                <p class="text-foreground-muted">Search for a country, city, state or province</p>
            </div>

            <!-- Search Input -->
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-foreground-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" wire:model.live.debounce.300ms="destination" wire:keydown.enter="searchDestinations"
                    class="field p-4 pl-12"
                    placeholder="Search for countries or cities..." autocomplete="off"
                    aria-label="Search destinations">
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                    <x-ui.spinner wire:loading.delay wire:target="destination, searchDestinations" size="md" class="text-primary" />
                </div>
            </div>

            <!-- Search skeleton while results load -->
            <div wire:loading.delay.grid wire:target="destination, searchDestinations" class="grid grid-cols-1 md:grid-cols-2 gap-4" aria-hidden="true">
                <x-ui.skeleton variant="row" :count="4" />
            </div>

            <div wire:loading.remove.delay wire:target="destination, searchDestinations">
            <!-- Search Results -->
            @if($this->searchResultCount > 0)
            <div class="space-y-6 max-h-96 overflow-y-auto pr-1"
                 wire:loading.delay.class="opacity-40 pointer-events-none" wire:target="selectDestination, selectStateCity">
                @if(count($searchResults['countries']) > 0)
                <div>
                    <h5 class="eyebrow text-foreground-subtle mb-2.5">Countries</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($searchResults['countries'] as $index => $country)
                        <div wire:key="country-{{ $country['code'] }}" wire:click="selectDestination('countries', {{ $index }})"
                            role="button" tabindex="0" wire:keydown.enter="selectDestination('countries', {{ $index }})"
                            class="bg-surface-card border border-border rounded-xl p-4 hover:border-primary hover:shadow-md active:scale-[0.99] transition-[border-color,box-shadow,transform] duration-200 cursor-pointer group focus-ring">
                            <div class="flex items-start gap-3">
                                @if($country['flag'])
                                <img src="{{ $country['flag'] }}" alt="{{ $country['name'] }} flag"
                                    class="w-12 h-8 object-cover rounded shadow-sm shrink-0">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-foreground group-hover:text-primary transition-colors duration-150 truncate">
                                        {{ $country['name'] }}
                                    </h4>
                                    <p class="text-sm text-foreground-muted truncate">{{ $country['capital'] }}</p>
                                    @if($country['region'])
                                        <x-ui.chip color="neutral" class="mt-1.5 text-[11px]">{{ $country['region'] }}</x-ui.chip>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(count($searchResults['states']) > 0)
                <div>
                    <h5 class="eyebrow text-foreground-subtle mb-2.5">States &amp; Provinces</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($searchResults['states'] as $index => $place)
                        <div wire:key="state-{{ $index }}"
                            class="bg-surface-card border rounded-xl p-3.5 transition-[border-color,box-shadow] duration-200 group {{ $expandedStateIndex === $index ? 'border-primary shadow-md md:col-span-2' : 'border-border hover:border-primary hover:shadow-md' }}">
                            <div wire:click="toggleStateCities({{ $index }})" class="flex items-center gap-3 cursor-pointer"
                                role="button" tabindex="0" wire:keydown.enter="toggleStateCities({{ $index }})"
                                aria-expanded="{{ $expandedStateIndex === $index ? 'true' : 'false' }}">
                                @if($place['flag'])
                                <img src="{{ $place['flag'] }}" alt="{{ $place['country_name'] }} flag"
                                    class="w-10 h-7 object-cover rounded shadow-sm shrink-0">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-foreground group-hover:text-primary transition-colors duration-150 truncate">{{ $place['name'] }}</h4>
                                    <p class="text-xs text-foreground-subtle truncate">
                                        <span class="capitalize">{{ $place['type'] }}</span> · {{ $place['country_name'] ?? $place['country_code'] }}
                                    </p>
                                </div>
                                <x-ui.spinner wire:loading.delay wire:target="toggleStateCities({{ $index }})" size="xs" class="text-primary shrink-0" />
                                <svg wire:loading.remove.delay wire:target="toggleStateCities({{ $index }})" class="w-4 h-4 text-foreground-subtle transition-transform duration-200 {{ $expandedStateIndex === $index ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>

                            @if($expandedStateIndex === $index)
                            <div class="mt-3 pt-3 border-t border-border fade-up">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="eyebrow text-foreground-subtle">
                                        Cities
                                        @if($stateCityTotal > count($stateCities))
                                            <span class="normal-case font-normal tracking-normal">(top {{ count($stateCities) }} of {{ $stateCityTotal }})</span>
                                        @endif
                                    </span>
                                    <button wire:click="selectDestination('states', {{ $index }})"
                                        class="text-xs font-semibold text-primary hover:text-primary-dark cursor-pointer focus-ring rounded">
                                        Use entire {{ $place['type'] }} →
                                    </button>
                                </div>
                                @if(count($stateCities) > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($stateCities as $cityIndex => $city)
                                    <button wire:key="state-city-{{ $index }}-{{ $cityIndex }}"
                                        wire:click="selectStateCity({{ $cityIndex }})"
                                        class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium bg-primary/10 text-primary hover:bg-primary hover:text-white transition-colors duration-150 cursor-pointer focus-ring">
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
                    <h5 class="eyebrow text-foreground-subtle mb-2.5">Cities</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        @foreach($searchResults['cities'] as $index => $place)
                        <div wire:key="city-{{ $index }}" wire:click="selectDestination('cities', {{ $index }})"
                            role="button" tabindex="0" wire:keydown.enter="selectDestination('cities', {{ $index }})"
                            class="bg-surface-card border border-border rounded-xl p-3.5 hover:border-primary hover:shadow-md active:scale-[0.99] transition-[border-color,box-shadow,transform] duration-200 cursor-pointer group focus-ring">
                            <div class="flex items-center gap-3">
                                @if($place['flag'])
                                <img src="{{ $place['flag'] }}" alt="{{ $place['country_code'] }} flag"
                                    class="w-10 h-7 object-cover rounded shadow-sm shrink-0">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-foreground group-hover:text-primary transition-colors duration-150 truncate">{{ $place['name'] }}</h4>
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
            </div>

            <!-- Selected-destination skeleton (country info + weather load after picking) -->
            <div wire:loading.delay.flex wire:target="selectDestination, selectStateCity"
                class="items-center gap-3 rounded-xl border border-primary/20 bg-primary/5 p-4 animate-pulse" aria-hidden="true">
                <div class="w-10 h-7 rounded bg-surface-muted dark:bg-surface shrink-0"></div>
                <div class="flex-1 space-y-2">
                    <div class="h-4 w-40 rounded bg-surface-muted dark:bg-surface"></div>
                    <div class="h-3 w-24 rounded bg-surface-muted dark:bg-surface"></div>
                </div>
            </div>

            <!-- Selected Destination (the ticket carries the full preview) -->
            @if($countryCode && $this->searchResultCount === 0)
            <div class="flex items-center justify-between gap-3 rounded-xl border border-primary/20 bg-primary/5 p-4 fade-up">
                <div class="flex items-center gap-3 min-w-0">
                    @if(isset($selectedCountry['flag']))
                    <img src="{{ $selectedCountry['flag'] }}" alt="{{ $destination }} flag"
                        class="w-10 h-7 object-cover rounded shadow-sm shrink-0">
                    @endif
                    <div class="min-w-0">
                        <h4 class="font-semibold text-foreground truncate">{{ $destination }}</h4>
                        @if(isset($selectedCountry['capital']))
                        <p class="text-xs text-foreground-muted truncate">{{ $selectedCountry['capital'] }}</p>
                        @endif
                    </div>
                </div>
                <button wire:click="$set('countryCode', '')" class="btn-ghost px-3.5 py-1.5 text-sm shrink-0">
                    Change
                </button>
            </div>
            @endif
        </div>
        @endif

        <!-- Step 2: Dates -->
        @if($currentStep === 2)
        <div wire:key="step-2-content" class="space-y-6 fade-up">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-foreground mb-1.5">When are you traveling?</h2>
                <p class="text-foreground-muted">Select your trip dates</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="create-start-date" class="eyebrow text-foreground-subtle block mb-2">Start Date</label>
                    <input id="create-start-date" type="date" wire:model.live="startDate" wire:change="checkConflicts"
                        min="{{ date('Y-m-d') }}"
                        class="field p-3">
                    @error('startDate') <span class="text-destructive text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label for="create-end-date" class="eyebrow text-foreground-subtle block mb-2">End Date</label>
                    <input id="create-end-date" type="date" wire:model.live="endDate" wire:change="checkConflicts"
                        min="{{ $startDate ?: date('Y-m-d') }}"
                        class="field p-3">
                    @error('endDate') <span class="text-destructive text-sm mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div wire:loading.delay.flex wire:target="startDate, endDate, checkConflicts" class="flex items-center gap-2 text-sm text-foreground-muted">
                <x-ui.spinner size="xs" class="text-primary" />
                Checking for date conflicts…
            </div>

            @if($startDate && $endDate)
            @php
                $start = new DateTime($startDate);
                $end = new DateTime($endDate);
                $duration = $start->diff($end)->days + 1;
            @endphp
            <div class="flex items-center gap-3 rounded-xl border border-primary/20 bg-primary/5 p-4 fade-up">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="eyebrow text-foreground-subtle">Trip duration</p>
                    <p class="font-bold text-foreground tabular-nums">{{ $duration }} {{ $duration === 1 ? 'day' : 'days' }}</p>
                </div>
            </div>
            @endif

            @if(count($conflictingTrips) > 0)
            <x-ui.alert variant="error">
                <p class="font-semibold mb-1">These dates overlap with existing trips:</p>
                <ul class="list-disc list-inside space-y-0.5 text-sm">
                    @foreach($conflictingTrips as $trip)
                    <li wire:key="conflict-{{ $trip['id'] }}">
                        {{ $trip['destination'] }}
                        ({{ date('M j', strtotime($trip['start_date'])) }} – {{ date('M j, Y', strtotime($trip['end_date'])) }})
                    </li>
                    @endforeach
                </ul>
            </x-ui.alert>
            @endif
        </div>
        @endif

        <!-- Step 3: Details -->
        @if($currentStep === 3)
        <div wire:key="step-3-content" class="space-y-8 fade-up">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-foreground mb-1.5">Trip details</h2>
                <p class="text-foreground-muted">What kind of trip is this?</p>
            </div>

            <!-- Trip Type -->
            <div>
                <span class="eyebrow text-foreground-subtle block mb-3">Trip type</span>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    @foreach($tripTypes as $typeKey => $typeInfo)
                    <button wire:key="type-{{ $typeKey }}" type="button" wire:click="$set('type', '{{ $typeKey }}')"
                        class="relative rounded-xl border p-4 text-center transition-[border-color,background-color,transform] duration-200 active:scale-[0.98] cursor-pointer focus-ring
                        {{ $type === $typeKey ? 'border-primary bg-primary/5 shadow-sm' : 'border-border bg-surface-card hover:border-primary/50' }}">
                        @if($type === $typeKey)
                        <span class="absolute top-2 right-2 w-5 h-5 rounded-full bg-primary text-primary-foreground flex items-center justify-center" aria-hidden="true">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        @endif
                        <span class="mx-auto mb-2 w-10 h-10 rounded-lg flex items-center justify-center {{ $type === $typeKey ? 'bg-primary/15 text-primary' : 'bg-surface-muted dark:bg-surface text-foreground-muted' }}">
                            @include('livewire.partials.wizard.type-icon', ['typeKey' => $typeKey, 'class' => 'w-5 h-5'])
                        </span>
                        <span class="block text-sm font-semibold {{ $type === $typeKey ? 'text-primary' : 'text-foreground' }}">{{ $typeInfo['label'] }}</span>
                        @if(!empty($typeInfo['description']))
                        <span class="block text-[11px] text-foreground-subtle mt-0.5 leading-snug">{{ $typeInfo['description'] }}</span>
                        @endif
                    </button>
                    @endforeach
                </div>
                @error('type') <span class="text-destructive text-sm mt-2 block">{{ $message }}</span> @enderror
            </div>

            <!-- Travelers -->
            <div>
                <span class="eyebrow text-foreground-subtle block mb-3">Travelers</span>
                <div class="flex items-center gap-4">
                    <div class="inline-flex items-center gap-1 rounded-xl border border-border bg-surface-card p-1.5">
                        <button type="button" wire:click.prevent="decrementTravelers"
                            class="w-10 h-10 rounded-lg flex items-center justify-center text-foreground hover:bg-surface-muted active:scale-[0.95] transition-[background-color,transform] duration-150 cursor-pointer focus-ring"
                            aria-label="Decrease travelers">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M20 12H4"/></svg>
                        </button>
                        <span class="w-12 text-center text-xl font-extrabold text-foreground tabular-nums" aria-live="polite">{{ $travelers }}</span>
                        <button type="button" wire:click.prevent="incrementTravelers"
                            class="w-10 h-10 rounded-lg flex items-center justify-center text-foreground hover:bg-surface-muted active:scale-[0.95] transition-[background-color,transform] duration-150 cursor-pointer focus-ring"
                            aria-label="Increase travelers">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                    <span class="text-sm text-foreground-muted">{{ $travelers === 1 ? 'traveler' : 'travelers' }}</span>
                </div>
                @error('travelers') <span class="text-destructive text-sm mt-2 block">{{ $message }}</span> @enderror
            </div>

            <!-- Budget -->
            <div>
                <label for="create-budget" class="eyebrow text-foreground-subtle block mb-3">Budget <span class="normal-case font-normal tracking-normal">(optional)</span></label>
                <div class="relative max-w-xs">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-sm font-semibold text-foreground-muted pointer-events-none">PKR</span>
                    <input id="create-budget" type="number" wire:model="budget" min="0" step="0.01" placeholder="0.00"
                        class="field p-3 pl-14 tabular-nums">
                </div>
                <p class="text-xs text-foreground-subtle mt-1.5">Enter your estimated budget in Pakistani Rupees</p>
                @error('budget') <span class="text-destructive text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Notes -->
            <div>
                <label for="create-notes" class="eyebrow text-foreground-subtle block mb-3">Notes <span class="normal-case font-normal tracking-normal">(optional)</span></label>
                <textarea id="create-notes" wire:model.live="notes" rows="4" maxlength="1000" class="field p-3"
                    placeholder="Anything to remember about this trip..."></textarea>
                <p class="text-xs text-foreground-subtle mt-1 text-right tabular-nums">{{ strlen($notes) }}/1000 characters</p>
                @error('notes') <span class="text-destructive text-sm block">{{ $message }}</span> @enderror
            </div>
        </div>
        @endif

        <!-- Step 4: Confirm -->
        @if($currentStep === 4)
        <div wire:key="step-4-content" class="space-y-6 fade-up">
            <div>
                <h2 class="text-2xl font-extrabold tracking-tight text-foreground mb-1.5">Review your trip</h2>
                <p class="text-foreground-muted">Everything look right? You can still edit any section.</p>
            </div>

            <div class="rounded-xl border border-border divide-y divide-border overflow-hidden">
                <!-- Destination -->
                <div class="flex items-start justify-between gap-4 p-5 bg-surface-card">
                    <div class="min-w-0">
                        <p class="eyebrow text-foreground-subtle mb-2">Destination</p>
                        <div class="flex items-center gap-3">
                            @if(isset($selectedCountry['flag']))
                            <img src="{{ $selectedCountry['flag'] }}" alt="{{ $destination }} flag" class="w-10 h-7 object-cover rounded shadow-sm shrink-0">
                            @endif
                            <span class="font-bold text-foreground truncate">{{ $destination }}</span>
                        </div>
                    </div>
                    <button wire:click="goToStep(1)" class="btn-ghost px-3.5 py-1.5 text-sm shrink-0">Edit</button>
                </div>

                <!-- Dates -->
                <div class="flex items-start justify-between gap-4 p-5 bg-surface-card">
                    <div class="min-w-0">
                        <p class="eyebrow text-foreground-subtle mb-2">Travel dates</p>
                        <p class="font-bold text-foreground tabular-nums">
                            {{ date('M j, Y', strtotime($startDate)) }} – {{ date('M j, Y', strtotime($endDate)) }}
                        </p>
                        @php $duration = (new DateTime($startDate))->diff(new DateTime($endDate))->days + 1; @endphp
                        <p class="text-sm text-foreground-muted tabular-nums">{{ $duration }} {{ $duration === 1 ? 'day' : 'days' }}</p>
                    </div>
                    <button wire:click="goToStep(2)" class="btn-ghost px-3.5 py-1.5 text-sm shrink-0">Edit</button>
                </div>

                <!-- Details -->
                <div class="flex items-start justify-between gap-4 p-5 bg-surface-card">
                    <div class="min-w-0 space-y-2">
                        <p class="eyebrow text-foreground-subtle">Trip details</p>
                        <p class="inline-flex items-center gap-1.5 font-bold text-foreground capitalize">
                            <span class="text-primary">@include('livewire.partials.wizard.type-icon', ['typeKey' => $type, 'class' => 'w-4 h-4'])</span>
                            {{ $tripTypes[$type]['label'] ?? $type }}
                        </p>
                        <p class="text-sm text-foreground-muted tabular-nums">{{ $travelers }} {{ $travelers === 1 ? 'person' : 'people' }}
                            @if($budget) · PKR {{ number_format((float) $budget, 2) }} @endif
                        </p>
                        @if($notes)
                        <p class="text-sm text-foreground-muted">{{ $notes }}</p>
                        @endif
                    </div>
                    <button wire:click="goToStep(3)" class="btn-ghost px-3.5 py-1.5 text-sm shrink-0">Edit</button>
                </div>
            </div>
        </div>
        @endif

        <!-- Navigation Buttons -->
        <div wire:key="nav-buttons-step-{{ $currentStep }}" class="flex items-center justify-between mt-8 pt-6 border-t border-border">
            @if($currentStep > 1)
            <button wire:key="prev-btn-{{ $currentStep }}" wire:click="previousStep" class="btn-ghost px-6 py-3">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Back
            </button>
            @else
            <a href="{{ route('dashboard') }}" wire:navigate class="btn-ghost px-6 py-3">Cancel</a>
            @endif

            @if($currentStep < 4)
            <button wire:key="next-btn-{{ $currentStep }}" wire:click="nextStep" class="btn-primary px-6 py-3">
                Next
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
            @else
            <button wire:key="create-btn" type="button" wire:click="createTrip" x-on:click="creating = true"
                wire:loading.attr="disabled" wire:loading.class="opacity-50 cursor-not-allowed" wire:target="createTrip"
                class="inline-flex items-center gap-2 px-8 py-3 bg-accent text-accent-foreground rounded-xl font-semibold hover:bg-accent-light active:scale-[0.98] transition-[background-color,transform] duration-150 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer focus-ring">
                <svg wire:loading.remove wire:target="createTrip" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <x-ui.spinner wire:loading wire:target="createTrip" size="md" />
                <span wire:loading.remove wire:target="createTrip">Create Trip</span>
                <span wire:loading wire:target="createTrip">Creating...</span>
            </button>
            @endif
        </div>
    </div>

    <!-- Live trip ticket (desktop rail) -->
    <aside class="hidden lg:block lg:sticky lg:top-24" aria-label="Trip summary">
        @include('livewire.partials.wizard.ticket')
    </aside>

    </div>

    <!-- Help Text -->
    <div class="mt-6 text-center">
        <p class="text-sm text-foreground-subtle">
            Your progress is automatically saved. You can safely navigate away and return later.
        </p>
    </div>

    <!-- Full-screen overlay while the trip is being created. Driven by Alpine,
         not wire:loading, so it survives past the Livewire response and stays
         up through the redirect/navigation to the new trip's page. Hidden only
         when the server says the save did not go through. -->
    <div x-show="creating" style="display: none"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
         role="status" aria-live="polite">
        <div class="bg-surface-card rounded-2xl shadow-xl border border-border p-8 text-center max-w-sm mx-4">
            <x-ui.spinner size="lg" class="mx-auto text-primary" />
            <h3 class="mt-4 text-lg font-bold text-foreground">Creating your trip…</h3>
            <p class="mt-1 text-sm text-foreground-muted">Setting things up and preparing your packing list.</p>
        </div>
    </div>
</div>
