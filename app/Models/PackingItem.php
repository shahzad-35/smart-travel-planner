<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackingItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'trip_id',
        'category',
        'item',
        'is_packed',
        'is_custom',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
