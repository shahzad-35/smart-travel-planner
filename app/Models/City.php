<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name',
        'state_name',
        'country_code',
        'latitude',
        'longitude',
        'population',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'population' => 'integer',
        ];
    }
}
