<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Trip extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'destination',
        'start_date',
        'end_date',
        'type',
        'budget',
        'status',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(TripNote::class, 'notable');
    }

    public function packingItems(): HasMany
    {
        return $this->hasMany(PackingItem::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(TripExpense::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(TripShare::class);
    }
}
