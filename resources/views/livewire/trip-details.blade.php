<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb Navigation -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex py-4" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-4">
                    <li>
                        <div>
                            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500">
                                <svg class="flex-shrink-0 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-5.5 5.5A1 1 0 004.5 9H5v6a1 1 0 001 1h1a1 1 0 001-1v-3a1 1 0 011-1h2a1 1 0 011 1v3a1 1 0 001 1h1a1 1 0 001-1V9h.5a1 1 0 00.707-1.707l-5.5-5.5z"/>
                                </svg>
                                <span class="sr-only">Home</span>
                            </a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <a href="{{ route('trips') }}" class="ml-4 text-sm font-medium text-gray-500 hover:text-gray-700">My Trips</a>
                        </div>
                    </li>
                    <li>
                        <div class="flex items-center">
                            <svg class="flex-shrink-0 h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-4 text-sm font-medium text-gray-900">{{ $trip->destination }}</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Trip Header -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <div class="flex items-center space-x-4 mb-4">
                        <h1 class="text-3xl font-bold text-gray-900">{{ $trip->destination }}</h1>
                        <x-status-badge :status="$trip->status" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span>{{ $trip->country_code }}</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>{{ $trip->start_date->format('M j') }} - {{ $trip->end_date->format('M j, Y') }}</span>
                        </div>
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span class="capitalize">{{ $trip->type }}</span>
                        </div>
                    </div>
                    @if($trip->budget)
                        <div class="mt-4">
                            <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                                <span>Budget</span>
                                <span>PKR {{ number_format($trip->budget) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min($budgetUsed, 100) }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>PKR {{ number_format($totalExpenses) }} spent</span>
                                <span>{{ $budgetUsed }}% used</span>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="flex space-x-2 ml-6">
                    <a href="{{ route('trips.edit', $trip->id) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Trip
                    </a>
                    <button wire:click="shareTrip" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                        </svg>
                        Share
                    </button>
                    <button wire:click="exportTrip" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Export
                    </button>
                    <button wire:click="deleteTrip" class="inline-flex items-center px-3 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Delete
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Weather Forecast -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Weather Forecast</h2>
                    @if($weatherForecast)
                        <div class="grid grid-cols-1 md:grid-cols-7 gap-4">
                            @foreach($weatherForecast as $weather)
                                <div class="text-center p-4 rounded-lg {{ $weather->date >= $trip->start_date->format('Y-m-d') && $weather->date <= $trip->end_date->format('Y-m-d') ? 'bg-indigo-50 border border-indigo-200' : 'bg-gray-50' }}">
                                    <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($weather->date)->format('M j') }}</div>
                                    @if($weather->icon)
                                        <img src="https://openweathermap.org/img/wn/{{ $weather->icon }}@2x.png" alt="{{ $weather->condition }}" class="w-12 h-12 mx-auto my-2">
                                    @endif
                                    <div class="text-lg font-semibold text-gray-900">{{ round($weather->temperature) }}°C</div>
                                    <div class="text-xs text-gray-600">{{ $weather->condition }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        H: {{ round($weather->maxTemp ?? $weather->temperature) }}° L: {{ round($weather->minTemp ?? $weather->temperature) }}°
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                            </svg>
                            <p>Weather forecast not available</p>
                        </div>
                    @endif
                </div>

                <!-- Packing Checklist -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Packing Checklist</h2>
                        <span class="text-sm text-gray-600">{{ $packingProgress }}% complete</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ $packingProgress }}%"></div>
                    </div>
                    @if($trip->packingItems->count() > 0)
                        <div class="space-y-2">
                            @foreach($trip->packingItems->groupBy('category') as $category => $items)
                                <div>
                                    <h3 class="font-medium text-gray-900 mb-2 capitalize">{{ $category }}</h3>
                                    <div class="space-y-1 ml-4">
                                        @foreach($items as $item)
                                            <div class="flex items-center">
                                                <input type="checkbox" {{ $item->is_packed ? 'checked' : '' }} disabled class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                                <span class="ml-2 text-sm {{ $item->is_packed ? 'line-through text-gray-500' : 'text-gray-900' }}">{{ $item->item }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p>No packing items added yet</p>
                        </div>
                    @endif
                </div>

                <!-- Trip Notes -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-gray-900">Trip Notes</h2>
                        @if(!$editingNote)
                            <button wire:click="editNote" class="text-indigo-600 hover:text-indigo-500 text-sm font-medium">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </button>
                        @endif
                    </div>
                    @if($editingNote)
                        <div class="space-y-4">
                            <textarea
                                wire:model="noteContent"
                                rows="6"
                                class="block w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Add your trip notes here..."
                            ></textarea>
                            <div class="flex space-x-2">
                                <button wire:click="saveNote" class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                                    Save Notes
                                </button>
                                <button wire:click="cancelEditNote" class="inline-flex items-center px-3 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-medium">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    @else
                        @if($noteContent)
                            <div class="prose prose-sm max-w-none text-gray-700">
                                {!! nl2br(e($noteContent)) !!}
                            </div>
                        @else
                            <div class="text-center py-8 text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p>No notes added yet</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-8">
                <!-- Holidays -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Holidays</h2>
                    @if($holidays)
                        <div class="space-y-3">
                            @foreach($holidays as $holiday)
                                <div class="flex items-start space-x-3 p-3 rounded-lg bg-yellow-50 border border-yellow-200">
                                    <div class="flex-shrink-0">
                                        <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">{{ $holiday->name }}</p>
                                        <p class="text-xs text-gray-600">{{ \Carbon\Carbon::parse($holiday->date)->format('M j, Y') }}</p>
                                        @if($holiday->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ $holiday->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p>No holidays during your trip</p>
                        </div>
                    @endif
                </div>

                <!-- Expense Summary -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Expense Summary</h2>
                    @if($trip->expenses->count() > 0)
                        <div class="space-y-4">
                            @foreach($trip->expenses->groupBy('category') as $category => $expenses)
                                <div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm font-medium text-gray-900 capitalize">{{ $category }}</span>
                                        <span class="text-sm text-gray-600">PKR {{ number_format($expenses->sum('amount')) }}</span>
                                    </div>
                                    <div class="space-y-1">
                                        @foreach($expenses as $expense)
                                            <div class="flex items-center justify-between text-xs text-gray-600">
                                                <span>{{ $expense->description ?: 'No description' }}</span>
                                                <span>{{ $expense->expense_date->format('M j') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                            <hr class="border-gray-200">
                            <div class="flex items-center justify-between font-semibold text-gray-900">
                                <span>Total</span>
                                <span>PKR {{ number_format($totalExpenses) }}</span>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                            </svg>
                            <p>No expenses recorded</p>
                        </div>
                    @endif
                </div>

                <!-- Trip Timeline -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Trip Timeline</h2>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-2 h-2 bg-green-500 rounded-full mt-2"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900">Trip Created</p>
                                <p class="text-xs text-gray-600">{{ $trip->created_at->format('M j, Y \a\t g:i A') }}</p>
                            </div>
                        </div>
                        @if($trip->updated_at != $trip->created_at)
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">Last Updated</p>
                                    <p class="text-xs text-gray-600">{{ $trip->updated_at->format('M j, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        @endif
                        @if($trip->status === 'completed')
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-2 h-2 bg-gray-500 rounded-full mt-2"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">Trip Completed</p>
                                    <p class="text-xs text-gray-600">{{ $trip->updated_at->format('M j, Y \a\t g:i A') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
