<div class="w-full max-w-4xl mx-auto">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Edit Trip</h1>
        <p class="text-gray-600">Update your trip details and preferences</p>
    </div>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <!-- Step 1: Destination -->
            <div class="flex items-center flex-1">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-full {{ $currentStep >= 1 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600' }} font-semibold">
                    @if($currentStep > 1)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    @else
                    1
                    @endif
                </div>
                <div class="ml-3 hidden sm:block">
                    <p class="text-sm font-medium {{ $currentStep >= 1 ? 'text-indigo-600' : 'text-gray-500' }}">
                        Destination</p>
                </div>
            </div>
            <div class="flex-1 h-1 mx-2 {{ $currentStep > 1 ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>

            <!-- Step 2: Dates -->
            <div class="flex items-center flex-1">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-full {{ $currentStep >= 2 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600' }} font-semibold">
                    @if($currentStep > 2)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    @else
                    2
                    @endif
                </div>
                <div class="ml-3 hidden sm:block">
                    <p class="text-sm font-medium {{ $currentStep >= 2 ? 'text-indigo-600' : 'text-gray-500' }}">Dates
                    </p>
                </div>
            </div>
            <div class="flex-1 h-1 mx-2 {{ $currentStep > 2 ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>

            <!-- Step 3: Details -->
            <div class="flex items-center flex-1">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-full {{ $currentStep >= 3 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600' }} font-semibold">
                    @if($currentStep > 3)
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    @else
                    3
                    @endif
                </div>
                <div class="ml-3 hidden sm:block">
                    <p class="text-sm font-medium {{ $currentStep >= 3 ? 'text-indigo-600' : 'text-gray-500' }}">Details
                    </p>
                </div>
            </div>
            <div class="flex-1 h-1 mx-2 {{ $currentStep > 3 ? 'bg-indigo-600' : 'bg-gray-200' }}"></div>

            <!-- Step 4: Confirm -->
            <div class="flex items-center flex-1">
                <div
                    class="flex items-center justify-center w-10 h-10 rounded-full {{ $currentStep >= 4 ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-600' }} font-semibold">
                    4
                </div>
                <div class="ml-3 hidden sm:block">
                    <p class="text-sm font-medium {{ $currentStep >= 4 ? 'text-indigo-600' : 'text-gray-500' }}">Confirm
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
    <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
        {{ session('success') }}
    </div>
    @endif

    @if (session()->has('error'))
    <div class="mb-6 px-4 py-3 rounded-lg"
        style="background-color: #ffe5e5; border: 1px solid #ffcccc; color: #cc0000;">
        {{ session('error') }}
    </div>
    @endif

    @if ($errors->any())
    <div class="mb-6 px-4 py-3 rounded-lg"
        style="background-color: #ffe5e5; border: 1px solid #ffcccc; color: #cc0000;">
        <ul class="list-disc list-inside">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Confirmation Modal -->
    @if($showConfirmationModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="confirmation-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mt-4">Confirm Changes</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">{{ $confirmationMessage }}</p>
                </div>
                <div class="flex items-center px-4 py-3 space-x-4">
                    <button wire:click="cancelAction" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button wire:click="confirmAction" class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 md:p-8">
        <!-- Step 1: Destination -->
        @if($currentStep === 1)
        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Where are you going?</h2>
                <p class="text-gray-600">Search for your destination</p>
            </div>

            <!-- Search Input -->
            <div class="relative">
                <input type="text" wire:model.live.debounce.300ms="destination" wire:keydown.enter="searchDestinations"
                    class="block w-full rounded-xl p-4 border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Search for countries or cities..." autocomplete="off">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    @if($isSearching)
                    <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-indigo-600"></div>
                    @else
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    @endif
                </div>
            </div>

            <!-- Search Results -->
            @if(count($searchResults) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-96 overflow-y-auto">
                @foreach($searchResults as $country)
                <div wire:click="selectDestination('{{ $country['code'] }}')"
                    class="bg-white border border-gray-200 rounded-lg p-4 hover:border-indigo-300 hover:shadow-md transition-all cursor-pointer group">
                    <div class="flex items-start space-x-3">
                        @if($country['flag'])
                        <img src="{{ $country['flag'] }}" alt="{{ $country['name'] }} flag"
                            class="w-12 h-8 object-cover rounded-sm shadow-sm">
                        @endif
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900 group-hover:text-indigo-600">
                                {{ $country['name'] }}
                            </h4>
                            <p class="text-sm text-gray-600">{{ $country['capital'] }}</p>
                            <p class="text-xs text-gray-500">{{ $country['region'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Selected Destination -->
            @if($countryCode && count($searchResults) === 0)
            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        @if(isset($selectedCountry['flag']))
                        <img src="{{ $selectedCountry['flag'] }}" alt="{{ $destination }} flag"
                            class="w-12 h-8 object-cover rounded-sm">
                        @endif
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $destination }}</h4>
                            @if(isset($selectedCountry['capital']))
                            <p class="text-sm text-gray-600">{{ $selectedCountry['capital'] }}</p>
                            @endif
                        </div>
                    </div>
                    <button wire:click="$set('countryCode', '')" class="text-red-600 hover:text-red-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Weather Preview -->
                @if($showWeatherPreview && !empty($weatherPreview))
                <div class="mt-4 pt-4 border-t border-indigo-200">
                    <h5 class="text-sm font-medium text-gray-700 mb-2">Current Weather</h5>
                    <div class="flex items-center space-x-4">
                        @if(isset($weatherPreview['icon']))
                        <img src="https://openweathermap.org/img/wn/{{ $weatherPreview['icon'] }}@2x.png"
                            alt="Weather icon" class="w-12 h-12">
                        @endif
                        <div>
                            <p class="text-2xl font-bold text-gray-900">{{ round($weatherPreview['temperature']) }}°C
                            </p>
                            <p class="text-sm text-gray-600 capitalize">{{ $weatherPreview['condition'] }}</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endif
        </div>
        @endif

        <!-- Step 2: Dates -->
        @if($currentStep === 2)
        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">When are you traveling?</h2>
                <p class="text-gray-600">Select your trip dates</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" wire:model.live="startDate" wire:change="checkConflicts"
                        min="{{ date('Y-m-d') }}"
                        class="block w-full rounded-lg p-3 border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('startDate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" wire:model.live="endDate" wire:change="checkConflicts"
                        min="{{ $startDate ?: date('Y-m-d') }}"
                        class="block w-full rounded-lg p-3 border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('endDate') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Trip Duration -->
            @if($startDate && $endDate)
            @php
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            $duration = $start->diff($end)->days + 1;
            @endphp
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <span class="font-semibold">Trip Duration:</span> {{ $duration }} {{ $duration === 1 ? 'day' :
                    'days' }}
                </p>
            </div>
            @endif

            <!-- Conflicting Trips Warning -->
            @if(count($conflictingTrips) > 0)
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-yellow-600 mt-0.5 mr-2" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <div>
                        <h4 class="font-semibold text-yellow-800 mb-1">Conflicting Trips Detected</h4>
                        <p class="text-sm text-yellow-700 mb-2">You have the following trips during these dates:</p>
                        <ul class="list-disc list-inside text-sm text-yellow-700">
                            @foreach($conflictingTrips as $trip)
                            <li>{{ $trip['destination'] }} ({{ date('M j', strtotime($trip['start_date'])) }} - {{
                                date('M j, Y', strtotime($trip['end_date'])) }})</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        <!-- Step 3: Details -->
        @if($currentStep === 3)
        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Trip Details</h2>
                <p class="text-gray-600">Tell us more about your trip</p>
            </div>

            <!-- Trip Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Trip Type</label>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-3">
                    @foreach($tripTypes as $typeKey => $typeInfo)
                    <button type="button" wire:click="$set('type', '{{ $typeKey }}')"
                        class="flex flex-col items-center justify-center p-4 border-2 rounded-lg transition-all {{ $type === $typeKey ? 'border-indigo-600 bg-indigo-50' : 'border-gray-200 hover:border-indigo-300' }}">
                        <div class="text-2xl mb-2">
                            @if($typeKey === 'business')
                            💼
                            @elseif($typeKey === 'leisure')
                            🏖️
                            @elseif($typeKey === 'adventure')
                            🏔️
                            @elseif($typeKey === 'family')
                            👨‍👩‍👧‍👦
                            @else
                            🧳
                            @endif
                        </div>
                        <span
                            class="text-sm font-medium {{ $type === $typeKey ? 'text-indigo-600' : 'text-gray-700' }}">
                            {{ $typeInfo['label'] }}
                        </span>
                    </button>
                    @endforeach
                </div>
                @error('type') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>


            <!-- Travelers Count -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Number of Travelers</label>
                <div class="flex items-center space-x-4">
                    <button type="button" wire:click.prevent="decrementTravelers"
                        class="w-10 h-10 rounded-lg border border-gray-300 hover:bg-gray-50 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                        </svg>
                    </button>
                    <div
                        class="w-20 text-center rounded-lg p-3 border border-gray-300 bg-gray-50 font-medium text-gray-900">
                        {{ $travelers }}
                    </div>
                    <button type="button" wire:click.prevent="incrementTravelers"
                        class="w-10 h-10 rounded-lg border border-gray-300 hover:bg-gray-50 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                    </button>
                    <span class="text-sm text-gray-600">{{ $travelers === 1 ? 'traveler' : 'travelers' }}</span>
                </div>
                @error('travelers') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Budget -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Budget (Optional)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">PKR</span>
                    <input type="number" wire:model="budget" min="0" step="0.01"
                        class="block w-full pl-14 rounded-lg p-3 border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                        placeholder="0.00">
                </div>
                <p class="mt-1 text-sm text-gray-500">Enter your estimated budget in Pakistani Rupees</p>
                @error('budget') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Notes -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea wire:model="notes" rows="4" maxlength="1000"
                    class="block w-full rounded-lg p-3 border border-gray-300 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Add any additional notes about your trip..."></textarea>
                <p class="mt-1 text-sm text-gray-500">{{ strlen($notes) }}/1000 characters</p>
                @error('notes') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
        @endif

        <!-- Step 4: Confirm -->
        @if($currentStep === 4)
        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Review Your Trip</h2>
                <p class="text-gray-600">Please review your trip details before updating</p>
            </div>

            <!-- Trip Summary -->
            <div class="bg-gray-50 rounded-lg p-6 space-y-4">
                <!-- Destination -->
                <div class="flex items-start justify-between pb-4 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        @if(isset($selectedCountry['flag']))
                        <img src="{{ $selectedCountry['flag'] }}" alt="{{ $destination }} flag"
                            class="w-12 h-8 object-cover rounded-sm">
                        @endif
                        <div>
                            <p class="text-sm text-gray-600">Destination</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $destination }}</p>
                        </div>
                    </div>
                    <button wire:click="goToStep(1)" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        Edit
                    </button>
                </div>

                <!-- Dates -->
                <div class="flex items-start justify-between pb-4 border-b border-gray-200">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Travel Dates</p>
                        <p class="text-lg font-semibold text-gray-900">
                            {{ date('M j, Y', strtotime($startDate)) }} - {{ date('M j, Y', strtotime($endDate)) }}
                        </p>
                        @php
                        $start = new DateTime($startDate);
                        $end = new DateTime($endDate);
                        $duration = $start->diff($end)->days + 1;
                        @endphp
                        <p class="text-sm text-gray-600">{{ $duration }} {{ $duration === 1 ? 'day' : 'days' }}</p>
                    </div>
                    <button wire:click="goToStep(2)" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        Edit
                    </button>
                </div>

                <!-- Trip Details -->
                <div class="flex items-start justify-between">
                    <div class="space-y-3 flex-1">
                        <div>
                            <p class="text-sm text-gray-600">Trip Type</p>
                            <p class="text-lg font-semibold text-gray-900 capitalize">{{ $type }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Travelers</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $travelers }} {{ $travelers === 1 ?
                                'person' : 'people' }}</p>
                        </div>
                        @if($budget)
                        <div>
                            <p class="text-sm text-gray-600">Budget</p>
                            <p class="text-lg font-semibold text-gray-900">PKR {{ number_format($budget, 2) }}</p>
                        </div>
                        @endif
                        @if($notes)
                        <div>
                            <p class="text-sm text-gray-600">Notes</p>
                            <p class="text-sm text-gray-900">{{ $notes }}</p>
                        </div>
                        @endif
                    </div>
                    <button wire:click="goToStep(3)" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                        Edit
                    </button>
                </div>
            </div>
        </div>
        @endif

        <!-- Navigation Buttons -->
        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200">
            <div>
                @if($currentStep > 1)
                <button wire:click="previousStep"
                    class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                    Previous
                </button>
                @else
                <a href="{{ route('trips.show', $trip->id) }}"
                    class="inline-flex items-center px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors">
                    Cancel
                </a>
                @endif
            </div>

            <div class="flex items-center space-x-3">
                @if($currentStep < 4) <button wire:click="nextStep"
                    class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors">
                    Next
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    </button>
                    @else
                    <button wire:click="updateTrip" wire:loading.attr="disabled"
                        class="inline-flex items-center px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:bg-green-400 disabled:cursor-not-allowed font-medium transition-colors">
                        <svg wire:loading class="w-5 h-5 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <svg wire:loading.remove class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        <span wire:loading.remove>Update Trip</span>
                        <span wire:loading>Updating...</span>
                    </button>
                    @endif
            </div>
        </div>
    </div>

    <!-- Help Text -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-500">
            Your progress is automatically saved. You can safely navigate away and return later.
        </p>
    </div>
</div>
