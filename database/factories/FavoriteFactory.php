<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class FavoriteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'destination_name' => $this->faker->city(),
            'country_code' => $this->faker->countryCode(),
            'metadata' => [
                'weather' => [
                    'temperature' => $this->faker->numberBetween(-10, 35),
                    'condition' => $this->faker->randomElement(['sunny', 'cloudy', 'rainy', 'snowy']),
                    'humidity' => $this->faker->numberBetween(30, 90),
                ],
                'notes' => $this->faker->optional(0.7)->sentence(),
            ],
        ];
    }
}
