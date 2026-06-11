<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
            'paypal' => 'PayPal',
            'free' => 'Free',
            'tabby' => 'Tabby',
            'cash_on_delivery' => 'Cash on Delivery',
            default => ucfirst(str_replace('_', ' ', $this->payment_method ?? 'Unknown')),
        };
    }
}
