<?php

namespace App\Livewire;

use App\DTOs\CountryDTO;
use App\Services\External\CountryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use Livewire\Attributes\On;
use Livewire\Component;

class DestinationSearch extends Component
{
    public string $searchQuery = '';
    public array $searchResults = [];
    public array $recentSearches = [];
    public bool $isLoading = false;
    public bool $showRecentSearches = false;
    public string $selectedCountry = '';

    private CountryService $countryService;
    private string $recentSearchesKey = 'user_recent_searches:';
    private int $maxRecentSearches = 10;

    public function boot(CountryService $countryService)
    {
        $this->countryService = $countryService;
    }

    public function mount()
    {
        $this->loadRecentSearches();
    }

    public function updatedSearchQuery()
    {
        if (empty($this->searchQuery)) {
            $this->searchResults = [];
            $this->isLoading = false;
            $this->showRecentSearches = true;
            return;
        }

        $this->showRecentSearches = false;

        // Only trigger debounced search when query length > 3 (i.e., 4+ chars)
        if (mb_strlen(trim($this->searchQuery)) > 3) {
            $this->isLoading = true;
            $this->dispatch('search-debounced', query: trim($this->searchQuery));
        } else {
            // Too short: do not search, clear results/loading
            $this->isLoading = false;
            $this->searchResults = [];
        }
    }

    #[On('perform-search')]
    public function performSearch(string $query)
    {
        $query = trim($query);

        if ($query == '') {
            $this->searchResults = [];
            $this->isLoading = false;
            return;
        }

        try {
            $this->isLoading = true;
            $results = $this->countryService->searchCountries($query);
            $this->searchResults = array_map(fn(CountryDTO $country) => $country->toArray(), $results);
            $this->isLoading = false;
        } catch (\Exception $e) {
            $this->searchResults = [];
            $this->isLoading = false;
            session()->flash('error', 'Failed to search destinations. Please try again.');
        }
    }

    public function triggerSearch(): void
    {
        $query = trim($this->searchQuery);
        if ($query === '') {
            return;
        }

        $this->showRecentSearches = false;
        $this->isLoading = true;
        $this->performSearch($query);
    }

    public function selectCountry(string $countryCode)
    {
        $this->selectedCountry = $countryCode;
        $this->addToRecentSearches($countryCode);
        $this->searchQuery = '';
        $this->searchResults = [];
        $this->showRecentSearches = false;
        
        // Emit event for parent components to handle country selection
        $this->dispatch('country-selected', countryCode: $countryCode);
    }

    public function clearSearch()
    {
        $this->searchQuery = '';
        $this->searchResults = [];
        $this->isLoading = false;
        $this->showRecentSearches = true;
    }

    public function clearHistory()
    {
        if (Auth::check()) {
            $userId = Auth::id();
            Redis::del($this->recentSearchesKey . $userId);
            $this->recentSearches = [];
            session()->flash('success', 'Search history cleared successfully.');
        }
    }

    public function loadRecentSearches()
    {
        if (!Auth::check()) {
            $this->recentSearches = [];
            return;
        }

        $userId = Auth::id();
        $recentSearches = Redis::lrange($this->recentSearchesKey . $userId, 0, $this->maxRecentSearches - 1);
        
        $this->recentSearches = array_map(function($searchData) {
            return json_decode($searchData, true);
        }, $recentSearches);
        $this->showRecentSearches = true;
    }

    private function addToRecentSearches(string $countryCode)
    {
        if (!Auth::check()) {
            return;
        }

        $userId = Auth::id();
        $country = collect($this->searchResults)->firstWhere('code', $countryCode);
        
        if (!$country) {
            return;
        }

        $searchData = [
            'code' => $country['code'],
            'name' => $country['name'],
            'capital' => $country['capital'],
            'region' => $country['region'],
            'flag' => $country['flag'],
            'searched_at' => now()->toISOString()
        ];

        // Remove if already exists
        Redis::lrem($this->recentSearchesKey . $userId, 0, json_encode($searchData));
        
        // Add to beginning
        Redis::lpush($this->recentSearchesKey . $userId, json_encode($searchData));
        
        // Keep only the latest searches
        Redis::ltrim($this->recentSearchesKey . $userId, 0, $this->maxRecentSearches - 1);
        
        // Refresh recent searches
        $this->loadRecentSearches();
    }

    public function render()
    {
        return view('livewire.destination-search');
    }
}