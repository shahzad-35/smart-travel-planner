<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PackingItem;
use App\Models\Trip;

class PackingItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultItems = [
            'business' => [
                'clothing' => ['Suit', 'Dress Shirt', 'Tie'],
                'toiletries' => ['Toothbrush', 'Shampoo', 'Deodorant'],
                'electronics' => ['Laptop', 'Phone Charger', 'USB Drive'],
                'documents' => ['Passport', 'Business Cards', 'Tickets'],
                'miscellaneous' => ['Notebook', 'Pen', 'Snacks'],
            ],
            'leisure' => [
                'clothing' => ['T-shirt', 'Shorts', 'Swimsuit'],
                'toiletries' => ['Sunscreen', 'Toothbrush', 'Shampoo'],
                'electronics' => ['Camera', 'Phone Charger', 'Headphones'],
                'documents' => ['Passport', 'ID Card', 'Travel Insurance'],
                'miscellaneous' => ['Book', 'Sunglasses', 'Water Bottle'],
            ],
            'adventure' => [
                'clothing' => ['Hiking Boots', 'Jacket', 'Hat'],
                'toiletries' => ['Sunscreen', 'Insect Repellent', 'Toothbrush'],
                'electronics' => ['GPS Device', 'Phone Charger', 'Headlamp'],
                'documents' => ['Passport', 'ID Card', 'Permits'],
                'miscellaneous' => ['Backpack', 'Water Bottle', 'First Aid Kit'],
            ],
            'family' => [
                'clothing' => ['Casual Clothes', 'Jacket', 'Comfortable Shoes'],
                'toiletries' => ['Toothbrush', 'Diapers', 'Baby Wipes'],
                'electronics' => ['Tablet', 'Phone Charger', 'Camera'],
                'documents' => ['Passports', 'Health Insurance Cards', 'Tickets'],
                'miscellaneous' => ['Toys', 'Snacks', 'Stroller'],
            ],
            'solo' => [
                'clothing' => ['Comfortable Clothes', 'Jacket', 'Hat'],
                'toiletries' => ['Toothbrush', 'Shampoo', 'Deodorant'],
                'electronics' => ['Phone Charger', 'Camera', 'Power Bank'],
                'documents' => ['Passport', 'ID Card', 'Travel Insurance'],
                'miscellaneous' => ['Book', 'Journal', 'Water Bottle'],
            ],
        ];

        $trips = Trip::all();

        foreach ($trips as $trip) {
            $categoryOrder = 1;
            foreach ($defaultItems[$trip->type] ?? [] as $category => $items) {
                $order = 1;
                foreach ($items as $item) {
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
                $categoryOrder++;
            }
        }
    }
}
