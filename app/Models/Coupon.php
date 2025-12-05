<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'scope',
        'course_id',
        'user_scope',
        'user_id',
        'start_date',
        'end_date',
        'usage_limit',
        'per_user_limit',
        'used_count',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the course this coupon applies to
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user this coupon applies to
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the usage records for this coupon
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Check if coupon is valid
     */
    public function isValid(): bool
    {
        // Check if active
        if (!$this->is_active) {
            return false;
        }

        // Check dates
        $now = Carbon::now();
        if ($now->lt($this->start_date) || $now->gt($this->end_date)) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if coupon is valid for a specific user
     */
    public function isValidForUser(User $user): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        // Check user scope
        if ($this->user_scope === 'specific_user' && $this->user_id !== $user->id) {
            return false;
        }

        // Check per-user usage limit
        $userUsageCount = $this->usages()->where('user_id', $user->id)->count();
        if ($userUsageCount >= $this->per_user_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if coupon applies to a specific course
     */
    public function appliesToCourse(Course $course): bool
    {
        if ($this->scope === 'all_courses') {
            return true;
        }

        return $this->course_id === $course->id;
    }

    /**
     * Check if coupon applies to cart items
     */
    public function appliesToCart($cartItems): bool
    {
        if ($this->scope === 'all_courses') {
            return true;
        }

        // Check if the specific course is in the cart
        foreach ($cartItems as $item) {
            if ($item->course_id === $this->course_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate discount amount for a given subtotal
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'percentage') {
            $discount = ($subtotal * $this->discount_value) / 100;
        } else {
            $discount = $this->discount_value;
        }

        // Ensure discount doesn't exceed subtotal
        return min($discount, $subtotal);
    }

    /**
     * Calculate discount for cart items
     */
    public function calculateDiscountForCart($cartItems): float
    {
        if ($this->scope === 'all_courses') {
            $subtotal = $cartItems->sum(function($item) {
                return $item->course ? $item->course->price : 0;
            });
            return $this->calculateDiscount($subtotal);
        }

        // Calculate discount only for the specific course
        $coursePrice = 0;
        foreach ($cartItems as $item) {
            if ($item->course_id === $this->course_id && $item->course) {
                $coursePrice = $item->course->price;
                break;
            }
        }

        return $this->calculateDiscount($coursePrice);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(): void
    {
        $this->increment('used_count');
    }

    /**
     * Scope for active coupons
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for valid coupons (active and within date range)
     */
    public function scopeValid($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
                     ->where('start_date', '<=', $now)
                     ->where('end_date', '>=', $now);
    }
}

