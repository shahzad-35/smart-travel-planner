<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripShare extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'trip_id',
        'shared_with_email',
        'token',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
