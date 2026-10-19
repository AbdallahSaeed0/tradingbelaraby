<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'course_id',
        'bundle_id',
        'subscription_plan_id',
        'duration_months',
        'is_renewal',
        'access_applied_at',
        'price',
    ];

    protected $casts = [
        'duration_months' => 'integer',
        'is_renewal' => 'boolean',
        'access_applied_at' => 'datetime',
    ];

    /**
     * Get the order that owns the order item
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the course that owns the order item
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the bundle that owns the order item
     */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(CourseSubscriptionPlan::class, 'subscription_plan_id');
    }

    public function isSubscription(): bool
    {
        return $this->course_id !== null && $this->duration_months !== null;
    }

    /**
     * Check if this order item is for a bundle
     */
    public function isBundle(): bool
    {
        return $this->bundle_id !== null;
    }
}
