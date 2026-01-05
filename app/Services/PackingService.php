<?php

namespace App\Services;

use App\Models\Trip;
use App\Models\PackingItem;
use App\Services\External\WeatherService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PackingService extends BaseService
{
    private WeatherService $weatherService;

    // Default checklist templates by category
    private const DEFAULT_TEMPLATES = [
        'clothing' => [
            'Undergarments (enough for trip duration + extras)',
            'Socks',
            'Sleepwear',
            'Belt',
            'Jewelry',
            'Laundry bag',
        ],
        'toiletries' => [
            'Toothbrush & toothpaste',
            'Shampoo & conditioner',
            'Body wash/soap',
            'Deodorant',
            'Skincare products',
            'Makeup & makeup remover',
            'Hairbrush/comb',
            'Razor & shaving cream',
            'Feminine hygiene products',
            'Contact lenses/solution',
            'Medications & prescriptions',
            'First aid kit',
            'Sunscreen',
            'Insect repellent',
        ],
        'electronics' => [
            'Phone & charger',
            'Headphones',
            'Camera & charger',
            'Laptop & charger',
            'Power bank',
            'Adapters & converters',
        ],
        'documents' => [
            'Passport/ID',
            'Travel insurance',
            'Flight tickets',
            'Hotel reservations',
            'Driver\'s license',
            'Credit cards',
            'Cash',
            'Emergency contacts',
        ],
        'miscellaneous' => [
            'Sunglasses',
            'Umbrella',
            'Reusable water bottle',
            'Snacks',
            'Books/entertainment',
            'Daypack/backpack',
        ],
    ];

    // Weather-based suggestions
    private const WEATHER_SUGGESTIONS = [
        'cold' => [
            'Warm jacket/coats',
            'Gloves',
            'Scarves',
            'Warm hats/beanies',
            'Thermal underwear',
            'Boots',
        ],
        'hot' => [
            'Light clothing',
            'Sunscreen',
            'Hat',
            'Sunglasses',
            'Light jacket for evenings',
            'Sandals',
        ],
        'rainy' => [
            'Umbrella',
            'Raincoat',
            'Waterproof shoes/boots',
            'Waterproof bag',
            'Poncho',
        ],
    ];

    // Trip-type-based suggestions
    private const TRIP_TYPE_SUGGESTIONS = [
        'business' => [
            'Formal wear/suits',
            'Business shirts/blouses',
            'Dress shoes',
            'Tie/bowtie',
            'Laptop',
            'Business documents',
            'Business cards',
            'Presentation materials',
        ],
        'leisure' => [
            'Casual clothing',
            'Comfortable shoes',
            'Camera',
            'Books',
            'Entertainment (games, music)',
            'Swimwear',
            'Beach accessories',
        ],
        'adventure' => [
            'Hiking boots',
            'Outdoor clothing',
            'Backpack',
            'First aid kit',
            'Navigation tools (map, compass)',
            'Camping gear',
            'Water purification',
            'Multi-tool',
        ],
        'family' => [
            'Kids clothing',
            'Kids toiletries',
            'Snacks for kids',
            'Games/toys',
            'Baby supplies (if applicable)',
            'Stroller',
            'Child car seat',
            'Family entertainment',
        ],
    ];

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * Generate packing list for a trip
     */
    public function generatePackingList(Trip $trip): Collection
    {
        $items = collect();

        // Add default templates
        $items = $items->merge($this->getDefaultTemplates());

        // Add weather-based suggestions
        $weatherItems = $this->getWeatherBasedSuggestions($trip);
        $items = $items->merge($weatherItems);

        // Add trip-type-based suggestions
        $typeItems = $this->getTripTypeSuggestions($trip);
        $items = $items->merge($typeItems);

        // Add duration-based suggestions
        $durationItems = $this->getDurationBasedSuggestions($trip);
        $items = $items->merge($durationItems);

        // Remove duplicates and organize by category
        return $this->organizeItems($items);
    }

    /**
     * Save generated packing items to database
     */
    public function savePackingList(Trip $trip, Collection $items): void
    {
        $order = 1;
        foreach ($items as $category => $categoryItems) {
            foreach ($categoryItems as $item) {
                PackingItem::create([
                    'trip_id' => $trip->id,
                    'category' => $category,
                    'item' => $item,
                    'is_packed' => false,
                    'is_custom' => false,
                    'order' => $order++,
                    'created_by' => $trip->user_id,
                ]);
            }
        }
    }

    /**
     * Get default checklist templates
     */
    private function getDefaultTemplates(): Collection
    {
        $templates = collect();
        foreach (self::DEFAULT_TEMPLATES as $category => $items) {
            $templates[$category] = collect($items);
        }
        return $templates;
    }

    /**
     * Get weather-based suggestions
     */
    private function getWeatherBasedSuggestions(Trip $trip): Collection
    {
        $weatherItems = collect();

        // Get weather forecast for the trip destination
        $forecast = $this->weatherService->getForecast($trip->destination);

        if ($forecast) {
            $weatherConditions = $this->analyzeWeatherConditions($forecast);

            foreach ($weatherConditions as $condition) {
                if (isset(self::WEATHER_SUGGESTIONS[$condition])) {
                    foreach (self::WEATHER_SUGGESTIONS[$condition] as $item) {
                        $weatherItems->push($item);
                    }
                }
            }
        }

        return collect(['weather' => $weatherItems]);
    }

    /**
     * Analyze weather conditions from forecast
     */
    private function analyzeWeatherConditions(array $forecast): array
    {
        $conditions = [];

        foreach ($forecast as $day) {
            $temp = $day['temperature'] ?? 20;
            $condition = strtolower($day['condition'] ?? '');

            // Temperature-based
            if ($temp < 10) {
                $conditions[] = 'cold';
            } elseif ($temp > 25) {
                $conditions[] = 'hot';
            }

            // Weather condition-based
            if (str_contains($condition, 'rain') || str_contains($condition, 'shower')) {
                $conditions[] = 'rainy';
            }
        }

        return array_unique($conditions);
    }

    /**
     * Get trip-type-based suggestions
     */
    private function getTripTypeSuggestions(Trip $trip): Collection
    {
        $typeItems = collect();

        if (isset(self::TRIP_TYPE_SUGGESTIONS[$trip->type])) {
            $typeItems = collect(self::TRIP_TYPE_SUGGESTIONS[$trip->type]);
        }

        return collect(['trip_type' => $typeItems]);
    }

    /**
     * Get duration-based suggestions
     */
    private function getDurationBasedSuggestions(Trip $trip): Collection
    {
        $durationItems = collect();
        $days = $trip->start_date->diffInDays($trip->end_date) + 1;

        // For short trips (3 days or less)
        if ($days <= 3) {
            $durationItems = collect([
                'Light packing - minimal clothing',
                'Travel-sized toiletries',
                'Essential documents only',
            ]);
        }
        // For medium trips (4-7 days)
        elseif ($days <= 7) {
            $durationItems = collect([
                'Mix of clothing for versatility',
                'Full toiletries kit',
                'Entertainment for downtime',
            ]);
        }
        // For long trips (8+ days)
        else {
            $durationItems = collect([
                'Seasonal clothing variety',
                'Complete toiletries with backups',
                'Multiple pairs of shoes',
                'Laundry supplies',
                'Extended medication supply',
                'Entertainment variety',
            ]);
        }

        return collect(['duration' => $durationItems]);
    }

    /**
     * Organize items by category and remove duplicates
     */
    private function organizeItems(Collection $items): Collection
    {
        $organized = collect();

        foreach ($items as $category => $categoryItems) {
            if ($categoryItems instanceof Collection) {
                $organized[$category] = $categoryItems->unique()->values();
            }
        }

        return $organized;
    }
}
