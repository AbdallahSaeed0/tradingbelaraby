<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'subtotal',
        'total',
        'coupon_id',
        'discount_amount',
        'payment_method',
        'payment_gateway_id',
        'status',
        'billing_first_name',
        'billing_last_name',
        'billing_email',
        'billing_phone',
        'billing_address',
        'billing_city',
        'billing_state',
        'billing_postal_code',
        'billing_country',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the order
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Alias for orderItems (for convenience)
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the courses for this order
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'order_items');
    }

    /**
     * Get the coupon used for this order
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * All course IDs included in this order (direct items + bundle courses).
     */
    public function getCourseIds(): array
    {
        $this->loadMissing(['orderItems.bundle.courses']);

        $courseIds = [];
        foreach ($this->orderItems as $item) {
            if ($item->course_id) {
                $courseIds[] = $item->course_id;
            } elseif ($item->bundle_id && $item->bundle) {
                $courseIds = array_merge($courseIds, $item->bundle->courses->pluck('id')->all());
            }
        }

        return array_values(array_unique($courseIds));
    }

    /**
     * Apply what was paid for to the student's enrollments: lifetime access, or
     * subscription months (stacked on top of any remaining time). Each item is
     * applied once, so calling this again (webhook + redirect) is safe.
     * Call it after the order is paid and its pending enrollments are activated.
     */
    public function applyEnrollmentAccess(): void
    {
        $this->loadMissing(['orderItems.bundle.courses']);

        foreach ($this->orderItems as $item) {
            if ($item->access_applied_at) {
                continue;
            }

            $courseIds = $item->course_id
                ? [$item->course_id]
                : ($item->bundle ? $item->bundle->courses->pluck('id')->all() : []);

            foreach ($courseIds as $courseId) {
                $enrollment = CourseEnrollment::where('user_id', $this->user_id)
                    ->where('course_id', $courseId)
                    ->first();

                if (!$enrollment) {
                    continue;
                }

                if ($item->isSubscription()) {
                    $enrollment->extendSubscription($item->duration_months, $item->subscription_plan_id);
                } else {
                    $enrollment->grantLifetime();
                }
            }

            $item->update(['access_applied_at' => now()]);
        }
    }

    /**
     * Card orders still unpaid after the payment timeout (config/payments.php).
     */
    public function scopeStalePending($query)
    {
        return $query->where('status', 'pending')
            ->whereIn('payment_method', config('payments.expirable_methods', ['visa', 'stripe']))
            ->where('created_at', '<', now()->subMinutes(config('payments.pending_timeout_minutes', 15)));
    }

    /**
     * Whether a gateway confirmation may still complete this order. A card order
     * that timed out ("failed") is accepted too, so a late but real payment is
     * never lost.
     */
    public function isAwaitingPayment(): bool
    {
        return $this->status === 'pending'
            || ($this->status === 'failed'
                && in_array($this->payment_method, config('payments.expirable_methods', ['visa', 'stripe']), true));
    }

    /**
     * Mark an unpaid order as failed: cancel the enrollments it left pending and
     * release its coupon, so the student can buy the course again.
     */
    public function markPaymentFailed(): void
    {
        DB::transaction(function () {
            $order = static::whereKey($this->id)->lockForUpdate()->first();

            if (!$order || $order->status !== 'pending') {
                return;
            }

            // Only enrollments still waiting on THIS order (a newer order for the same
            // course re-points transaction_id, so it is left alone).
            CourseEnrollment::where('user_id', $order->user_id)
                ->whereIn('course_id', $order->getCourseIds())
                ->where('status', 'pending')
                ->where('transaction_id', $order->order_number)
                ->update(['status' => 'cancelled']);

            $couponIds = CouponUsage::where('order_id', $order->id)->pluck('coupon_id')->all();
            CouponUsage::where('order_id', $order->id)->delete();
            foreach (array_unique($couponIds) as $couponId) {
                Coupon::find($couponId)?->syncUsedCount();
            }

            $order->update(['status' => 'failed']);
            $this->status = 'failed';
        });
    }

    /**
     * Fail every timed-out card order, optionally for one user only.
     */
    public static function expireStalePending(?int $userId = null): int
    {
        $orders = static::stalePending()
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->get();

        foreach ($orders as $order) {
            try {
                $order->markPaymentFailed();
            } catch (\Throwable $e) {
                Log::error('Failed to expire pending order', ['order_id' => $order->id, 'error' => $e->getMessage()]);
            }
        }

        return $orders->count();
    }

    public function getTransactionReferenceAttribute(): ?string
    {
        if ($this->payment_method === 'bank_transfer') {
            return $this->payment_gateway_id;
        }

        return $this->payment_gateway_id ?: $this->order_number;
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match ($this->payment_method) {
            'bank_transfer' => 'Bank Transfer',
            'visa' => 'Credit Card',
            'stripe' => 'Stripe',
            'paypal' => 'PayPal',
            'free' => 'Free',
            'tabby' => 'Tabby',
            'cash_on_delivery' => 'Cash on Delivery',
            default => ucfirst(str_replace('_', ' ', $this->payment_method ?? 'Unknown')),
        };
    }
}
