<div class="card p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-foreground">Travel Statistics</h2>
        <div class="flex gap-2">
            <button wire:click="refreshStats" class="px-4 py-2 bg-surface-muted dark:bg-surface text-foreground rounded-lg hover:bg-border transition-colors cursor-pointer">
                Refresh
            </button>
            <button wire:click="downloadPdf" class="px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary-dark transition-colors cursor-pointer">
                Export PDF
            </button>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-primary/10 p-4 rounded-lg shadow-sm">
            <h3 class="text-foreground-muted text-sm">Total Trips</h3>
            <p class="text-3xl font-bold text-primary">{{ $stats['total_trips'] ?? 0 }}</p>
            <div class="text-xs text-foreground-subtle mt-1">
                {{ $stats['completed_trips'] ?? 0 }} Completed • {{ $stats['planned_trips'] ?? 0 }} Planned
            </div>
        </div>

        <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-lg shadow-sm">
            <h3 class="text-foreground-muted text-sm">Countries Visited</h3>
            <p class="text-3xl font-bold text-emerald-700 dark:text-emerald-400">{{ $stats['unique_countries'] ?? 0 }}</p>
        </div>

        <div class="bg-purple-50 dark:bg-purple-900/20 p-4 rounded-lg shadow-sm">
            <h3 class="text-foreground-muted text-sm">Travel Days</h3>
            <p class="text-3xl font-bold text-purple-700 dark:text-purple-400">{{ $stats['total_travel_days'] ?? 0 }}</p>
        </div>

        <div class="bg-accent/10 p-4 rounded-lg shadow-sm">
            <h3 class="text-foreground-muted text-sm">Top Destination</h3>
            <p class="text-xl font-bold text-accent truncate" title="{{ $stats['top_destinations'][0]['destination'] ?? 'N/A' }}">
                {{ $stats['top_destinations'][0]['destination'] ?? 'N/A' }}
            </p>
            <div class="text-xs text-foreground-subtle mt-1">
                {{ $stats['most_common_trip_type'] ?? 'N/A' }}
            </div>
        </div>
    </div>

    <!-- Charts Area -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Status Distribution -->
        <div class="bg-surface-card p-4 border border-border rounded-lg shadow-sm">
            <h3 class="font-semibold text-foreground mb-4 text-center">Trip Status Distribution</h3>
            <div class="relative h-64 w-full">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Travel Timeline -->
        <div class="bg-surface-card p-4 border border-border rounded-lg shadow-sm">
            <h3 class="font-semibold text-foreground mb-4 text-center">Travel Timeline (Trips per Year)</h3>
            <div class="relative h-64 w-full">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Destinations List -->
    <div class="bg-surface-card border border-border rounded-lg shadow-sm p-4">
        <h3 class="font-semibold text-foreground mb-4">Top 5 Destinations</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-foreground-muted uppercase tracking-wider">Destination</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-foreground-muted uppercase tracking-wider">Visits</th>
                    </tr>
                </thead>
                <tbody class="bg-surface-card divide-y divide-border">
                    @forelse($stats['top_destinations'] ?? [] as $dest)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-foreground">{{ $dest['destination'] }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground-muted">{{ $dest['count'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-sm text-foreground-muted">No data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
             let statusChart = null;
             let timelineChart = null;

             const isDark = document.documentElement.classList.contains('dark');
             const textColor = isDark ? '#ECFDF5' : '#134E4A';
             const gridColor = isDark ? '#1E293B' : '#CCFBF1';

             const initCharts = (stats) => {
                const statusCtx = document.getElementById('statusChart');
                const timelineCtx = document.getElementById('timelineChart');

                if (statusChart) statusChart.destroy();
                if (timelineChart) timelineChart.destroy();

                if (statusCtx) {
                    statusChart = new Chart(statusCtx, {
                        type: 'doughnut',
                        data: {
                            labels: Object.keys(stats.status_distribution || {}),
                            datasets: [{
                                data: Object.values(stats.status_distribution || {}),
                                backgroundColor: [
                                    '#0D9488', // Planned - Teal
                                    '#10B981', // Ongoing - Emerald
                                    '#8B5CF6', // Completed - Purple
                                    '#EF4444'  // Cancelled - Red
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { color: textColor }
                                }
                            }
                        }
                    });
                }

                if (timelineCtx) {
                    timelineChart = new Chart(timelineCtx, {
                        type: 'bar',
                        data: {
                            labels: Object.keys(stats.trips_per_year || {}),
                            datasets: [{
                                label: 'Trips',
                                data: Object.values(stats.trips_per_year || {}),
                                backgroundColor: '#EA580C',
                                borderRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { stepSize: 1, color: textColor },
                                    grid: { color: gridColor }
                                },
                                x: {
                                    ticks: { color: textColor },
                                    grid: { color: gridColor }
                                }
                            }
                        }
                    });
                }
            };

            const initialStats = @json($stats);
            initCharts(initialStats);

            Livewire.on('stats-refreshed', (event) => {
                const newStats = event.stats || event[0].stats || event;
                initCharts(newStats);
            });
        });
    </script>
</div>
