<?php

namespace App\Observers;

use App\Models\Trip;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class TripStatusObserver
{
    /**
     * Handle the Trip "created" event.
     */
    public function created(Trip $trip): void
    {
        // Initialize status history for new trips
        $trip->statusHistories()->create([
            'old_status' => null,
            'new_status' => $trip->status,
            'reason' => 'Trip created',
            'metadata' => ['action' => 'created'],
        ]);

        Log::info("Trip status initialized: {$trip->id} - {$trip->status}");
    }

    /**
     * Handle the Trip "updated" event.
     */
    public function updated(Trip $trip): void
    {
        // Check if status was changed
        if ($trip->wasChanged('status')) {
            $oldStatus = $trip->getOriginal('status');
            $newStatus = $trip->status;

            // Trigger side effects based on status change
            $this->handleStatusChange($trip, $oldStatus, $newStatus);

            Log::info("Trip status changed: {$trip->id} from {$oldStatus} to {$newStatus}");
        }
    }

    /**
     * Handle status change side effects
     */
    private function handleStatusChange(Trip $trip, string $oldStatus, string $newStatus): void
    {
        switch ($newStatus) {
            case 'ongoing':
                $this->handleTripStarted($trip);
                break;
            case 'completed':
                $this->handleTripCompleted($trip);
                break;
            case 'cancelled':
                $this->handleTripCancelled($trip);
                break;
        }

        // Send notification to user
        $this->sendStatusChangeNotification($trip, $oldStatus, $newStatus);
    }

    /**
     * Handle trip started
     */
    private function handleTripStarted(Trip $trip): void
    {
        // Update user statistics
        $this->updateUserStatistics($trip->user_id, 'trips_started', 1);

        // Log the event
        Log::info("Trip started: {$trip->id} - {$trip->destination}");
    }

    /**
     * Handle trip completed
     */
    private function handleTripCompleted(Trip $trip): void
    {
        // Update user statistics
        $this->updateUserStatistics($trip->user_id, 'trips_completed', 1);

        // Calculate trip duration
        $duration = $trip->start_date->diffInDays($trip->end_date) + 1;
        $this->updateUserStatistics($trip->user_id, 'total_trip_days', $duration);

        // Update trip metadata with completion info
        $trip->update([
            'metadata' => array_merge($trip->metadata ?? [], [
                'completed_at' => now(),
                'actual_duration_days' => $duration,
            ])
        ]);

        Log::info("Trip completed: {$trip->id} - {$trip->destination} ({$duration} days)");
    }

    /**
     * Handle trip cancelled
     */
    private function handleTripCancelled(Trip $trip): void
    {
        // Update user statistics
        $this->updateUserStatistics($trip->user_id, 'trips_cancelled', 1);

        Log::info("Trip cancelled: {$trip->id} - {$trip->destination}");
    }

    /**
     * Send notification for status change
     */
    private function sendStatusChangeNotification(Trip $trip, string $oldStatus, string $newStatus): void
    {
        $message = "Your trip to {$trip->destination} status changed from " . ucfirst($oldStatus) . " to " . ucfirst($newStatus);

        Notification::create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'type' => 'App\\Notifications\\TripStatusChanged',
            'notifiable_type' => 'App\\Models\\User',
            'notifiable_id' => $trip->user_id,
            'data' => [
                'trip_id' => $trip->id,
                'destination' => $trip->destination,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'message' => $message,
            ],
            'read_at' => null,
        ]);
    }

    /**
     * Update user statistics
     */
    private function updateUserStatistics(int $userId, string $stat, int $increment): void
    {
        $user = \App\Models\User::find($userId);
        if ($user) {
            $stats = $user->metadata['statistics'] ?? [];
            $stats[$stat] = ($stats[$stat] ?? 0) + $increment;
            $user->update(['metadata' => array_merge($user->metadata ?? [], ['statistics' => $stats])]);
        }
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
