<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id',
        'temperature_unit',
        'theme',
        'timezone',
        'default_packing_items',
        'notifications',
        'privacy_settings',
    ];

    protected $casts = [
        'default_packing_items' => 'array',
        'notifications' => 'array',
        'privacy_settings' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
