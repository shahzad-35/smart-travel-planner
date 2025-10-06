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
        'order',
        'created_by',
    ];

    protected $casts = [
        'is_packed' => 'boolean',
        'is_custom' => 'boolean',
        'order' => 'integer',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
