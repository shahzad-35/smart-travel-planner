<?php

use Illuminate\Support\Facades\Route;

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

require __DIR__.'/auth.php';
