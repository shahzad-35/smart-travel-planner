<?php

namespace App\Observers;

use App\Models\PackingItem;

class PackingItemObserver
{
    public function creating(PackingItem $packingItem): void
    {
        if (is_null($packingItem->order)) {
            $maxOrder = PackingItem::where('trip_id', $packingItem->trip_id)->max('order') ?? 0;
            $packingItem->order = $maxOrder + 1;
        }
    }
}
