<?php

namespace App\Livewire;

use App\DTOs\CountryDTO;
use App\DTOs\WeatherDTO;
use App\Http\Requests\UpdateTripRequest;
use App\Models\Trip;
use App\Repositories\TripRepository;
use App\Services\External\CountryService;
use App\Services\External\WeatherService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\On;
use Livewire\Component;

class EditTrip extends Component
{
    public Trip $trip;

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
    public bool $showConfirmationModal = false;
    public string $confirmationMessage = '';
    public string $pendingAction = '';
    public bool $isUpdating = false;

    // Track original values for change detection
    private array $originalValues = [];

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

    public function mount($id)
    {
        $this->trip = Trip::where('user_id', Auth::id())->findOrFail($id);
        $this->loadTripData();
        $this->loadFormState();
    }

    public function dehydrate()
    {
        $this->saveFormState();
    }

    /**
     * Load existing trip data and pre-fill form
     */
    private function loadTripData()
    {
        $this->destination = $this->trip->destination;
        $this->countryCode = $this->trip->country_code;
        $this->startDate = $this->trip->start_date->format('Y-m-d');
        $this->endDate = $this->trip->end_date->format('Y-m-d');
        $this->type = $this->trip->type;
        $this->budget = $this->trip->budget ? (string) $this->trip->budget : '';
        $this->travelers = $this->trip->metadata['travelers'] ?? 1;
        $this->notes = $this->trip->notes;

        // Store original values for change detection
        $this->originalValues = [
            'destination' => $this->destination,
            'country_code' => $this->countryCode,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'type' => $this->type,
            'budget' => $this->budget,
            'travelers' => $this->travelers,
            'notes' => $this->notes,
        ];

        if ($this->countryCode) {
            $this->loadCountryDetails();
        }

        $this->loadWeatherPreview();
        $this->checkConflicts();
    }

    /**
     * Load form state from session
     */
    private function loadFormState()
    {
        $state = Session::get("trip_edit_state_{$this->trip->id}", []);
        $this->destination = $state['destination'] ?? $this->destination;
        $this->countryCode = $state['country_code'] ?? $this->countryCode;
        $this->startDate = $state['start_date'] ?? $this->startDate;
        $this->endDate = $state['end_date'] ?? $this->endDate;
        $this->type = $state['type'] ?? $this->type;
        $this->budget = $state['budget'] ?? $this->budget;
        $this->travelers = $state['travelers'] ?? $this->travelers;
        $this->notes = $state['notes'] ?? $this->notes;
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
        Session::put("trip_edit_state_{$this->trip->id}", [
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
        Session::forget("trip_edit_state_{$this->trip->id}");
    }

    public function incrementTravelers()
    {
        if ($this->travelers < 50) {
            $this->travelers++;
        }
    }

    public function decrementTravelers()
    {
        if ($this->travelers > 1) {
            $this->travelers--;
        }
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

        // Check if destination changed significantly
        if ($this->hasSignificantChanges(['destination', 'country_code'])) {
            $this->showConfirmation('Changing the destination will affect weather data and may impact related trip information. Continue?', 'destination_change');
        } else {
            $this->nextStep();
        }
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
            ->findConflictingTrips(Auth::id(), $this->startDate, $this->endDate, $this->trip->id)
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
     * Check if there are significant changes that require confirmation
     */
    private function hasSignificantChanges(array $fields): bool
    {
        foreach ($fields as $field) {
            $originalKey = str_replace(['countryCode', 'startDate', 'endDate'], ['country_code', 'start_date', 'end_date'], $field);
            if (isset($this->originalValues[$originalKey]) && $this->{$field} !== $this->originalValues[$originalKey]) {
                return true;
            }
        }
        return false;
    }

    /**
     * Show confirmation modal for significant changes
     */
    private function showConfirmation(string $message, string $action)
    {
        $this->confirmationMessage = $message;
        $this->pendingAction = $action;
        $this->showConfirmationModal = true;
    }

    /**
     * Confirm the pending action
     */
    public function confirmAction()
    {
        $this->showConfirmationModal = false;

        if ($this->pendingAction === 'destination_change') {
            $this->nextStep();
        } elseif ($this->pendingAction === 'update_trip') {
            $this->performUpdate();
        }

        $this->pendingAction = '';
        $this->confirmationMessage = '';
    }

    /**
     * Cancel the pending action
     */
    public function cancelAction()
    {
        $this->showConfirmationModal = false;
        $this->pendingAction = '';
        $this->confirmationMessage = '';
    }

    /**
     * Update the trip
     */
    public function updateTrip()
    {
        // Prevent double submission
        if ($this->isUpdating) {
            return;
        }

        // Check for significant changes
        if ($this->hasSignificantChanges(['destination', 'countryCode', 'startDate', 'endDate'])) {
            $this->showConfirmation('You are making significant changes to your trip. This may affect related data like expenses and packing items. Continue?', 'update_trip');
            return;
        }

        $this->performUpdate();
    }

    /**
     * Perform the actual trip update
     */
    private function performUpdate()
    {
        $this->isUpdating = true;

        try {
            // Validate form data first
            $this->validate([
                'destination' => 'required|string|max:255',
                'countryCode' => 'required|string|size:2',
                'startDate' => 'required|date|after_or_equal:today',
                'endDate' => 'required|date|after_or_equal:startDate',
                'type' => 'required|in:business,leisure,adventure,family,solo',
                'budget' => 'nullable|numeric|min:0|max:99999999.99',
                'travelers' => 'required|integer|min:1|max:50',
                'notes' => 'nullable|string|max:1000',
            ]);

            // Check for trip conflicts
            $conflicts = $this->tripRepository->findConflictingTrips(
                Auth::id(),
                $this->startDate,
                $this->endDate,
                $this->trip->id
            );

            if ($conflicts->count() > 0) {
                $conflictList = $conflicts->map(function ($trip) {
                    return "{$trip->destination} ({$trip->start_date->format('M j')} - {$trip->end_date->format('M j, Y')})";
                })->join(', ');

                $this->addError('general', "You have conflicting trips: {$conflictList}. Please adjust your dates.");
                $this->isUpdating = false;
                return;
            }

            // Update the trip
            $this->trip->update([
                'destination' => $this->destination,
                'country_code' => $this->countryCode,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate,
                'type' => $this->type,
                'budget' => $this->budget ?: null,
                'notes' => $this->notes,
                'metadata' => array_merge($this->trip->metadata ?? [], [
                    'travelers' => $this->travelers,
                    'updated_at' => now(),
                ]),
            ]);

            // Handle related data updates if dates changed
            if ($this->hasDateChanges()) {
                $this->updateRelatedData();
            }

            $this->clearFormState();
            $this->isUpdating = false;

            session()->flash('success', 'Trip updated successfully!');
            return redirect()->route('trips.show', $this->trip->id);

        } catch (\Exception $e) {
            $this->addError('general', 'An error occurred while updating the trip. Please try again.');
            $this->isUpdating = false;
        }
    }

    /**
     * Check if dates have changed
     */
    private function hasDateChanges(): bool
    {
        return $this->startDate !== $this->originalValues['start_date'] ||
               $this->endDate !== $this->originalValues['end_date'];
    }

    /**
     * Update related data when dates change
     */
    private function updateRelatedData()
    {
        // Update expenses dates if they exist
        $this->trip->expenses()->update([
            'updated_at' => now(),
        ]);

        // Update packing items if needed
        $this->trip->packingItems()->update([
            'updated_at' => now(),
        ]);

        // Refresh weather data
        $this->loadWeatherPreview();
    }

    /**
     * Reset the form to original values
     */
    public function resetForm()
    {
        $this->loadTripData();
        $this->currentStep = 1;
        $this->searchResults = [];
        $this->selectedCountry = [];
        $this->weatherPreview = [];
        $this->conflictingTrips = [];
        $this->showWeatherPreview = false;
        $this->clearFormState();
    }

    public function render()
    {
        return view('livewire.edit-trip');
    }
}
