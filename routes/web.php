<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CountryInfo;
use App\Livewire\HolidayList;
use App\Http\Controllers\CalendarController;

Route::view('/', 'welcome');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('destinations', \App\Livewire\DestinationSearch::class)
    ->middleware(['auth', 'verified'])
    ->name('destinations');

Route::get('settings', \App\Livewire\UserPreferences::class)
    ->middleware(['auth'])
    ->name('settings');

Route::get('weather', \App\Livewire\WeatherCard::class)
    ->middleware(['auth', 'verified'])
    ->name('weather');

Route::get('weather/compare', \App\Livewire\WeatherComparison::class)
    ->middleware(['auth', 'verified'])
    ->name('weather.compare');

// Public shared packing checklist (no auth required)
Route::get('packing-checklist/shared/{token}', \App\Livewire\PackingChecklist::class)
    ->name('packing-checklist.share');

// Public shared trip summary (no auth required, token-gated)
Route::get('trips/shared/{token}', \App\Http\Controllers\SharedTripController::class)
    ->name('trips.shared');

Route::middleware(['auth','verified'])->group(function () {
    Route::get('country', CountryInfo::class)->name('country.info');

    // Trip routes
    Route::get('trips/create', \App\Livewire\CreateTrip::class)->name('trips.create');
    Route::get('trips/{id}/edit', \App\Livewire\EditTrip::class)->name('trips.edit');
    Route::get('trips/{id}', \App\Livewire\TripDetails::class)->name('trips.show');
    Route::get('trips', \App\Livewire\TripList::class)->name('trips.listing');
    Route::get('dashboard', \App\Livewire\TripDashboard::class)->name('dashboard');

    // Packing checklist routes
    Route::get('packing-checklist/{tripId}', \App\Livewire\PackingChecklist::class)
        ->name('packing-checklist.show');

    Route::get('packing-checklist/{id}/print', [\App\Http\Controllers\PackingChecklistController::class, 'printView'])
        ->name('packing-checklist.print');

    // ICS endpoints
    Route::get('calendar/holiday', [CalendarController::class, 'holidaySingle'])->name('calendar.holidays.single');
    Route::get('calendar/holidays', [CalendarController::class, 'holidayRange'])->name('calendar.holidays.range');
});

require __DIR__.'/auth.php';
