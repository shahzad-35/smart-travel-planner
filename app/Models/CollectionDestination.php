<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionDestination extends Model
{
    protected $fillable = [
        'collection_id',
        'destination_data',
    ];

    protected $casts = [
        'destination_data' => 'array',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }
}
