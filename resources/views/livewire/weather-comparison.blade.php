<div class="max-w-7xl mx-auto" x-data="{ unit: '{{ $units === 'imperial' ? 'imperial' : 'metric' }}', toggle(){ this.unit = this.unit === 'metric' ? 'imperial' : 'metric'; $wire.updateUnits(this.unit); } }">
    <div class="mb-4 flex flex-wrap gap-3 items-center">
        <form wire:submit.prevent="addNewLocation" class="flex items-center gap-2">
            <input type="text" placeholder="Add location" class="border border-border bg-surface-card text-foreground px-3 py-2 rounded w-64 focus:ring-primary focus:border-primary" wire:model="newLocation" />
            <button type="submit" class="px-3 py-2 bg-primary text-primary-foreground rounded hover:bg-primary-dark cursor-pointer">Add</button>
        </form>
        <button type="button" @click="toggle()" class="px-3 py-2 rounded-full text-sm bg-surface-card/70 border border-border text-foreground hover:bg-surface-card cursor-pointer">Toggle <span class="ml-1" x-text="unit === 'metric' ? '°C' : '°F'"></span></button>
        @if($isLoading)
            <div class="text-sm text-foreground-muted">Loading...</div>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($locations as $loc)
            <div class="glass rounded-2xl p-4 bg-gradient-to-br from-teal-500/10 via-emerald-500/10 to-cyan-500/10 border border-border shadow-card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-foreground">{{ $loc }}</h3>
                    <button type="button" class="text-xs text-destructive hover:text-destructive/80 cursor-pointer" wire:click="removeLocation('{{ $loc }}')">Remove</button>
                </div>
                @php($data = $currentByLocation[$loc] ?? null)
                @if($data)
                    <div class="flex items-center gap-3">
                        @if(!empty($data['icon']))
                            <img alt="icon" class="w-10 h-10" src="https://openweathermap.org/img/wn/{{ $data['icon'] }}@2x.png" />
                        @endif
                        <div class="text-2xl font-bold text-foreground">{{ round($data['temperature'] ?? 0) }}°</div>
                    </div>
                    <div class="mt-1 text-sm capitalize text-foreground-muted">{{ $data['condition'] ?? '' }}</div>
                    <div class="text-xs text-foreground-muted">Humidity {{ $data['humidity'] ?? '-' }}% · Wind {{ $data['wind_speed'] ?? '-' }} {{ $units === 'imperial' ? 'mph' : 'm/s' }}</div>
                    @if(isset($errorsByLocation[$loc]))
                        <div class="mt-1 text-xs text-destructive">{{ $errorsByLocation[$loc] }}</div>
                    @endif
                @else
                    <div class="text-sm text-foreground-muted">No data</div>
                    @if(isset($errorsByLocation[$loc]))
                        <div class="mt-1 text-xs text-destructive">{{ $errorsByLocation[$loc] }}</div>
                    @endif
                @endif
            </div>
        @endforeach
    </div>
</div>
