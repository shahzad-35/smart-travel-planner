<?php

namespace App\Jobs;

use App\Models\Trip;
use App\Services\PackingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GeneratePackingList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Trip $trip;

    /**
     * Create a new job instance.
     */
    public function __construct(Trip $trip)
    {
        $this->queue = 'generate-packing-list';
        $this->trip = $trip;
    }

    /**
     * Execute the job.
     */
    public function handle(PackingService $packingService): void
    {
        try {
            Log::info("Starting packing list generation for trip ID: {$this->trip->id}");

            // Generate the packing list
            $items = $packingService->generatePackingList($this->trip);

            // Save the items to the database
            $packingService->savePackingList($this->trip, $items);

            Log::info("Packing list generated successfully for trip ID: {$this->trip->id}, items: " . $items->flatten()->count());
        } catch (\Exception $e) {
            Log::error("Failed to generate packing list for trip ID: {$this->trip->id}, error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("GeneratePackingList job failed for trip ID: {$this->trip->id}, error: " . $exception->getMessage());
    }
}
