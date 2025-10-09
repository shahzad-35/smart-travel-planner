<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold">Country Info</h1>
        @if($country)
            <p class="text-sm text-gray-600 mt-1">{{ $country['name'] }} ({{ $country['code'] }})</p>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow border">
        <div class="border-b px-4 py-3">
            <nav class="flex gap-4" aria-label="Tabs">
                <button wire:click="$set('tab','info')" class="px-3 py-2 text-sm font-medium rounded-md {{ $tab==='info' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">Info</button>
                <button wire:click="$set('tab','holidays')" class="px-3 py-2 text-sm font-medium rounded-md {{ $tab==='holidays' ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">Holidays</button>
            </nav>
        </div>
        <div class="p-4">
            @if(!$country)
                <div class="text-gray-600">Enter a country code or name in the URL query (?code=US) to view details.</div>
            @else
                @if($tab==='info')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 space-y-4">
                            <div class="flex items-center gap-4">
                                @if($country['flag'])
                                    <img src="{{ $country['flag'] }}" alt="Flag" class="h-10 w-auto border rounded"/>
                                @endif
                                <div>
                                    <div class="text-lg font-semibold">{{ $country['name'] }}</div>
                                    <div class="text-sm text-gray-600">Code: {{ $country['code'] }}</div>
                                </div>
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm text-gray-500">Capital</dt>
                                    <dd class="font-medium">{{ $country['capital'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Currency</dt>
                                    <dd class="font-medium">{{ $country['currency'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Languages</dt>
                                    <dd class="font-medium">{{ implode(', ', $country['languages']) ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Timezone</dt>
                                    <dd class="font-medium">{{ $country['timezone'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-gray-500">Population</dt>
                                    <dd class="font-medium">{{ number_format($country['population']) }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <div id="map" class="w-full h-64 rounded border"></div>
                            @push('scripts')
                                <script>
                                    if (!window.mapScriptLoaded) {
                                        window.mapScriptLoaded = true;
                                        document.addEventListener('livewire:navigated', initMap);
                                        document.addEventListener('DOMContentLoaded', initMap);
                                        function initMap(){
                                            const lat = @json($country['latitude']);
                                            const lng = @json($country['longitude']);
                                            if(!lat || !lng) return;
                                            if(!window.L){
                                                const link = document.createElement('link');
                                                link.rel = 'stylesheet';
                                                link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                                                document.head.appendChild(link);
                                                const script = document.createElement('script');
                                                script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                                                script.onload = () => renderMap(lat,lng);
                                                document.body.appendChild(script);
                                            } else {
                                                renderMap(lat,lng);
                                            }
                                        }
                                        function renderMap(lat,lng){
                                            const container = document.getElementById('map');
                                            if (container._leaflet_id) {
                                                // Map already initialized
                                                return;
                                            }
                                            const map = L.map('map').setView([lat,lng], 5);
                                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                maxZoom: 19,
                                                attribution: '&copy; OpenStreetMap'
                                            }).addTo(map);
                                            L.marker([lat,lng]).addTo(map);
                                        }
                                    }
                                </script>
                            @endpush
                        </div>
                    </div>
                @else
                    <livewire:holiday-list :code="$country['code']" :start-date="$startDate" :end-date="$endDate" :trip-id="$tripId" />
                @endif
            @endif
        </div>
    </div>
</div>


