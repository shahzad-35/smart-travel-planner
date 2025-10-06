<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Trip extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'destination',
        'country_code',
        'start_date',
        'end_date',
        'type',
        'budget',
        'status',
        'notes',
        'user_id',
        'metadata',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(TripNote::class, 'notable');
    }

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'metadata' => 'array',
    ];

    public static function rules(): array
    {
        return [
            'destination' => 'required|string|max:255',
            'country_code' => 'required|string|size:2',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'type' => 'required|in:business,leisure,adventure,family,solo',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|in:planned,ongoing,completed,cancelled',
            'notes' => 'nullable|string',
            'user_id' => 'required|exists:users,id',
            'metadata' => 'nullable|array',
        ];
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
