<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\TripExpense;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripExpenseFactory extends Factory
{
    protected $model = TripExpense::class;

    public function definition(): array
    {
        $categories = TripExpense::CATEGORIES;

        $currencies = ['USD', 'PKR'];

        return [
            'trip_id' => Trip::factory(),
            'category' => $this->faker->randomElement($categories),
            'amount' => $this->faker->randomFloat(2, 5, 2000),
            'currency' => $this->faker->randomElement($currencies),
            'description' => $this->faker->optional()->sentence(),
            'expense_date' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'receipt_url' => $this->faker->optional()->imageUrl(600, 800, 'receipt'),
        ];
    }
}


