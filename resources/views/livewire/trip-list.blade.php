<div class="w-full">
    <x-ui.page-header title="My Trips" subtitle="Manage and organize your travel adventures">
        <a href="{{ route('trips.create') }}" wire:navigate class="btn-primary">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create Trip
        </a>
    </x-ui.page-header>

    <!-- Filters and Search -->
    <div class="card p-6 mb-6">
        <!-- Search Bar -->
        <div class="mb-4">
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    class="field p-3 pl-10"
                    placeholder="Search trips by destination, type, or notes..."
                >
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                    <svg class="h-5 w-5 text-foreground-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filters Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-1">Status</label>
                <select wire:model.live="statusFilter" class="field p-2.5">
                    @foreach($statusOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-1">Type</label>
                <select wire:model.live="typeFilter" class="field p-2.5">
                    @foreach($typeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Destination Filter -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-1">Destination</label>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="destinationFilter"
                    class="field p-2.5"
                    placeholder="Filter by destination"
                >
            </div>

            <!-- Date From -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-1">From Date</label>
                <input
                    type="date"
                    wire:model.live="dateFrom"
                    class="field p-2.5"
                >
            </div>

            <!-- Date To -->
            <div>
                <label class="block text-sm font-medium text-foreground mb-1">To Date</label>
                <input
                    type="date"
                    wire:model.live="dateTo"
                    class="field p-2.5"
                >
            </div>
        </div>

        <!-- Actions Row -->
        <div class="flex items-center justify-between">
            <button
                wire:click="clearFilters"
                class="btn-ghost px-3 py-2 text-sm text-foreground-muted"
            >
                Clear Filters
            </button>

            <!-- View Toggle -->
            <div class="flex items-center space-x-2">
                <span class="text-sm text-foreground-muted">View:</span>
                <div class="flex bg-surface-muted dark:bg-surface rounded-lg p-1">
                    <button
                        wire:click="toggleViewMode"
                        class="p-2 rounded cursor-pointer {{ $viewMode === 'card' ? 'bg-surface-card shadow-sm text-foreground' : 'text-foreground-muted hover:text-foreground' }}"
                        title="Card View" aria-label="Card view"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </button>
                    <button
                        wire:click="toggleViewMode"
                        class="p-2 rounded cursor-pointer {{ $viewMode === 'list' ? 'bg-surface-card shadow-sm text-foreground' : 'text-foreground-muted hover:text-foreground' }}"
                        title="List View" aria-label="List view"
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
            <span class="text-sm text-foreground-muted">Sort by:</span>
            <select wire:model.live="sortBy" class="field w-auto p-2.5">
                @foreach($sortOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            <button
                wire:click="$set('sortDirection', '{{ $sortDirection === 'asc' ? 'desc' : 'asc' }}')"
                class="btn-ghost p-2.5" aria-label="Toggle sort direction"
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

    <!-- Results refresh indicator (filters, sort, pagination all re-query) -->
    <div wire:loading.delay.flex class="mb-4 flex items-center gap-2 text-sm text-foreground-muted">
        <x-ui.spinner size="xs" class="text-primary" />
        Updating results…
    </div>

    <!-- Trips Display -->
    <div wire:loading.delay.class="opacity-40 pointer-events-none" class="transition-opacity duration-200">
    @if($trips->count() > 0)
        @if($viewMode === 'card')
            <!-- Card View -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6 stagger-grid">
                @foreach($trips as $trip)
                    <a href="{{ route('trips.show', $trip->id) }}" wire:navigate wire:key="card-{{ $trip->id }}"
                        class="card card-hover block group overflow-hidden hover:border-primary/40 transition-[border-color,box-shadow] duration-200">
                        <!-- Destination cover: the country's flag as a cinematic backdrop -->
                        <div class="relative h-28 overflow-hidden">
                            @if($trip->country_code)
                                <img src="https://flagcdn.com/w320/{{ strtolower($trip->country_code) }}.png" alt=""
                                    aria-hidden="true"
                                    class="absolute inset-0 w-full h-full object-cover scale-105 brightness-[0.75] saturate-[1.1] group-hover:scale-110 transition-transform duration-500"
                                    loading="lazy">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-primary to-primary-dark"></div>
                            @endif
                            <div class="absolute inset-0 bg-gradient-to-t from-black/55 via-black/10 to-transparent" aria-hidden="true"></div>
                            <div class="absolute top-3 right-3 inline-flex rounded-full bg-white/90 dark:bg-black/60 backdrop-blur-sm shadow-sm">
                                <x-status-badge :status="$trip->status" />
                            </div>
                            <div class="absolute bottom-3 left-4 right-4 flex items-center gap-3">
                                @if($trip->country_code)
                                    <img src="https://flagcdn.com/{{ strtolower($trip->country_code) }}.svg" alt="{{ $trip->country_code }} flag"
                                        class="w-10 h-7 object-cover rounded-md shadow-lg ring-1 ring-white/30 shrink-0" loading="lazy">
                                @endif
                                <div class="min-w-0">
                                    <h3 class="text-lg font-extrabold text-white drop-shadow-sm truncate leading-tight">{{ $trip->destination }}</h3>
                                    <p class="text-xs font-semibold text-white/70 tracking-wider">{{ $trip->country_code }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2 p-5 pb-0 mb-4">
                            <div class="flex items-center text-sm text-foreground-muted">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $trip->start_date->format('M j') }} - {{ $trip->end_date->format('M j, Y') }}
                            </div>

                            <div class="flex items-center text-sm text-foreground-muted">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <span class="chip bg-secondary/10 text-secondary capitalize">{{ $trip->type }}</span>
                            </div>

                            @if($trip->budget)
                                <div class="flex items-center text-sm text-foreground-muted">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    <span class="tabular-nums">PKR {{ number_format($trip->budget, 0) }}</span>
                                </div>
                            @endif
                        </div>

                        @if($trip->notes)
                            <p class="text-sm text-foreground-muted line-clamp-2 px-5">{{ Str::limit($trip->notes, 100) }}</p>
                        @endif

                        <div class="flex items-center justify-end gap-1 mt-4 px-5 py-4 border-t border-border text-sm font-semibold text-primary">
                            View details
                            <svg class="w-4 h-4 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <!-- List View -->
            <div class="card overflow-hidden mb-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-surface-muted dark:bg-surface">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-foreground-muted uppercase tracking-wider">Destination</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-foreground-muted uppercase tracking-wider">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-foreground-muted uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-foreground-muted uppercase tracking-wider">Budget</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-foreground-muted uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-foreground-muted uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-surface-card divide-y divide-border">
                            @foreach($trips as $trip)
                                <tr wire:key="list-{{ $trip->id }}" class="hover:bg-surface-muted dark:hover:bg-surface">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-foreground">{{ $trip->destination }}</div>
                                            <div class="text-sm text-foreground-muted">{{ $trip->country_code }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">
                                        {{ $trip->start_date->format('M j') }} - {{ $trip->end_date->format('M j, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground capitalize">
                                        {{ $trip->type }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-foreground">
                                        @if($trip->budget)
                                            PKR {{ number_format($trip->budget, 0) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge :status="$trip->status" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('trips.show', $trip->id) }}" class="text-primary hover:text-primary-dark">View</a>
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
            <div class="text-sm text-foreground-muted">
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
        <div class="card aurora-bg p-12 text-center">
            <div class="w-16 h-16 mx-auto mb-5 bg-primary/10 rounded-2xl flex items-center justify-center">
                <svg class="h-8 w-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-foreground mb-2">No trips found</h3>
            <p class="text-foreground-muted mb-6">
                @if($search || $statusFilter || $typeFilter || $destinationFilter || $dateFrom || $dateTo)
                    Try adjusting your filters or search terms.
                @else
                    Get started by creating your first trip.
                @endif
            </p>
            <a href="{{ route('trips.create') }}" wire:navigate class="btn-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Create Your First Trip
            </a>
        </div>
    @endif
    </div>
</div>
