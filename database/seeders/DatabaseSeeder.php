<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPreference;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create()->each(function ($user) {
            UserPreference::factory()->create([
                'user_id' => $user->id,
            ]);
        });

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ])->each(function ($user) {
            UserPreference::factory()->create([
                'user_id' => $user->id,
            ]);
        });
    }
}
