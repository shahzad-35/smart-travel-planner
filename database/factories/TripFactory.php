<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TripFactory extends Factory
{
    protected $model = Trip::class;

    public function definition(): array
    {
        $types = ['business', 'leisure', 'adventure', 'family', 'solo'];
        $statuses = ['planned', 'ongoing', 'completed', 'cancelled'];

        $startDate = $this->faker->dateTimeBetween('-1 year', '+1 year');
        $endDate = (clone $startDate)->modify('+'.rand(1,60).' days');

        return [
            'destination' => $this->faker->city(),
            'country_code' => $this->faker->countryCode(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'type' => $this->faker->randomElement($types),
            'budget' => $this->faker->randomFloat(2, 100, 10000),
            'status' => $this->faker->randomElement($statuses),
            'notes' => $this->faker->optional()->paragraph(),
            'user_id' => User::factory(),
            'metadata' => [
                'weather' => $this->faker->randomElement(['sunny', 'rainy', 'cloudy']),
                'transport' => $this->faker->randomElement(['car', 'plane', 'train']),
            ],
        ];
    }
}
