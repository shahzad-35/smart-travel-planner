<?php

namespace App\Http\Controllers;

use App\Services\ShareTokenService;

class SharedTripController extends Controller
{
    /**
     * Public, read-only view of a shared trip. No auth — access is granted
     * by a valid, unexpired share token.
     */
    public function __invoke(string $token, ShareTokenService $shareTokenService)
    {
        $trip = $shareTokenService->getSharedTrip($token);

        if (!$trip) {
            abort(404);
        }

        $trip->load('packingItems');

        $packingItems = $trip->packingItems;
        $packingProgress = $packingItems->isEmpty()
            ? 0
            : round(($packingItems->where('is_packed', true)->count() / $packingItems->count()) * 100);

        return view('trips.shared', [
            'trip' => $trip,
            'packingProgress' => $packingProgress,
        ]);
    }
}
