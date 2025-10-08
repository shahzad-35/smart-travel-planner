<div class="max-w-7xl mx-auto" x-data="{ unit: '{{ $units === 'imperial' ? 'imperial' : 'metric' }}', toggle(){ this.unit = this.unit === 'metric' ? 'imperial' : 'metric'; $wire.updateUnits(this.unit); } }">
    <div class="mb-4 flex flex-wrap gap-3 items-center">
        <form wire:submit.prevent="addNewLocation" class="flex items-center gap-2">
            <input type="text" placeholder="Add location" class="border px-3 py-2 rounded w-64" wire:model.defer="newLocation" />
            <button type="submit" class="px-3 py-2 bg-indigo-600 text-white rounded">Add</button>
        </form>
        <button type="button" @click="toggle()" class="px-3 py-2 rounded-full text-sm bg-white/70 border hover:bg-white">Toggle <span class="ml-1" x-text="unit === 'metric' ? '°C' : '°F'"></span></button>
        @if($isLoading)
            <div class="text-sm text-gray-600">Loading...</div>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($locations as $loc)
            <div class="rounded-2xl p-4 bg-gradient-to-br from-sky-500/10 via-indigo-500/10 to-purple-500/10 border border-white/20 shadow">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold">{{ $loc }}</h3>
                    <button type="button" class="text-xs text-red-600" wire:click="removeLocation('{{ $loc }}')">Remove</button>
                </div>
                @php($data = $currentByLocation[$loc] ?? null)
                @if($data)
                    <div class="flex items-center gap-3">
                        @if(!empty($data['icon']))
                            <img alt="icon" class="w-10 h-10" src="https://openweathermap.org/img/wn/{{ $data['icon'] }}@2x.png" />
                        @endif
                        <div class="text-2xl font-bold">{{ round($data['temperature'] ?? 0) }}°</div>
                    </div>
                    <div class="mt-1 text-sm capitalize">{{ $data['condition'] ?? '' }}</div>
                    <div class="text-xs text-gray-700">Humidity {{ $data['humidity'] ?? '-' }}% · Wind {{ $data['wind_speed'] ?? '-' }} {{ $units === 'imperial' ? 'mph' : 'm/s' }}</div>
                    @if(isset($errorsByLocation[$loc]))
                        <div class="mt-1 text-xs text-red-600">{{ $errorsByLocation[$loc] }}</div>
                    @endif
                @else
                    <div class="text-sm text-gray-600">No data</div>
                @endif
            </div>
        @endforeach
    </div>
</div>


