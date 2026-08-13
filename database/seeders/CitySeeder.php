<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the cities table from the bundled dataset at
 * storage/app/cities_offline.json.gz (~150k cities with state linkage and
 * population, built from dr5hn/countries-states-cities + GeoNames).
 */
class CitySeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('app/cities_offline.json.gz');

        if (!is_file($path)) {
            $this->command?->warn("City dataset not found at {$path} — skipping.");
            return;
        }

        $cities = json_decode((string) gzdecode((string) file_get_contents($path)), true);

        if (!is_array($cities)) {
            $this->command?->error('City dataset could not be decoded — skipping.');
            return;
        }

        DB::table('cities')->truncate();

        $rows = array_map(fn($c) => [
            'name' => $c['n'],
            'state_name' => $c['s'],
            'country_code' => $c['c'],
            'latitude' => $c['lat'],
            'longitude' => $c['lng'],
            'population' => $c['p'],
        ], $cities);

        foreach (array_chunk($rows, 2000) as $chunk) {
            DB::table('cities')->insert($chunk);
        }

        $this->command?->info('Seeded ' . City::count() . ' cities.');
    }
}
