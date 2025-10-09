<?php

namespace App\Livewire;

use App\DTOs\CountryDTO;
use App\DTOs\WeatherDTO;
use App\Http\Requests\StoreTripRequest;
use App\Models\Trip;
use App\Repositories\TripRepository;
use App\Services\External\CountryService;
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
    public array $searchResults = [];
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
    private WeatherService $weatherService;
    private TripRepository $tripRepository;

    public function boot(
        CountryService $countryService,
        WeatherService $weatherService,
        TripRepository $tripRepository
    ) {
        $this->countryService = $countryService;
        $this->weatherService = $weatherService;
        $this->tripRepository = $tripRepository;
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
        $this->destination = $state['destination'] ?? '';
        $this->countryCode = $state['country_code'] ?? '';
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

    /**
     * Search for destinations
     */
    public function searchDestinations()
    {
        $query = trim($this->destination);

        if (strlen($query) < 3) {
            $this->searchResults = [];
            return;
        }

        $this->isSearching = true;

        try {
            $results = $this->countryService->searchCountries($query);
            $this->searchResults = array_map(fn(CountryDTO $country) => $country->toArray(), $results);
        } catch (\Exception $e) {
            $this->searchResults = [];
            session()->flash('error', 'Failed to search destinations. Please try again.');
        }

        $this->isSearching = false;
    }

    /**
     * Select a destination
     */
    public function selectDestination(string $countryCode)
    {
        $country = collect($this->searchResults)->firstWhere('code', $countryCode);

        if (!$country) {
            return;
        }

        $this->countryCode = $countryCode;
        $this->destination = $country['name'];
        $this->selectedCountry = $country;
        $this->searchResults = [];

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
        if ($step >= self::STEP_DESTINATION && $step <= self::STEP_CONFIRM) {
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
            $this->addError('step', 'Please complete all required fields in this step.');
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
        // Validate all steps
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

        // Create the trip
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

        // Clear form state
        $this->clearFormState();

        // Redirect to trip details or dashboard
        session()->flash('success', 'Trip created successfully!');
        return redirect()->route('dashboard');
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
        $this->searchResults = [];
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
