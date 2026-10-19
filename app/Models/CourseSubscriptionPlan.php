<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseSubscriptionPlan extends Model
{
    protected $fillable = [
        'course_id',
        'duration_months',
        'price',
        'renewal_price',
        'is_active',
    ];

    protected $casts = [
        'duration_months' => 'integer',
        'price' => 'decimal:2',
        'renewal_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Price for a first-time subscriber, or for an existing subscriber adding more time.
     */
    public function priceFor(bool $isRenewal): float
    {
        if ($isRenewal && $this->renewal_price !== null) {
            return (float) $this->renewal_price;
        }

        return (float) $this->price;
    }

    /**
     * Human-readable duration, e.g. "3 Months" / "1 Year".
     */
    public function getDurationLabelAttribute(): string
    {
        $months = (int) $this->duration_months;

        if ($months % 12 === 0) {
            $years = intdiv($months, 12);
            return $years . ' ' . custom_trans($years === 1 ? 'Year' : 'Years', 'front');
        }

        return $months . ' ' . custom_trans($months === 1 ? 'Month' : 'Months', 'front');
    }
}
