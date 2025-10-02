<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TripNote extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'note',
        'notable_id',
        'notable_type',
    ];

    public function notable(): MorphTo
    {
        return $this->morphTo();
    }
}
