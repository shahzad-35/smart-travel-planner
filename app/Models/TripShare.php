<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class TripShare extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'trip_id',
        'shared_with_email',
        'token',
        'permissions',
        'expires_at',
        'created_by',
    ];

    protected $casts = [
        'permissions' => 'array',
        'expires_at' => 'datetime',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
