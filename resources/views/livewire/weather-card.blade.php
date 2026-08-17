<div x-data="{ unit: '{{ $units === 'imperial' ? 'imperial' : 'metric' }}', toggle(){ this.unit = this.unit === 'metric' ? 'imperial' : 'metric'; $wire.dispatch('weather-unit-changed', { unit: this.unit }); } }" class="max-w-5xl mx-auto">
    <div class="mb-8">
        <h1 class="page-title mb-1.5">Weather</h1>
        <p class="text-foreground-muted">Current conditions and forecast for your destination</p>
    </div>

    <form wire:submit="search" class="mb-6 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <input
                type="text"
                wire:model="query"
                class="field p-3 pl-10"
                placeholder="Search any city, e.g. Tokyo or Istanbul..."
                aria-label="Search a city's weather"
            >
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-foreground-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="btn-primary px-5" wire:loading.attr="disabled" wire:target="search">
                <span wire:loading.remove wire:target="search">Search</span>
                <span wire:loading wire:target="search" class="inline-flex items-center gap-2">
                    <x-ui.spinner />
                    Searching
                </span>
            </button>
            @if(!$autoDetected)
                <button type="button" wire:click="useMyLocation" class="btn-ghost px-4 inline-flex items-center gap-1.5" wire:loading.attr="disabled" wire:target="useMyLocation">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4" aria-hidden="true">
                        <path fill-rule="evenodd" d="m9.69 18.933.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 0 0 .281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 1 0 3 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 0 0 2.273 1.765 11.842 11.842 0 0 0 .976.544l.062.029.018.008.006.003ZM10 11.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" clip-rule="evenodd" />
                    </svg>
                    My location
                </button>
            @endif
        </div>
    </form>

    <div class="relative overflow-hidden rounded-2xl p-6 mb-6 bg-gradient-to-br from-primary/15 via-secondary/10 to-accent/10 border border-border shadow-card">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <h2 class="section-title">{{ $locationLabel !== '' ? $locationLabel : $location }}</h2>
                    @if($autoDetected)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary border border-primary/20" title="Detected from your IP address">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-3.5 h-3.5" aria-hidden="true">
                                <path fill-rule="evenodd" d="m9.69 18.933.003.001C9.89 19.02 10 19 10 19s.11.02.308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 0 0 .281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 1 0 3 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 0 0 2.273 1.765 11.842 11.842 0 0 0 .976.544l.062.029.018.008.006.003ZM10 11.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5Z" clip-rule="evenodd" />
                            </svg>
                            Your location
                        </span>
                    @endif
                </div>
                @if($current)
                    <p class="text-sm text-foreground-muted capitalize mt-0.5">{{ $current['condition'] ?? '' }}</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <button type="button" @click="toggle()" class="px-3.5 py-1.5 rounded-full text-sm font-semibold bg-surface-card border border-border hover:border-border-strong text-foreground active:scale-[0.97] transition-[border-color,transform] duration-150 cursor-pointer focus-ring">
                    <span x-text="unit === 'metric' ? '°C' : '°F'"></span>
                </button>
            </div>
        </div>

        {{-- Every request in this component is a weather fetch (search, My
             location, unit toggle via dispatch), so the loading state is
             untargeted on purpose. --}}
        <div wire:loading.delay.grid class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-6 items-center animate-pulse" aria-hidden="true">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-surface-muted dark:bg-surface"></div>
                <div class="space-y-2">
                    <div class="h-10 w-24 rounded bg-surface-muted dark:bg-surface"></div>
                    <div class="h-3 w-20 rounded bg-surface-muted dark:bg-surface"></div>
                </div>
            </div>
            <div class="space-y-2">
                <div class="h-4 w-36 rounded bg-surface-muted dark:bg-surface"></div>
                <div class="h-4 w-28 rounded bg-surface-muted dark:bg-surface"></div>
            </div>
            <div class="space-y-2">
                <div class="h-4 w-32 rounded bg-surface-muted dark:bg-surface"></div>
                <div class="h-3 w-24 rounded bg-surface-muted dark:bg-surface"></div>
            </div>
        </div>

        <div wire:loading.remove.delay>
        @if($current)
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-6 items-center">
            <div class="flex items-center gap-4">
                @if(!empty($current['icon']))
                    <img alt="icon" class="w-16 h-16" src="https://openweathermap.org/img/wn/{{ $current['icon'] }}@2x.png" />
                @endif
                <div>
                    <div class="text-5xl font-extrabold tracking-tight text-foreground tabular-nums">{{ round($current['temperature'] ?? 0) }}<span class="text-2xl align-super font-bold">°</span></div>
                    @if(isset($current['feels_like']))
                        <div class="text-xs text-foreground-muted">Feels like {{ round($current['feels_like']) }}°</div>
                    @endif
                </div>
            </div>
            <div class="space-y-1 text-foreground">
                <div class="text-sm">Humidity: <span class="font-medium">{{ $current['humidity'] ?? '-' }}%</span></div>
                <div class="text-sm">Wind: <span class="font-medium">{{ $current['wind_speed'] ?? '-' }} {{ $units === 'imperial' ? 'mph' : 'm/s' }}</span></div>
            </div>
            <div class="space-y-1">
                @if(isset($current['min_temp'], $current['max_temp']))
                    <div class="text-sm text-foreground">Today: <span class="font-medium">{{ round($current['min_temp']) }}° / {{ round($current['max_temp']) }}°</span></div>
                @endif
                @if(!empty($current['date']))
                    <div class="text-xs text-foreground-muted">As of {{ $current['date'] }}</div>
                @endif
            </div>
        </div>
        @endif

        @if(!$current)
            <p class="mt-4 text-sm text-foreground-muted">
                @if(trim($location) === '')
                    We couldn't detect your location automatically. Search a city above, or check your connection and refresh to try again.
                @else
                    No weather found for "{{ $locationLabel !== '' ? $locationLabel : $location }}". Check the spelling or try a nearby larger city.
                @endif
            </p>
        @endif
        </div>
    </div>

    <div wire:loading.delay.block class="rounded-2xl p-6 bg-gradient-to-br from-secondary/10 via-primary/5 to-transparent border border-border shadow-card bg-surface-card" aria-hidden="true">
        <h3 class="section-title text-lg mb-4">Forecast</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-4 animate-pulse">
            @for($i = 0; $i < 7; $i++)
                <div class="rounded-xl p-4 bg-surface-card border border-border text-center space-y-3">
                    <div class="h-3 w-8 mx-auto rounded bg-surface-muted dark:bg-surface"></div>
                    <div class="w-12 h-12 mx-auto rounded-full bg-surface-muted dark:bg-surface"></div>
                    <div class="h-4 w-10 mx-auto rounded bg-surface-muted dark:bg-surface"></div>
                    <div class="h-3 w-14 mx-auto rounded bg-surface-muted dark:bg-surface"></div>
                </div>
            @endfor
        </div>
    </div>

    <div wire:loading.remove.delay>
    @if(!empty($forecast))
    <div class="rounded-2xl p-6 bg-gradient-to-br from-secondary/10 via-primary/5 to-transparent border border-border shadow-card bg-surface-card">
        <h3 class="section-title text-lg mb-4">Forecast</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-4">
            @foreach($forecast as $index => $day)
                <div wire:key="forecast-{{ $index }}" class="group rounded-xl p-4 bg-surface-card border border-transparent hover:border-primary/40 transition-[border-color] duration-200 shadow-sm text-center">
                    <div class="text-sm text-foreground">{{ \Carbon\Carbon::parse($day['date'] ?? now())->format('D') }}</div>
                    @if(!empty($day['icon']))
                        <img alt="icon" class="w-12 h-12 mx-auto scale-100 group-hover:scale-105 transition" src="https://openweathermap.org/img/wn/{{ $day['icon'] }}@2x.png" />
                    @endif
                    <div class="mt-2 text-center">
                        <div class="text-base font-bold text-foreground tabular-nums">{{ round(($day['max_temp'] ?? $day['temperature'] ?? 0)) }}°</div>
                        <div class="text-xs text-foreground-muted">{{ round(($day['min_temp'] ?? $day['temperature'] ?? 0)) }}°</div>
                    </div>
                    <div class="mt-1 text-xs text-foreground-muted capitalize text-center truncate">{{ $day['condition'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
    </div>

    @if($current && !empty($current['alerts']))
    <div class="mt-6 rounded-xl p-4 border border-accent/25 bg-accent/10">
        <div class="flex items-center gap-2 text-accent font-semibold mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd" d="M8.485 2.495a1.5 1.5 0 0 1 3.03 0l6.364 11.727A1.5 1.5 0 0 1 16.485 16H3.515a1.5 1.5 0 0 1-1.394-2.278L8.485 2.495ZM11 13a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm-1-2a1 1 0 0 1-1-1V7a1 1 0 1 1 2 0v3a1 1 0 0 1-1 1Z" clip-rule="evenodd" />
            </svg>
            Weather Alerts
        </div>
        <ul class="space-y-3">
            @foreach($current['alerts'] as $index => $alert)
                <li wire:key="alert-{{ $index }}" class="text-sm">
                    <div class="font-medium text-foreground">{{ $alert['event'] ?? 'Alert' }}</div>
                    @if(!empty($alert['description']))
                        <div class="text-foreground-muted">{{ $alert['description'] }}</div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>
