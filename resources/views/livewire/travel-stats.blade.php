<div class="p-6 bg-white border-b border-gray-200">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Travel Statistics</h2>
        <div class="flex gap-2">
            <button wire:click="refreshStats" class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                Refresh
            </button>
            <button wire:click="downloadPdf" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Export PDF
            </button>
        </div>
    </div>

    <!-- Key Metrics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-blue-50 p-4 rounded-lg shadow-sm">
            <h3 class="text-gray-500 text-sm">Total Trips</h3>
            <p class="text-3xl font-bold text-blue-700">{{ $stats['total_trips'] ?? 0 }}</p>
            <div class="text-xs text-gray-400 mt-1">
                {{ $stats['completed_trips'] ?? 0 }} Completed • {{ $stats['planned_trips'] ?? 0 }} Planned
            </div>
        </div>
        
        <div class="bg-green-50 p-4 rounded-lg shadow-sm">
            <h3 class="text-gray-500 text-sm">Countries Visited</h3>
            <p class="text-3xl font-bold text-green-700">{{ $stats['unique_countries'] ?? 0 }}</p>
        </div>
        
        <div class="bg-purple-50 p-4 rounded-lg shadow-sm">
            <h3 class="text-gray-500 text-sm">Travel Days</h3>
            <p class="text-3xl font-bold text-purple-700">{{ $stats['total_travel_days'] ?? 0 }}</p>
        </div>
        
        <div class="bg-yellow-50 p-4 rounded-lg shadow-sm">
            <h3 class="text-gray-500 text-sm">Top Destination</h3>
            <p class="text-xl font-bold text-yellow-700 truncate" title="{{ $stats['top_destinations'][0]->destination ?? 'N/A' }}">
                {{ $stats['top_destinations'][0]->destination ?? 'N/A' }}
            </p>
            <div class="text-xs text-gray-400 mt-1">
                {{ $stats['most_common_trip_type'] ?? 'N/A' }}
            </div>
        </div>
    </div>

    <!-- Charts Area -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Status Distribution -->
        <div class="bg-white p-4 border rounded-lg shadow-sm">
            <h3 class="font-semibold mb-4 text-center">Trip Status Distribution</h3>
            <div class="relative h-64 w-full">
                <canvas id="statusChart"></canvas>
            </div>
        </div>

        <!-- Travel Timeline -->
        <div class="bg-white p-4 border rounded-lg shadow-sm">
            <h3 class="font-semibold mb-4 text-center">Travel Timeline (Trips per Year)</h3>
            <div class="relative h-64 w-full">
                <canvas id="timelineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Destinations List -->
    <div class="bg-white border rounded-lg shadow-sm p-4">
        <h3 class="font-semibold mb-4">Top 5 Destinations</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visits</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($stats['top_destinations'] ?? [] as $dest)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $dest->destination }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $dest->count }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">No data available</td>
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

             const initCharts = (stats) => {
                const statusCtx = document.getElementById('statusChart');
                const timelineCtx = document.getElementById('timelineChart');

                // Destroy existing charts if they exist
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
                                    '#60A5FA', // Planned - Blue
                                    '#34D399', // Ongoing - Green
                                    '#A78BFA', // Completed - Purple
                                    '#F87171'  // Cancelled - Red
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }

                if (timelineCtx) {
                    timelineChart = new Chart(timelineCtx, {
                        type: 'bar', // or 'line'
                        data: {
                            labels: Object.keys(stats.trips_per_year || {}),
                            datasets: [{
                                label: 'Trips',
                                data: Object.values(stats.trips_per_year || {}),
                                backgroundColor: '#FCD34D',
                                borderRadius: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }
            };

            // Initial load data
            const initialStats = @json($stats);
            initCharts(initialStats);

            // Listen for refresh event
            Livewire.on('stats-refreshed', (event) => {
                // event.stats contains the new stats data
                // Note: Livewire v3 usually unwraps the event payload, so check structure
                const newStats = event.stats || event[0].stats || event; 
                initCharts(newStats);
            });
        });
    </script>
</div>
