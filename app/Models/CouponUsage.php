<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CouponUsage extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::deleted(function (CouponUsage $usage) {
            $usage->coupon?->syncUsedCount();
        });
    }

    protected $fillable = [
        'coupon_id',
        'user_id',
        'order_id',
        'used_at',
    ];

    protected $casts = [
        'used_at' => 'datetime',
    ];

    /**
     * Get the coupon that was used
     */
    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    /**
     * Get the user who used the coupon
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order where the coupon was used
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Remove stale usage rows (deleted orders, cancelled orders), then sync coupon counts.
     */
    public static function pruneOrphanedUsages(): int
    {
        $affectedCouponIds = static::query()
            ->where(function ($query) {
                $query->whereDoesntHave('order')
                    ->orWhereHas('order', fn ($orderQuery) => $orderQuery->where('status', 'cancelled'));
            })
            ->pluck('coupon_id')
            ->unique();

        $deleted = static::query()
            ->where(function ($query) {
                $query->whereDoesntHave('order')
                    ->orWhereHas('order', fn ($orderQuery) => $orderQuery->where('status', 'cancelled'));
            })
            ->delete();

        foreach ($affectedCouponIds as $couponId) {
            Coupon::find($couponId)?->syncUsedCount();
        }

        return $deleted;
    }
}

