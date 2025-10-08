<div class="w-full max-w-4xl mx-auto">
    <!-- Search Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Discover Destinations</h1>
        <p class="text-gray-600">Search and explore amazing places around the world</p>
    </div>

    <!-- Search Input -->
    <div class="relative mb-6">
        <div class="relative">
            <button
                type="button"
                wire:click="triggerSearch"
                class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400 hover:text-gray-600"
                aria-label="Search"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
            <input 
                type="text" 
                wire:model.live.debounce.300ms="searchQuery"
                wire:keydown.enter="triggerSearch"
                class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg"
                placeholder="Search for countries, capitals, or regions..."
                autocomplete="off"
            >
            @if($searchQuery)
                <button 
                    wire:click="clearSearch"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                >
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    <!-- Recent Searches -->
    @if($showRecentSearches && count($recentSearches) > 0)
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Recent Searches</h3>
                <button 
                    wire:click="clearHistory"
                    class="text-sm text-red-600 hover:text-red-800 font-medium"
                >
                    Clear History
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($recentSearches as $search)
                    <div 
                        wire:click="selectCountry('{{ $search['code'] }}')"
                        class="bg-white rounded-lg border border-gray-200 p-4 hover:border-blue-300 hover:shadow-md transition-all duration-200 cursor-pointer group"
                    >
                        <div class="flex items-center space-x-3">
                            @if($search['flag'])
                                <img 
                                    src="{{ $search['flag'] }}" 
                                    alt="{{ $search['name'] }} flag"
                                    class="w-8 h-6 object-cover rounded-sm"
                                >
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 group-hover:text-blue-600 truncate">
                                    {{ $search['name'] }}
                                </h4>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ $search['capital'] }}, {{ $search['region'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Loading State -->
    @if($isLoading)
        <div class="space-y-4">
            <div class="flex items-center space-x-2 mb-4">
                <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-600"></div>
                <span class="text-gray-600">Searching destinations...</span>
            </div>
            
            <!-- Skeleton Loaders -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @for($i = 0; $i < 6; $i++)
                    <div class="bg-white rounded-lg border border-gray-200 p-6 animate-pulse">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-8 bg-gray-200 rounded-sm"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                                <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                <div class="h-3 bg-gray-200 rounded w-2/3"></div>
                            </div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="h-3 bg-gray-200 rounded w-full"></div>
                            <div class="h-3 bg-gray-200 rounded w-4/5"></div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    @endif

    <!-- Search Results -->
    @if(!$isLoading && count($searchResults) > 0)
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900">
                    Search Results ({{ count($searchResults) }} found)
                </h3>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($searchResults as $country)
                    <div 
                        wire:click="selectCountry('{{ $country['code'] }}')"
                        class="bg-white rounded-lg border border-gray-200 p-6 hover:border-blue-300 hover:shadow-lg transition-all duration-200 cursor-pointer group"
                    >
                        <!-- Country Header -->
                        <div class="flex items-start space-x-4 mb-4">
                            @if($country['flag'])
                                <img 
                                    src="{{ $country['flag'] }}" 
                                    alt="{{ $country['name'] }} flag"
                                    class="w-12 h-8 object-cover rounded-sm shadow-sm"
                                >
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="text-lg font-semibold text-gray-900 group-hover:text-blue-600 mb-1">
                                    {{ $country['name'] }}
                                </h4>
                                <p class="text-sm text-gray-600">
                                    {{ $country['code'] }} • {{ $country['region'] }}
                                </p>
                            </div>
                        </div>

                        <!-- Country Details -->
                        <div class="space-y-2">
                            @if($country['capital'])
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">{{ $country['capital'] }}</span>
                                </div>
                            @endif

                            @if($country['population'])
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">{{ number_format($country['population']) }}</span>
                                </div>
                            @endif

                            @if($country['currency'])
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">{{ $country['currency'] }}</span>
                                </div>
                            @endif

                            @if($country['timezone'])
                                <div class="flex items-center space-x-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-600">{{ $country['timezone'] }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Languages -->
                        @if(count($country['languages']) > 0)
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex items-center space-x-2 mb-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                    </svg>
                                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Languages</span>
                                </div>
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($country['languages'], 0, 3) as $language)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $language }}
                                        </span>
                                    @endforeach
                                    @if(count($country['languages']) > 3)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                            +{{ count($country['languages']) - 3 }} more
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Action Button -->
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-center text-blue-600 group-hover:text-blue-700">
                                <span class="text-sm font-medium">Select Destination</span>
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- No Results -->
    @if(!$isLoading && $searchQuery && count($searchResults) === 0)
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No destinations found</h3>
            <p class="mt-1 text-sm text-gray-500">Try searching with different keywords or check your spelling.</p>
        </div>
    @endif

    <!-- Empty State (when no search and no recent searches) -->
    @if(!$searchQuery && count($recentSearches) === 0)
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Start exploring destinations</h3>
            <p class="mt-1 text-sm text-gray-500">Search for countries, capitals, or regions to discover amazing places.</p>
        </div>
    @endif
</div>

<script>
document.addEventListener('livewire:init', () => {
    let searchTimeout;
    
    Livewire.on('search-debounced', (event) => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            Livewire.dispatch('perform-search', { query: event.query });
        }, 300);
    });
});
</script>