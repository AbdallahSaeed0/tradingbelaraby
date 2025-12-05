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
        return $this->course ? $this->course->price : 0;
    }
}
