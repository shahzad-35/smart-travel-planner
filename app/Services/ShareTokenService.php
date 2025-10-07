<?php

namespace App\Services;

use App\Models\TripShare;
use Illuminate\Support\Facades\Str;
use Illuminate\Support\Carbon;
use App\Notifications\ShareTokenExpiredNotification;

class ShareTokenService extends BaseService
{
    /**
     * Generate a unique token and set expiration.
     */
    public function generateToken(int $ttlMinutes = 60 * 24 * 7): string
    {
        return Str::random(64);
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
        $token = $this->generateToken();
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


