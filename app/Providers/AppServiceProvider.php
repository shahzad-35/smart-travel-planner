<?php

namespace App\Providers;

use App\Models\Collection;
use App\Models\Favorite;
use App\Models\Trip;
use App\Models\TripExpense;
use App\Repositories\CollectionRepository;
use App\Repositories\FavoriteRepository;
use App\Repositories\TripExpenseRepository;
use App\Repositories\TripRepository;
use App\Services\CacheService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register services for dependency injection
        $this->app->singleton(CacheService::class);
        
        $this->app->bind(TripRepository::class, function ($app) {
            return new TripRepository(new Trip());
        });

        $this->app->bind(FavoriteRepository::class, function ($app) {
            return new FavoriteRepository(new Favorite());
        });

        $this->app->bind(CollectionRepository::class, function ($app) {
            return new CollectionRepository(new Collection());
        });

        $this->app->bind(TripExpenseRepository::class, function ($app) {
            return new TripExpenseRepository(new TripExpense());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
