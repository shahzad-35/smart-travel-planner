<?php

namespace App\Services;

use App\Models\TripShare;
use Illuminate\Support\Carbon;
use App\Notifications\ShareTokenExpiredNotification;
use Illuminate\Support\Str;

class ShareTokenService extends BaseService
{
    /**
     * Generate a unique token and set expiration.
     */
    public function generateToken(\App\Models\Trip $trip, int $ttlMinutes = 60 * 24 * 7): string
    {
        $token = Str::random(64);
        
        // Create a TripShare record
        TripShare::create([
            'trip_id' => $trip->id,
            'token' => $token,
            'permissions' => ['view'],
            'expires_at' => Carbon::now()->addMinutes($ttlMinutes),
            'created_by' => $trip->user_id,
        ]);
        
        return $token;
    }

    /**
     * Get trip associated with a valid token
     */
    public function getSharedTrip(string $token): ?\App\Models\Trip
    {
        $share = $this->validateToken($token);
        
        if (!$share) {
            return null;
        }
        
        return $share->trip;
    }

    /**
     * Create a TripShare with generated token and expiration.
     */
    public function createShare(
        int $tripId,
        string $sharedWithEmail,
        int $createdByUserId,
        ?array $permissions = null,
        ?int $ttlMinutes = null
    ): TripShare {
        $token = Str::random(64);
        $expiresAt = $ttlMinutes !== null
            ? Carbon::now()->addMinutes($ttlMinutes)
            : Carbon::now()->addWeek();

        return TripShare::create([
            'trip_id' => $tripId,
            'shared_with_email' => $sharedWithEmail,
            'token' => $token,
            'permissions' => $permissions,
            'expires_at' => $expiresAt,
            'created_by' => $createdByUserId,
        ]);
    }

    /**
     * Validate a token and return the TripShare if valid and not expired.
     */
    public function validateToken(string $token): ?TripShare
    {
        $share = TripShare::where('token', $token)->first();
        if (!$share) {
            return null;
        }

        if ($share->expires_at && $share->expires_at->isPast()) {
            // Notify creator that a token expired
            if ($share->creator) {
                $share->creator->notify(new ShareTokenExpiredNotification($share));
            }
            return null;
        }

        return $share;
    }
}
