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
        $this->call(TripSeeder::class);
        $this->call(PackingItemSeeder::class);
        $this->call(FavoriteSeeder::class);
        $this->call(CollectionSeeder::class);
    }
}
