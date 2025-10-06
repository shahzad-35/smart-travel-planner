<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PackingItem>
 */
class PackingItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category' => $this->faker->randomElement(['clothing', 'toiletries', 'electronics', 'documents', 'miscellaneous']),
            'item' => $this->faker->word,
            'is_packed' => $this->faker->boolean,
            'is_custom' => $this->faker->boolean,
            'order' => $this->faker->numberBetween(1, 100),
            'created_by_user_id' => \App\Models\User::factory(),
        ];
    }
}
