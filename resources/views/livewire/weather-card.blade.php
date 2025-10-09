<div x-data="{ unit: '{{ $units === 'imperial' ? 'imperial' : 'metric' }}', toggle(){ this.unit = this.unit === 'metric' ? 'imperial' : 'metric'; $wire.dispatch('weather-unit-changed', this.unit); } }" class="max-w-5xl mx-auto">
    <div class="rounded-2xl p-6 mb-6 bg-gradient-to-br from-sky-500/10 via-indigo-500/10 to-purple-500/10 border border-white/20 shadow-lg backdrop-blur">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold">{{ $location }}</h2>
                @if($current)
                    <p class="text-sm text-gray-600 capitalize">{{ $current['condition'] ?? '' }}</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <button type="button" @click="toggle()" class="px-3 py-1 rounded-full text-sm bg-white/60 hover:bg-white/80 transition">
                    <span x-text="unit === 'metric' ? '°C' : '°F'"></span>
                </button>
            </div>
        </div>

        @if($current)
        <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-6 items-center">
            <div class="flex items-center gap-4">
                @if(!empty($current['icon']))
                    <img alt="icon" class="w-16 h-16" src="https://openweathermap.org/img/wn/{{ $current['icon'] }}@2x.png" />
                @endif
                <div>
                    <div class="text-4xl font-bold">{{ round($current['temperature'] ?? 0) }}<span class="text-xl align-super">°</span></div>
                    @if(isset($current['feels_like']))
                        <div class="text-xs text-gray-600">Feels like {{ round($current['feels_like']) }}°</div>
                    @endif
                </div>
            </div>
            <div class="space-y-1">
                <div class="text-sm">Humidity: <span class="font-medium">{{ $current['humidity'] ?? '-' }}%</span></div>
                <div class="text-sm">Wind: <span class="font-medium">{{ $current['wind_speed'] ?? '-' }} {{ $units === 'imperial' ? 'mph' : 'm/s' }}</span></div>
            </div>
            <div class="space-y-1">
                @if(isset($current['min_temp'], $current['max_temp']))
                    <div class="text-sm">Today: <span class="font-medium">{{ round($current['min_temp']) }}° / {{ round($current['max_temp']) }}°</span></div>
                @endif
                @if(!empty($current['date']))
                    <div class="text-xs text-gray-600">As of {{ $current['date'] }}</div>
                @endif
            </div>
        </div>
        @endif
    </div>

    @if(!empty($forecast))
    <div class="rounded-2xl p-6 bg-gradient-to-br from-blue-500/10 via-cyan-500/10 to-emerald-500/10 border border-white/20 shadow">
        <h3 class="text-lg font-semibold mb-4">7-Day Forecast</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-4">
            @foreach($forecast as $day)
                <div class="group rounded-xl p-4 bg-white/60 hover:bg-white/80 transition shadow-sm">
                    <div class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($day['date'] ?? now())->format('D') }}</div>
                    @if(!empty($day['icon']))
                        <img alt="icon" class="w-12 h-12 mx-auto scale-100 group-hover:scale-105 transition" src="https://openweathermap.org/img/wn/{{ $day['icon'] }}@2x.png" />
                    @endif
                    <div class="mt-2 text-center">
                        <div class="text-base font-semibold">{{ round(($day['max_temp'] ?? $day['temperature'] ?? 0)) }}°</div>
                        <div class="text-xs text-gray-600">{{ round(($day['min_temp'] ?? $day['temperature'] ?? 0)) }}°</div>
                    </div>
                    <div class="mt-1 text-xs text-gray-700 capitalize text-center truncate">{{ $day['condition'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    @if($current && !empty($current['alerts']))
    <div class="mt-6 rounded-xl p-4 border border-yellow-300 bg-yellow-50">
        <div class="flex items-center gap-2 text-yellow-800 font-semibold mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd" d="M8.485 2.495a1.5 1.5 0 0 1 3.03 0l6.364 11.727A1.5 1.5 0 0 1 16.485 16H3.515a1.5 1.5 0 0 1-1.394-2.278L8.485 2.495ZM11 13a1 1 0 1 1-2 0 1 1 0 0 1 2 0Zm-1-2a1 1 0 0 1-1-1V7a1 1 0 1 1 2 0v3a1 1 0 0 1-1 1Z" clip-rule="evenodd" />
            </svg>
            Weather Alerts
        </div>
        <ul class="space-y-3">
            @foreach($current['alerts'] as $alert)
                <li class="text-sm">
                    <div class="font-medium">{{ $alert['event'] ?? 'Alert' }}</div>
                    @if(!empty($alert['description']))
                        <div class="text-gray-700">{{ $alert['description'] }}</div>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    @endif
</div>


