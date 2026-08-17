<div class="card p-6">
    <div class="flex flex-wrap gap-3 justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-foreground tracking-tight">Travel Statistics</h2>
        <div class="flex gap-2">
            <button wire:click="refreshStats" wire:loading.attr="disabled" wire:target="refreshStats" class="disabled:opacity-60 inline-flex items-center gap-2 px-3.5 py-2 text-sm font-medium text-foreground-muted hover:text-foreground border border-border hover:border-border-strong rounded-lg transition-colors cursor-pointer focus-ring">
                <svg class="w-4 h-4" wire:loading.class="animate-spin" wire:target="refreshStats" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                </svg>
                Refresh
            </button>
            <button wire:click="downloadPdf" wire:loading.attr="disabled" wire:target="downloadPdf" class="inline-flex items-center gap-2 px-3.5 py-2 text-sm font-semibold bg-primary text-primary-foreground rounded-lg hover:bg-primary-dark transition-colors shadow-sm cursor-pointer focus-ring disabled:opacity-60">
                <x-ui.spinner wire:loading wire:target="downloadPdf" />
                <svg wire:loading.remove wire:target="downloadPdf" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span wire:loading.remove wire:target="downloadPdf">Export PDF</span>
                <span wire:loading wire:target="downloadPdf">Preparing…</span>
            </button>
        </div>
    </div>

    <!-- Refresh skeleton -->
    <div wire:loading.delay.block wire:target="refreshStats" class="animate-pulse space-y-8" aria-hidden="true">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @for($i = 0; $i < 4; $i++)
                <div class="h-28 bg-surface-muted dark:bg-surface rounded-xl"></div>
            @endfor
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="h-64 bg-surface-muted dark:bg-surface rounded-xl"></div>
            <div class="h-64 bg-surface-muted dark:bg-surface rounded-xl"></div>
        </div>
        <div class="h-40 bg-surface-muted dark:bg-surface rounded-xl"></div>
    </div>

    <div wire:loading.remove.delay wire:target="refreshStats">
    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8 stagger-grid">
        <x-ui.stat-card color="primary" label="Total Trips" :value="$stats['total_trips'] ?? 0"
            :hint="($stats['completed_trips'] ?? 0) . ' completed · ' . ($stats['planned_trips'] ?? 0) . ' planned'">
            <x-slot name="icon"><svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg></x-slot>
        </x-ui.stat-card>

        <x-ui.stat-card color="secondary" label="Countries Visited" :value="$stats['unique_countries'] ?? 0" hint="across all trips">
            <x-slot name="icon"><svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-slot>
        </x-ui.stat-card>

        <x-ui.stat-card color="accent" label="Travel Days" :value="$stats['total_travel_days'] ?? 0" hint="days on the road">
            <x-slot name="icon"><svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></x-slot>
        </x-ui.stat-card>

        <x-ui.stat-card color="neutral" label="Top Destination"
            :value="$stats['top_destinations'][0]['destination'] ?? 'N/A'"
            :title="$stats['top_destinations'][0]['destination'] ?? 'N/A'"
            :hint="$stats['most_common_trip_type'] ?? 'no trips yet'"
            class="[&>p]:text-xl">
            <x-slot name="icon"><svg class="w-16 h-16" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg></x-slot>
        </x-ui.stat-card>
    </div>

    <!-- Charts Area (x-init re-runs whenever this HTML is morphed in, incl. lazy load) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8"
        data-stats="{{ json_encode($stats) }}"
        x-data
        x-init="window.initTravelStatsCharts($el)"
        x-on:stats-refreshed.window="$el.dataset.stats = JSON.stringify($event.detail.stats ?? $event.detail[0]?.stats ?? {}); window.initTravelStatsCharts($el)">
        <!-- Status Distribution -->
        <div class="bg-surface-card p-5 border border-border rounded-xl">
            <h3 class="font-semibold text-foreground mb-4">Trip Status Distribution</h3>
            <div class="relative h-64 w-full">
                <canvas id="statusChart" role="img" aria-label="Doughnut chart of trips by status"></canvas>
            </div>
        </div>

        <!-- Travel Timeline -->
        <div class="bg-surface-card p-5 border border-border rounded-xl">
            <h3 class="font-semibold text-foreground mb-4">Travel Timeline <span class="font-normal text-foreground-subtle">(trips per year)</span></h3>
            <div class="relative h-64 w-full">
                <canvas id="timelineChart" role="img" aria-label="Bar chart of trips per year"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Destinations List -->
    <div class="bg-surface-card border border-border rounded-xl p-5">
        <h3 class="font-semibold text-foreground mb-4">Top 5 Destinations</h3>
        @php($maxVisits = max(1, collect($stats['top_destinations'] ?? [])->max('count') ?? 1))
        @forelse($stats['top_destinations'] ?? [] as $i => $dest)
            <div class="flex items-center gap-4 py-2.5 {{ !$loop->last ? 'border-b border-border' : '' }}">
                <span class="w-7 h-7 shrink-0 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center">{{ $i + 1 }}</span>
                <span class="w-32 sm:w-44 shrink-0 text-sm font-medium text-foreground truncate">{{ $dest['destination'] }}</span>
                <div class="flex-1 h-2 rounded-full bg-surface-muted dark:bg-surface overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-primary to-primary-light" style="width: {{ round($dest['count'] / $maxVisits * 100) }}%"></div>
                </div>
                <span class="w-14 shrink-0 text-right text-sm text-foreground-muted tabular-nums">{{ $dest['count'] }} {{ Str::plural('visit', $dest['count']) }}</span>
            </div>
        @empty
            <p class="py-6 text-center text-sm text-foreground-muted">No trips yet — your most-visited places will appear here.</p>
        @endforelse
    </div>
    </div>

</div>
