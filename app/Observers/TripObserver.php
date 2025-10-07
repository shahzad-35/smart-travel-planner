<?php

namespace App\Observers;

use App\Models\Trip;
use Illuminate\Support\Facades\Log;

class TripObserver
{
    /**
     * Handle the Trip "created" event.
     */
    public function created(Trip $trip): void
    {
        Log::info("Trip created: {$trip->id}");
    }

    /**
     * Handle the Trip "updated" event.
     */
    public function updated(Trip $trip): void
    {
        Log::info("Trip updated: {$trip->id}");
    }

    /**
     * Handle the Trip "deleted" event.
     */
    public function deleted(Trip $trip): void
    {
        Log::info("Trip deleted: {$trip->id}");
    }

    /**
     * Handle the Trip "restored" event.
     */
    public function restored(Trip $trip): void
    {
        Log::info("Trip restored: {$trip->id}");
    }

    /**
     * Handle the Trip "force deleted" event.
     */
    public function forceDeleted(Trip $trip): void
    {
        Log::info("Trip force deleted: {$trip->id}");
    }
}
