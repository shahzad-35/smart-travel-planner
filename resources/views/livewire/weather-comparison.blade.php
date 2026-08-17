<div class="max-w-7xl mx-auto" x-data="{ unit: '{{ $units === 'imperial' ? 'imperial' : 'metric' }}', toggle(){ this.unit = this.unit === 'metric' ? 'imperial' : 'metric'; $wire.updateUnits(this.unit); } }">
    <div class="mb-8">
        <h1 class="page-title mb-1.5">Compare Weather</h1>
        <p class="text-foreground-muted">Saved locations side by side, updated live</p>
    </div>

    <div class="mb-6 flex flex-wrap gap-3 items-center">
        <form wire:submit.prevent="addNewLocation" class="flex items-center gap-2">
            <input type="text" placeholder="Add a location…" class="field w-64 px-3.5 py-2.5" wire:model="newLocation" />
            <button type="submit" class="btn-primary px-4 py-2.5 text-sm inline-flex items-center gap-2" wire:loading.attr="disabled" wire:target="addNewLocation">
                <x-ui.spinner wire:loading.delay wire:target="addNewLocation" />
                Add
            </button>
        </form>
        <button type="button" @click="toggle()" wire:loading.attr="disabled" wire:target="updateUnits"
            class="px-3.5 py-2 rounded-full text-sm font-semibold bg-surface-card border border-border hover:border-border-strong text-foreground active:scale-[0.97] transition-[border-color,transform] duration-150 cursor-pointer focus-ring">
            <span x-text="unit === 'metric' ? '°C' : '°F'"></span>
        </button>
        <div wire:loading.delay.flex wire:target="updateUnits, addNewLocation" class="flex items-center gap-2 text-sm text-foreground-muted">
            <x-ui.spinner />
            Updating…
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
         wire:loading.delay.class="opacity-40 pointer-events-none" wire:target="updateUnits">
        @foreach($locations as $loc)
            <div class="rounded-2xl p-5 bg-gradient-to-br from-primary/10 via-transparent to-secondary/10 bg-surface-card border border-border shadow-card">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-bold text-foreground">{{ $loc }}</h3>
                    <button type="button" class="text-xs text-destructive hover:text-destructive/80 cursor-pointer disabled:opacity-50" wire:click="removeLocation('{{ $loc }}')" wire:loading.attr="disabled" wire:target="removeLocation">Remove</button>
                </div>
                @php($data = $currentByLocation[$loc] ?? null)
                @if($data)
                    <div class="flex items-center gap-3">
                        @if(!empty($data['icon']))
                            <img alt="icon" class="w-10 h-10" src="https://openweathermap.org/img/wn/{{ $data['icon'] }}@2x.png" />
                        @endif
                        <div class="text-3xl font-extrabold tracking-tight text-foreground tabular-nums">{{ round($data['temperature'] ?? 0) }}°</div>
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

        {{-- Placeholder card for the location being added --}}
        <div wire:loading.delay.block wire:target="addNewLocation" class="rounded-2xl p-5 bg-surface-card border border-border shadow-card animate-pulse" aria-hidden="true">
            <div class="h-4 w-28 rounded bg-surface-muted dark:bg-surface mb-4"></div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-surface-muted dark:bg-surface"></div>
                <div class="h-8 w-16 rounded bg-surface-muted dark:bg-surface"></div>
            </div>
            <div class="mt-3 h-3 w-32 rounded bg-surface-muted dark:bg-surface"></div>
        </div>
    </div>
</div>
