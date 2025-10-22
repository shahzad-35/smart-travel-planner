<div class="w-full">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">My Trips</h1>
                <p class="text-gray-600">Manage and organize your travel adventures</p>
            </div>
            <a href="{{ route('trips.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Trip
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-6">
        <!-- Search Bar -->
        <div class="mb-4">
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="block w-full rounded-lg p-3 pl-10 border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Search trips by destination, type, or notes..."
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filters Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select wire:model.live="statusFilter" class="block w-full rounded-lg p-2 border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <select wire:model.live="typeFilter" class="block w-full rounded-lg p-2 border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($typeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Destination Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Destination</label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="destinationFilter"
                    class="block w-full rounded-lg p-2 border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Filter by destination"
                >
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input
                    type="date"
                    wire:model.live="dateFrom"
                    class="block w-full rounded-lg p-2 border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input
                    type="date"
                    wire:model.live="dateTo"
                    class="block w-full rounded-lg p-2 border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                >
            </div>
        </div>

        <!-- Actions Row -->
        <div class="flex items-center justify-between">
            <button
                wire:click="clearFilters"
                class="inline-flex items-center px-3 py-2 text-sm text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50"
            >
                Clear Filters
            </button>

            <!-- View Toggle -->
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">View:</span>
                <div class="flex bg-gray-100 rounded-lg p-1">
                    <button
                        wire:click="toggleViewMode"
                        class="p-2 rounded {{ $viewMode === 'card' ? 'bg-white shadow-sm' : 'text-gray-600 hover:text-gray-800' }}"
                        title="Card View"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </button>
                    <button
                        wire:click="toggleViewMode"
                        class="p-2 rounded {{ $viewMode === 'list' ? 'bg-white shadow-sm' : 'text-gray-600 hover:text-gray-800' }}"
                        title="List View"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sorting -->
    <div class="mb-4">
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600">Sort by:</span>
            <select wire:model.live="sortBy" class="rounded-lg p-2 border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @foreach($sortOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <button
                wire:click="$set('sortDirection', sortDirection === 'asc' ? 'desc' : 'asc')"
                class="p-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg hover:bg-gray-50"
            >
                @if($sortDirection === 'asc')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                @endif
            </button>
        </div>
    </div>

    <!-- Trips Display -->
    @if($trips->count() > 0)
        @if($viewMode === 'card')
            <!-- Card View -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                @foreach($trips as $trip)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ $trip->destination }}</h3>
                                <p class="text-sm text-gray-600">{{ $trip->country_code }}</p>
                            </div>
                            @php
                                $statusColors = [
                                    'planned' => 'bg-blue-100 text-blue-800 border-blue-200',
                                    'ongoing' => 'bg-green-100 text-green-800 border-green-200',
                                    'completed' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                ];
                                $colorClass = $statusColors[$trip->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $colorClass }}">
                                {{ ucfirst($trip->status) }}
                            </span>
                        </div>

                        <div class="space-y-2 mb-4">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $trip->start_date->format('M j') }} - {{ $trip->end_date->format('M j, Y') }}
                            </div>

                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                {{ ucfirst($trip->type) }}
                            </div>

                            @if($trip->budget)
                                <div class="flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    PKR {{ number_format($trip->budget, 0) }}
                                </div>
                            @endif
                        </div>

                        @if($trip->notes)
                            <p class="text-sm text-gray-600 line-clamp-2">{{ Str::limit($trip->notes, 100) }}</p>
                        @endif

                        <div class="flex justify-end mt-4">
                            <a href="{{ route('trips.show', $trip->id) }}" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                View Details
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- List View -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($trips as $trip)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $trip->destination }}</div>
                                            <div class="text-sm text-gray-500">{{ $trip->country_code }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $trip->start_date->format('M j') }} - {{ $trip->end_date->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 capitalize">
                                        {{ $trip->type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($trip->budget)
                                            PKR {{ number_format($trip->budget, 0) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'planned' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'ongoing' => 'bg-green-100 text-green-800 border-green-200',
                                                'completed' => 'bg-gray-100 text-gray-800 border-gray-200',
                                                'cancelled' => 'bg-red-100 text-red-800 border-red-200',
                                            ];
                                            $colorClass = $statusColors[$trip->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $colorClass }}">
                                            {{ ucfirst($trip->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('trips.show', $trip->id) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <!-- Pagination -->
        <div class="flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ $trips->firstItem() }} to {{ $trips->lastItem() }} of {{ $trips->total() }} results
            </div>
            <div class="flex space-x-1">
                @if ($trips->hasPages())
                    {{ $trips->links() }}
                @endif
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No trips found</h3>
            <p class="text-gray-600 mb-6">
                @if($search || $statusFilter || $typeFilter || $destinationFilter || $dateFrom || $dateTo)
                    Try adjusting your filters or search terms.
                @else
                    Get started by creating your first trip.
                @endif
            </p>
            <a href="{{ route('trips.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Your First Trip
            </a>
        </div>
    @endif
</div>
