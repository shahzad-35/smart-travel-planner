<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;


class CollectionFactory extends Factory
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
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->optional(0.8)->paragraph(),
            'is_public' => $this->faker->boolean(30), // 30% chance of being public
            'theme_color' => $this->faker->hexColor(),
        ];
    }
}
