<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'bundle_id',
        'subscription_plan_id',
    ];

    /**
     * Get the user that owns the cart item
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that owns the cart item
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the bundle that owns the cart item
     */
    public function bundle(): BelongsTo
    {
        return $this->belongsTo(Bundle::class);
    }

    /**
     * Subscription plan chosen for this course (null = lifetime purchase)
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(CourseSubscriptionPlan::class, 'subscription_plan_id');
    }

    public function isSubscription(): bool
    {
        return !$this->isBundle() && $this->subscription_plan_id !== null;
    }

    /**
     * True when the student already has (or had) a subscription to this course,
     * so this item adds more time at the plan's renewal price.
     */
    public function isRenewal(): bool
    {
        if (!$this->isSubscription()) {
            return false;
        }

        return CourseEnrollment::where('user_id', $this->user_id)
            ->where('course_id', $this->course_id)
            ->whereIn('status', ['active', 'completed', 'expired'])
            ->subscription()
            ->exists();
    }

    /**
     * Check if this cart item is for a bundle
     */
    public function isBundle(): bool
    {
        return $this->bundle_id !== null;
    }

    /**
     * Get the price of this cart item
     */
    public function getPrice(): float
    {
        if ($this->isBundle()) {
            return $this->bundle ? $this->bundle->price : 0;
        }
        if ($this->isSubscription()) {
            return $this->subscriptionPlan ? $this->subscriptionPlan->priceFor($this->isRenewal()) : 0;
        }
        return $this->course ? $this->course->price : 0;
    }
}
