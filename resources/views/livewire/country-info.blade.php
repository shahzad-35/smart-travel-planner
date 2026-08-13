<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-foreground">Country Info</h1>
        @if($country)
            <p class="text-sm text-foreground-muted mt-1">{{ $country['name'] }} ({{ $country['code'] }})</p>
        @endif
    </div>

    <div class="card">
        <div class="border-b border-border px-4 py-3">
            <nav class="flex gap-4" aria-label="Tabs">
                <button wire:click="$set('tab','info')" class="px-3 py-2 text-sm font-medium rounded-md cursor-pointer {{ $tab==='info' ? 'bg-primary/10 text-primary' : 'text-foreground-muted hover:text-foreground' }}">Info</button>
                <button wire:click="$set('tab','holidays')" class="px-3 py-2 text-sm font-medium rounded-md cursor-pointer {{ $tab==='holidays' ? 'bg-primary/10 text-primary' : 'text-foreground-muted hover:text-foreground' }}">Holidays</button>
            </nav>
        </div>
        <div class="p-4">
            @if(!$country)
                <div class="text-foreground-muted">Enter a country code or name in the URL query (?code=US) to view details.</div>
            @else
                @if($tab =='info')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 space-y-4">
                            <div class="flex items-center gap-4">
                                @if($country['flag'])
                                    <img src="{{ $country['flag'] }}" alt="Flag" class="h-10 w-auto border border-border rounded"/>
                                @endif
                                <div>
                                    <div class="text-lg font-semibold text-foreground">{{ $country['name'] }}</div>
                                    <div class="text-sm text-foreground-muted">Code: {{ $country['code'] }}</div>
                                </div>
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm text-foreground-muted">Capital</dt>
                                    <dd class="font-medium text-foreground">{{ $country['capital'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-foreground-muted">Currency</dt>
                                    <dd class="font-medium text-foreground">{{ $country['currency'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-foreground-muted">Languages</dt>
                                    <dd class="font-medium text-foreground">
                                        {{ implode(', ', array_column($country['languages'], 'name')) ?: '—' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-foreground-muted">Timezone</dt>
                                    <dd class="font-medium text-foreground">{{ $country['timezone'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm text-foreground-muted">Population</dt>
                                    <dd class="font-medium text-foreground">{{ number_format($country['population']) }}</dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <div id="map" class="w-full h-64 rounded border border-border" style="min-height: 256px;"></div>
                            <script>
                                (function() {
                                    function initMap() {
                                        const container = document.getElementById('map');
                                        if (!container || container._leaflet_id) return;

                                        const lat = @json($country['latitude']);
                                        const lng = @json($country['longitude']);

                                        if (!lat || !lng || lat === 0 || lng === 0) {
                                            console.warn('Invalid coordinates for map:', lat, lng);
                                            return;
                                        }

                                        if (typeof L === 'undefined') {
                                            const link = document.createElement('link');
                                            link.rel = 'stylesheet';
                                            link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                                            document.head.appendChild(link);

                                            const script = document.createElement('script');
                                            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                                            script.onload = function() {
                                                renderMap(lat, lng);
                                            };
                                            document.head.appendChild(script);
                                        } else {
                                            renderMap(lat, lng);
                                        }
                                    }

                                    function renderMap(lat, lng) {
                                        const container = document.getElementById('map');
                                        if (!container || container._leaflet_id) return;

                                        try {
                                            const map = L.map('map').setView([lat, lng], 4);
                                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                maxZoom: 18,
                                                attribution: '&copy; OpenStreetMap contributors'
                                            }).addTo(map);
                                            L.marker([lat, lng]).addTo(map);

                                            setTimeout(() => {
                                                map.invalidateSize();
                                            }, 100);
                                        } catch (error) {
                                            console.error('Error initializing map:', error);
                                        }
                                    }

                                    if (document.readyState === 'loading') {
                                        document.addEventListener('DOMContentLoaded', initMap);
                                    } else {
                                        initMap();
                                    }

                                    document.addEventListener('livewire:navigated', initMap);
                                })();
                            </script>
                        </div>
                    </div>
                @else
                    <livewire:holiday-list :code="$country['code']" :start-date="$startDate" :end-date="$endDate" :trip-id="$tripId" />
                @endif
            @endif
        </div>
    </div>
</div>
