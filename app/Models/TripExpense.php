<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripExpense extends Model
{
    use HasFactory, SoftDeletes;

    public const CATEGORY_FOOD = 'food';
    public const CATEGORY_TRANSPORT = 'transport';
    public const CATEGORY_ACCOMMODATION = 'accommodation';
    public const CATEGORY_ACTIVITIES = 'activities';
    public const CATEGORY_OTHER = 'other';

    public const CATEGORIES = [
        self::CATEGORY_FOOD,
        self::CATEGORY_TRANSPORT,
        self::CATEGORY_ACCOMMODATION,
        self::CATEGORY_ACTIVITIES,
        self::CATEGORY_OTHER,
    ];

    protected $fillable = [
        'trip_id',
        'category',
        'amount',
        'currency',
        'description',
        'expense_date',
        'receipt_url',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date',
    ];

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
