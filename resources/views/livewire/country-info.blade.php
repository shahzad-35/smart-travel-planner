<div class="max-w-6xl mx-auto">
    <div class="mb-6">
        <h1 class="page-title">Country Info</h1>
        @if($country)
            <p class="text-foreground-muted mt-1.5">{{ $country['name'] }} ({{ $country['code'] }})</p>
        @endif
    </div>

    <div class="card">
        <div class="border-b border-border px-4 py-3">
            <nav class="flex gap-4" aria-label="Tabs">
                <button wire:click="$set('tab','info')" class="px-4 py-2 text-sm font-semibold rounded-lg cursor-pointer active:scale-[0.98] transition-[background-color,color,transform] duration-150 {{ $tab==='info' ? 'bg-primary/10 text-primary' : 'text-foreground-muted hover:text-foreground hover:bg-surface-muted' }}">Info</button>
                <button wire:click="$set('tab','holidays')" class="px-4 py-2 text-sm font-semibold rounded-lg cursor-pointer active:scale-[0.98] transition-[background-color,color,transform] duration-150 {{ $tab==='holidays' ? 'bg-primary/10 text-primary' : 'text-foreground-muted hover:text-foreground hover:bg-surface-muted' }}">Holidays</button>
            </nav>
        </div>
        <div class="p-6" wire:loading.delay.class="opacity-40 pointer-events-none" wire:target="tab">
            @if(!$country)
                <div class="aurora-bg rounded-xl text-center py-12 px-6">
                    <div class="w-14 h-14 mx-auto mb-4 bg-primary/10 rounded-2xl flex items-center justify-center">
                        <svg class="h-7 w-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-foreground mb-1.5">Pick a country to explore</h3>
                    <p class="text-foreground-muted max-w-sm mx-auto">Search a destination from the <a href="{{ route('destinations') }}" wire:navigate class="text-primary font-semibold hover:underline">Destinations</a> page, or add <code class="text-xs bg-surface-muted px-1.5 py-0.5 rounded">?code=JP</code> to the URL.</p>
                </div>
            @else
                @if($tab =='info')
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-2 space-y-4">
                            <div class="flex items-center gap-4">
                                @if($country['flag'])
                                    <img src="{{ $country['flag'] }}" alt="{{ $country['name'] }} flag" class="h-12 w-auto rounded-lg shadow-sm border border-border"/>
                                @endif
                                <div>
                                    <div class="text-xl font-extrabold tracking-tight text-foreground">{{ $country['name'] }}</div>
                                    <div class="text-sm text-foreground-muted">Code: {{ $country['code'] }}</div>
                                </div>
                            </div>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <dt class="eyebrow mb-0.5">Capital</dt>
                                    <dd class="font-semibold text-foreground">{{ $country['capital'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="eyebrow mb-0.5">Currency</dt>
                                    <dd class="font-semibold text-foreground">{{ $country['currency'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="eyebrow mb-0.5">Languages</dt>
                                    <dd class="font-semibold text-foreground">
                                        {{ implode(', ', array_column($country['languages'], 'name')) ?: '—' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="eyebrow mb-0.5">Timezone</dt>
                                    <dd class="font-semibold text-foreground">{{ $country['timezone'] ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="eyebrow mb-0.5">Population</dt>
                                    <dd class="font-semibold text-foreground"><span class="tabular-nums">{{ number_format($country['population']) }}</span></dd>
                                </div>
                            </dl>
                        </div>
                        <div>
                            <div id="map" class="w-full h-64 rounded-xl border border-border overflow-hidden" style="min-height: 256px;"></div>
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
                    <livewire:holiday-list :code="$country['code']" :start-date="$startDate" :end-date="$endDate" :trip-id="$tripId" lazy />
                @endif
            @endif
        </div>
    </div>
</div>
