<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackingChecklistController extends Controller
{
    /**
     * Display print view for packing checklist
     */
    public function printView($tripId)
    {
        $trip = Trip::with(['packingItems' => function($query) {
            $query->orderBy('category')->orderBy('order');
        }])->findOrFail($tripId);

        // Check if user owns this trip
        if ($trip->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $packingItems = $trip->packingItems
            ->groupBy('category')
            ->map(function ($items) {
                return $items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'item' => $item->item,
                        'category' => $item->category,
                        'is_packed' => $item->is_packed,
                        'is_custom' => $item->is_custom,
                        'order' => $item->order,
                    ];
                })->values()->toArray();
            })
            ->toArray();

        // Calculate progress
        $totalItems = $trip->packingItems->count();
        $packedItems = $trip->packingItems->where('is_packed', true)->count();
        $progress = $totalItems > 0 ? round(($packedItems / $totalItems) * 100) : 0;

        return view('livewire.packing-checklist-print', [
            'trip' => $trip,
            'packingItems' => $packingItems,
            'packingProgress' => $progress,
        ]);
    }
}
