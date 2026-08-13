<?php

namespace App\Livewire;

use App\DTOs\WeatherDTO;
use App\Http\Requests\StoreTripRequest;
use App\Models\Trip;
use App\Repositories\TripRepository;
use App\Services\External\CountryService;
use App\Services\External\LocationService;
use App\Services\External\WeatherService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

class CreateTrip extends Component
{
    // Form steps
    public int $currentStep = 1;
    public const STEP_DESTINATION = 1;
    public const STEP_DATES = 2;
    public const STEP_DETAILS = 3;
    public const STEP_CONFIRM = 4;

    // Form data
    public string $destination = '';
    public string $countryCode = '';
    public string $startDate = '';
    public string $endDate = '';
    public string $type = '';
    public string $budget = '';
    public int $travelers = 1;
    public string $notes = '';

    // UI state
    public array $searchResults = ['countries' => [], 'states' => [], 'cities' => []];
    public ?int $expandedStateIndex = null;
    public array $stateCities = [];
    public int $stateCityTotal = 0;
    public bool $isSearching = false;
    public array $selectedCountry = [];
    public array $weatherPreview = [];
    public array $conflictingTrips = [];
    public bool $showWeatherPreview = false;

    // Trip types with icons
    public array $tripTypes = [
        'business' => ['icon' => 'briefcase', 'label' => 'Business', 'description' => 'Work-related travel'],
        'leisure' => ['icon' => 'umbrella-beach', 'label' => 'Leisure', 'description' => 'Relaxation and enjoyment'],
        'adventure' => ['icon' => 'mountain', 'label' => 'Adventure', 'description' => 'Outdoor activities and exploration'],
        'family' => ['icon' => 'users', 'label' => 'Family', 'description' => 'Travel with family members'],
        'solo' => ['icon' => 'user', 'label' => 'Solo', 'description' => 'Independent travel'],
    ];

    private CountryService $countryService;
    private LocationService $locationService;
    private WeatherService $weatherService;
    private TripRepository $tripRepository;

    public function boot(
        CountryService $countryService,
        LocationService $locationService,
        WeatherService $weatherService,
        TripRepository $tripRepository
    ) {
        $this->countryService = $countryService;
        $this->locationService = $locationService;
        $this->weatherService = $weatherService;
        $this->tripRepository = $tripRepository;
    }

    public function getSearchResultCountProperty(): int
    {
        return count($this->searchResults['countries'] ?? [])
            + count($this->searchResults['states'] ?? [])
            + count($this->searchResults['cities'] ?? []);
    }

    public function mount()
    {
        $this->loadFormState();
    }

    public function dehydrate()
    {
        $this->saveFormState();
    }

    /**
     * Load form state from session
     */
    private function loadFormState()
    {
        $state = Session::get('trip_creation_state', []);
        $this->countryCode = $state['country_code'] ?? '';
        // Only restore a confirmed destination (one selected together with its
        // country). Raw, unselected search text would reappear looking like a
        // default value.
        $this->destination = $this->countryCode !== '' ? ($state['destination'] ?? '') : '';
        $this->startDate = $state['start_date'] ?? '';
        $this->endDate = $state['end_date'] ?? '';
        $this->type = $state['type'] ?? '';
        $this->budget = $state['budget'] ?? '';
        $this->travelers = $state['travelers'] ?? 1;
        $this->notes = $state['notes'] ?? '';
        $this->currentStep = $state['current_step'] ?? 1;

        if ($this->countryCode) {
            $this->loadCountryDetails();
        }
    }

    /**
     * Save form state to session
     */
    private function saveFormState()
    {
        Session::put('trip_creation_state', [
            'destination' => $this->destination,
            'country_code' => $this->countryCode,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'type' => $this->type,
            'budget' => $this->budget,
            'travelers' => $this->travelers,
            'notes' => $this->notes,
            'current_step' => $this->currentStep,
        ]);
    }

    /**
     * Clear form state from session
     */
    private function clearFormState()
    {
        Session::forget('trip_creation_state');
    }
    public function incrementTravelers()
    {
        if ($this->travelers < 50) {
            $this->travelers++;
        }
    }

    /**
     * Decrement travelers count
     */
    public function decrementTravelers()
    {
        if ($this->travelers > 1) {
            $this->travelers--;
        }
    }

    public function updatedDestination()
    {
        if (strlen(trim($this->destination)) >= 3) {
            $this->searchDestinations();
        } else {
            $this->searchResults = $this->emptySearchResults();
        }
    }

    private function emptySearchResults(): array
    {
        $this->expandedStateIndex = null;
        $this->stateCities = [];
        $this->stateCityTotal = 0;

        return ['countries' => [], 'states' => [], 'cities' => []];
    }

    /**
     * Expand/collapse a state result to show its major cities.
     */
    public function toggleStateCities(int $index)
    {
        if ($this->expandedStateIndex === $index) {
            $this->expandedStateIndex = null;
            $this->stateCities = [];
            $this->stateCityTotal = 0;
            return;
        }

        $place = $this->searchResults['states'][$index] ?? null;
        if (!$place) {
            return;
        }

        $result = $this->locationService->getCitiesForState($place['country_code'], $place['name']);
        $this->expandedStateIndex = $index;
        $this->stateCities = $result['cities'];
        $this->stateCityTotal = $result['total'];
    }

    /**
     * Select a city from an expanded state's list as the trip destination.
     */
    public function selectStateCity(int $cityIndex)
    {
        $city = $this->stateCities[$cityIndex] ?? null;
        if (!$city) {
            return;
        }

        $this->countryCode = $city['country_code'];
        $this->destination = $city['name'];
        $this->selectedCountry = [];
        $this->loadCountryDetails();

        $this->searchResults = $this->emptySearchResults();

        $this->loadWeatherPreview();
        $this->nextStep();
    }

    /**
     * Search for destinations: countries, states/provinces and cities
     */
    public function searchDestinations()
    {
        $query = trim($this->destination);

        if (strlen($query) < 3) {
            $this->searchResults = $this->emptySearchResults();
            return;
        }

        $this->isSearching = true;

        try {
            $this->searchResults = $this->locationService->searchDestinations($query, $this->countryService);
        } catch (\Exception $e) {
            $this->searchResults = $this->emptySearchResults();
            session()->flash('error', 'Failed to search destinations. Please try again.');
        }

        $this->isSearching = false;
    }

    /**
     * Select a destination (country, state/province or city)
     */
    public function selectDestination(string $group, int $index)
    {
        $selected = $this->searchResults[$group][$index] ?? null;

        if (!$selected) {
            return;
        }

        if ($group === 'countries') {
            $this->countryCode = $selected['code'];
            $this->destination = $selected['name'];
            $this->selectedCountry = $selected;
        } else {
            // States/provinces and cities: the destination keeps the place
            // name (better weather accuracy), the country code drives
            // holidays and country context.
            $this->countryCode = $selected['country_code'];
            $this->destination = $selected['name'];
            $this->selectedCountry = [];
            $this->loadCountryDetails();
        }

        $this->searchResults = $this->emptySearchResults();

        $this->loadWeatherPreview();
        $this->nextStep();
    }

    /**
     * Load country details for selected country
     */
    private function loadCountryDetails()
    {
        try {
            $country = $this->countryService->getCountryInfo($this->countryCode);

            if ($country) {
                $this->selectedCountry = $country->toArray();
                $this->loadWeatherPreview();
            }
        } catch (\Exception $e) {
            // Silently fail for country details
        }
    }

    /**
     * Load weather preview for selected destination
     */
    private function loadWeatherPreview()
    {
        if (empty($this->destination)) {
            $this->weatherPreview = [];
            return;
        }

        try {
            $weather = $this->weatherService->getCurrentWeather($this->destination, 'metric');
            $this->weatherPreview = $weather ? $weather->toArray() : [];
            $this->showWeatherPreview = !empty($this->weatherPreview);
        } catch (\Exception $e) {
            $this->weatherPreview = [];
            $this->showWeatherPreview = false;
        }
    }

    /**
     * Check for trip conflicts when dates change
     */
    public function checkConflicts()
    {
        if (empty($this->startDate) || empty($this->endDate)) {
            $this->conflictingTrips = [];
            return;
        }

        $this->conflictingTrips = $this->tripRepository
            ->findConflictingTrips(Auth::id(), $this->startDate, $this->endDate)
            ->toArray();
    }

    /**
     * Navigate to next step
     */
    public function nextStep()
    {
        if ($this->validateCurrentStep()) {
            $this->currentStep = min($this->currentStep + 1, self::STEP_CONFIRM);
        }
    }

    /**
     * Navigate to previous step
     */
    public function previousStep()
    {
        $this->currentStep = max($this->currentStep - 1, self::STEP_DESTINATION);
    }

    /**
     * Go to specific step
     */
    public function goToStep(int $step)
    {
        if ($step >= self::STEP_DESTINATION && $step < $this->currentStep) {
            $this->currentStep = $step;
        }
    }

    /**
     * Validate current step before proceeding
     */
    private function validateCurrentStep(): bool
    {
        $rules = [];

        switch ($this->currentStep) {
            case self::STEP_DESTINATION:
                $rules = [
                    'destination' => 'required|string|max:255',
                    'countryCode' => 'required|string|size:2',
                ];
                break;

            case self::STEP_DATES:
                $rules = [
                    'startDate' => 'required|date|after_or_equal:today',
                    'endDate' => 'required|date|after_or_equal:startDate',
                ];
                break;

            case self::STEP_DETAILS:
                $rules = [
                    'type' => 'required|in:business,leisure,adventure,family,solo',
                    'travelers' => 'required|integer|min:1|max:50',
                    'budget' => 'nullable|numeric|min:0|max:99999999.99',
                ];
                break;
        }

        $validator = validator([
            'destination' => $this->destination,
            'countryCode' => $this->countryCode,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'type' => $this->type,
            'travelers' => $this->travelers,
            'budget' => $this->budget,
        ], $rules);

        if ($validator->fails()) {
            $this->setErrorBag($validator->errors());
            return false;
        }

        // Additional conflict check for dates
        if ($this->currentStep === self::STEP_DATES) {
            $this->checkConflicts();
            if (!empty($this->conflictingTrips)) {
                $this->addError('dates', 'You have conflicting trips. Please adjust your dates.');
                return false;
            }
        }

        return true;
    }

    /**
     * Create the trip
     */
    public function createTrip()
    {
        $request = new StoreTripRequest();
        $request->merge([
            'destination' => $this->destination,
            'country_code' => $this->countryCode,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'type' => $this->type,
            'budget' => $this->budget ?: null,
            'travelers' => $this->travelers,
            'notes' => $this->notes,
        ]);

        $validator = validator($request->all(), $request->rules());

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->addError('general', $error);
            }
            return;
        }

        $trip = Trip::create([
            'destination' => $this->destination,
            'country_code' => $this->countryCode,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'type' => $this->type,
            'budget' => $this->budget ?: null,
            'status' => 'planned',
            'notes' => $this->notes,
            'user_id' => Auth::id(),
            'metadata' => [
                'travelers' => $this->travelers,
                'currency' => 'PKR',
            ],
        ]);

        $this->resetForm();

        session()->flash('success', 'Trip created successfully!');
        return $this->redirectRoute('trips.show', ['id' => $trip->id]);
    }

    /**
     * Reset the form
     */
    public function resetForm()
    {
        $this->currentStep = 1;
        $this->destination = '';
        $this->countryCode = '';
        $this->startDate = '';
        $this->endDate = '';
        $this->type = '';
        $this->budget = '';
        $this->travelers = 1;
        $this->notes = '';
        $this->searchResults = $this->emptySearchResults();
        $this->selectedCountry = [];
        $this->weatherPreview = [];
        $this->conflictingTrips = [];
        $this->showWeatherPreview = false;
        $this->clearFormState();
    }

    public function render()
    {
        return view('livewire.create-trip');
    }
}
