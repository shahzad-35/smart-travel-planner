<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\CountryInfo;
use App\Livewire\HolidayList;
use App\Http\Controllers\CalendarController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('destinations', \App\Livewire\DestinationSearch::class)
    ->middleware(['auth', 'verified'])
    ->name('destinations');

Route::get('weather', \App\Livewire\WeatherCard::class)
    ->middleware(['auth', 'verified'])
    ->name('weather');

Route::get('weather/compare', \App\Livewire\WeatherComparison::class)
    ->middleware(['auth', 'verified'])
    ->name('weather.compare');

Route::middleware(['auth','verified'])->group(function () {
    Route::get('country', CountryInfo::class)->name('country.info');

    // Trip routes
    Route::get('trips/create', \App\Livewire\CreateTrip::class)->name('trips.create');
    Route::get('trips', \App\Livewire\TripList::class)->name('trips');
    Route::get('dashboard', \App\Livewire\TripDashboard::class)->name('dashboard');

    // ICS endpoints
    Route::get('calendar/holiday', [CalendarController::class, 'holidaySingle'])->name('calendar.holidays.single');
    Route::get('calendar/holidays', [CalendarController::class, 'holidayRange'])->name('calendar.holidays.range');
});

require __DIR__.'/auth.php';
