<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserPreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();

        foreach ($users as $user) {
            if (!$user->preference) {
                $user->preference()->create([
                    'temperature_unit' => 'C',
                    'theme' => 'light',
                    'timezone' => 'UTC',
                    'default_packing_items' => ['Passport', 'Phone Charger', 'Toothbrush'],
                    'notifications' => ['email' => true, 'in_app' => true],
                    'privacy_settings' => ['profile_visibility' => 'private', 'trip_sharing' => 'private'],
                ]);
            }
        }
    }
}
