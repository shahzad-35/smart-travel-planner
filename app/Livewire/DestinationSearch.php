<?php

namespace App\Livewire;

use App\Services\External\CountryService;
use App\Services\External\LocationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Livewire\Attributes\On;
use Livewire\Component;

class DestinationSearch extends Component
{
    public string $searchQuery = '';
    public array $searchResults = ['countries' => [], 'states' => [], 'cities' => []];
    public array $recentSearches = [];
    public bool $showRecentSearches = false;
    public string $selectedCountry = '';
    public ?int $expandedStateIndex = null;
    public array $stateCities = [];
    public int $stateCityTotal = 0;

    private CountryService $countryService;
    private LocationService $locationService;
    private string $recentSearchesKey = 'user_recent_searches:';
    private int $maxRecentSearches = 10;

    public function boot(CountryService $countryService, LocationService $locationService)
    {
        $this->countryService = $countryService;
        $this->locationService = $locationService;
    }

    private function emptyResults(): array
    {
        $this->collapseStateCities();

        return ['countries' => [], 'states' => [], 'cities' => []];
    }

    private function collapseStateCities(): void
    {
        $this->expandedStateIndex = null;
        $this->stateCities = [];
        $this->stateCityTotal = 0;
    }

    /**
     * Expand/collapse a state result to show its major cities.
     */
    public function toggleStateCities(int $index)
    {
        if ($this->expandedStateIndex === $index) {
            $this->collapseStateCities();
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
     * Select a city from an expanded state's list.
     */
    public function selectStateCity(int $cityIndex)
    {
        $city = $this->stateCities[$cityIndex] ?? null;
        if (!$city) {
            return;
        }

        $this->addToRecentSearches([
            'type' => 'city',
            'code' => $city['country_code'],
            'name' => $city['name'],
            'capital' => '',
            'region' => trim(($city['state'] ?? '') . ' ' . $city['country_code']),
            'flag' => $city['flag'] ?? null,
        ]);

        $this->searchQuery = '';
        $this->searchResults = $this->emptyResults();
        $this->showRecentSearches = true;

        return $this->redirectRoute('country.info', ['code' => $city['country_code']]);
    }

    public function getResultCountProperty(): int
    {
        return count($this->searchResults['countries'] ?? [])
            + count($this->searchResults['states'] ?? [])
            + count($this->searchResults['cities'] ?? []);
    }

    public function mount()
    {
        $this->loadRecentSearches();
    }

    public function updatedSearchQuery()
    {
        if (empty($this->searchQuery)) {
            $this->searchResults = $this->emptyResults();
            $this->showRecentSearches = true;
            return;
        }

        $this->showRecentSearches = false;


        if (mb_strlen(trim($this->searchQuery)) > 3) {
            $this->performSearch(trim($this->searchQuery));
        } else {
            $this->searchResults = $this->emptyResults();
        }
    }

    #[On('perform-search')]
    public function performSearch(string $query)
    {
        $query = trim($query);

        if ($query == '') {
            $this->searchResults = $this->emptyResults();
            return;
        }

        try {
            $this->searchResults = $this->locationService->searchDestinations($query, $this->countryService);
        } catch (\Exception $e) {
            $this->searchResults = $this->emptyResults();
            $this->dispatch('notify', type: 'error', message: 'Failed to search destinations. Please try again.');
        }
    }

    public function triggerSearch(): void
    {
        $query = trim($this->searchQuery);
        if ($query === '') {
            return;
        }

        $this->showRecentSearches = false;
        $this->performSearch($query);
    }

    public function selectCountry(string $countryCode)
    {
        $country = collect($this->searchResults['countries'] ?? [])->firstWhere('code', $countryCode)
            ?? collect($this->recentSearches)->firstWhere('code', $countryCode);

        $this->selectedCountry = $countryCode;
        if ($country) {
            $this->addToRecentSearches([
                'type' => 'country',
                'code' => $country['code'],
                'name' => $country['name'],
                'capital' => $country['capital'] ?? '',
                'region' => $country['region'] ?? '',
                'flag' => $country['flag'] ?? null,
            ]);
        }
        $this->searchQuery = '';
        $this->searchResults = $this->emptyResults();
        $this->showRecentSearches = true;

        // Emit event for parent components to handle country selection
        $this->dispatch('country-selected', countryCode: $countryCode);

        return $this->redirectRoute('country.info', ['code' => $countryCode]);
    }

    /**
     * Select a state/province or city result — recorded in recent searches,
     * then opens the country page for its surrounding country.
     */
    public function selectPlace(string $group, int $index)
    {
        $place = $this->searchResults[$group][$index] ?? null;
        if (!$place) {
            return;
        }

        $this->addToRecentSearches([
            'type' => $place['type'],
            'code' => $place['country_code'],
            'name' => $place['name'],
            'capital' => '',
            'region' => trim(($place['state'] ?? $place['country_name'] ?? '') . ' ' . $place['country_code']),
            'flag' => $place['flag'] ?? null,
        ]);

        $this->searchQuery = '';
        $this->searchResults = $this->emptyResults();
        $this->showRecentSearches = true;

        return $this->redirectRoute('country.info', ['code' => $place['country_code']]);
    }

    public function clearSearch()
    {
        $this->searchQuery = '';
        $this->searchResults = $this->emptyResults();
        $this->showRecentSearches = true;
    }

    public function clearHistory()
    {
        if (Auth::check()) {
            try {
                $userId = Auth::id();
                Redis::del($this->recentSearchesKey . $userId);
            } catch (\Throwable $e) {
            }
            $this->recentSearches = [];
            $this->dispatch('notify', type: 'success', message: 'Search history cleared.');
        }
    }

    public function loadRecentSearches()
    {
        if (!Auth::check()) {
            $this->recentSearches = [];
            return;
        }

        try {
            $userId = Auth::id();
            $recentSearches = Redis::lrange($this->recentSearchesKey . $userId, 0, $this->maxRecentSearches - 1);

            $this->recentSearches = array_map(function ($searchData) {
                return json_decode($searchData, true);
            }, $recentSearches);

            $this->recentSearches = collect($this->recentSearches)
                ->sortByDesc('searched_at')
                ->unique(fn($s) => ($s['type'] ?? 'country') . ':' . $s['code'] . ':' . $s['name'])
                ->values()
                ->toArray();
        } catch (\Throwable $e) {
            $this->recentSearches = [];
        }
        $this->showRecentSearches = true;
    }

    private function addToRecentSearches(array $searchData)
    {
        if (!Auth::check()) {
            return;
        }

        $userId = Auth::id();
        $searchData['searched_at'] = now()->toISOString();

        try {
            Redis::lrem($this->recentSearchesKey . $userId, 0, json_encode($searchData));
            Redis::lpush($this->recentSearchesKey . $userId, json_encode($searchData));
            Redis::ltrim($this->recentSearchesKey . $userId, 0, $this->maxRecentSearches - 1);
        } catch (\Throwable $e) {
        }

        $this->loadRecentSearches();
    }

    public function render()
    {
        return view('livewire.destination-search');
    }
}
