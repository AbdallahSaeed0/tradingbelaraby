<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class CourseEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'status',
        'enrolled_at',
        'completed_at',
        'expires_at',
        'progress_percentage',
        'lessons_completed',
        'total_lessons',
        'last_accessed_at',
        'certificate_path',
        'certificate_issued_at',
        'certificate_name',
        'amount_paid',
        'payment_method',
        'transaction_id',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'certificate_issued_at' => 'datetime',
        'progress_percentage' => 'integer',
        'lessons_completed' => 'integer',
        'total_lessons' => 'integer',
        'amount_paid' => 'decimal:2',
    ];

    /**
     * Get the course that owns the enrollment
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user that owns the enrollment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lecture completions for this enrollment
     */
    public function lectureCompletions()
    {
        return $this->hasMany(LectureCompletion::class, 'course_id', 'course_id')
            ->where('user_id', $this->user_id);
    }

    /**
     * Enrollments that grant course content access.
     */
    public function scopeAccessible($query)
    {
        return $query->whereIn('status', ['active', 'completed']);
    }

    /**
     * Enrollments that block re-purchase (including pending payment).
     */
    public function scopeBlockingPurchase($query)
    {
        return $query->whereIn('status', ['active', 'completed', 'pending']);
    }

    /**
     * Scope for active enrollments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for pending enrollments
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Whether this enrollment grants access to course content.
     */
    public function grantsAccess(): bool
    {
        return in_array($this->status, ['active', 'completed'], true) && !$this->isExpired();
    }

    /**
     * Scope for completed enrollments
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for expired enrollments
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    /**
     * Check if enrollment is expired
     */
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }
        return now()->isAfter($this->expires_at);
    }

    /**
     * Check if enrollment is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if enrollment is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && !$this->isExpired();
    }

    /**
     * Mark enrollment as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'progress_percentage' => 100,
        ]);
    }

    /**
     * Update progress
     */
    public function updateProgress(int $lessonsCompleted, int $totalLessons): void
    {
        $progressPercentage = $totalLessons > 0 ? round(($lessonsCompleted / $totalLessons) * 100) : 0;

        $this->update([
            'lessons_completed' => $lessonsCompleted,
            'total_lessons' => $totalLessons,
            'progress_percentage' => $progressPercentage,
            'last_accessed_at' => now(),
        ]);

        // Auto-complete if all lessons are done
        if ($progressPercentage >= 100) {
            $this->markAsCompleted();
        }
    }

    /**
     * Get certificate URL
     */
    public function getCertificateUrlAttribute(): ?string
    {
        if ($this->certificate_path) {
            return asset('storage/' . $this->certificate_path);
        }
        return null;
    }

    /**
     * Actual amount paid after discounts (falls back to linked order when stored amount is list price).
     */
    public function getEffectiveAmountPaidAttribute(): float
    {
        $amount = (float) ($this->amount_paid ?? 0);

        if (!$this->transaction_id || !$this->user_id) {
            return $amount;
        }

        $order = Order::query()
            ->where('user_id', $this->user_id)
            ->where(function ($query) {
                $query->where('order_number', $this->transaction_id)
                    ->orWhere('payment_gateway_id', $this->transaction_id);
            })
            ->with('orderItems')
            ->first();

        if (!$order) {
            return $amount;
        }

        $courseIds = $order->getCourseIds();
        if (!in_array((int) $this->course_id, $courseIds, true)) {
            return $amount;
        }

        if (count($courseIds) === 1) {
            return (float) $order->total;
        }

        $itemTotal = (float) $order->orderItems->sum('price');
        $item = $order->orderItems->firstWhere('course_id', $this->course_id);

        if ($item && $itemTotal > 0) {
            return round((float) $order->total * ((float) $item->price / $itemTotal), 2);
        }

        return $amount;
    }

    /**
     * Get formatted amount paid
     */
    public function getFormattedAmountPaidAttribute(): string
    {
        $amount = $this->effective_amount_paid;
        if ($amount > 0) {
            return 'SAR ' . number_format($amount, 2);
        }
        return 'Free';
    }

    /**
     * Get enrollment duration
     */
    public function getEnrollmentDurationAttribute(): string
    {
        $start = $this->enrolled_at;
        $end = $this->completed_at ?? now();

        $duration = $start->diff($end);

        if ($duration->days > 0) {
            return $duration->days . ' days';
        } elseif ($duration->h > 0) {
            return $duration->h . ' hours';
        } else {
            return $duration->i . ' minutes';
        }
    }

    /**
     * Get time until expiration
     */
    public function getTimeUntilExpirationAttribute(): ?string
    {
        if (!$this->expires_at) {
            return null;
        }

        $now = now();
        if ($now->isAfter($this->expires_at)) {
            return 'Expired';
        }

        $diff = $now->diff($this->expires_at);

        if ($diff->days > 0) {
            return $diff->days . ' days remaining';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hours remaining';
        } else {
            return $diff->i . ' minutes remaining';
        }
    }

    /**
     * Get the progress attribute (alias for progress_percentage)
     */
    public function getProgressAttribute(): int
    {
        return $this->progress_percentage;
    }

    /**
     * Set the progress attribute (alias for progress_percentage)
     */
    public function setProgressAttribute($value): void
    {
        $this->attributes['progress_percentage'] = $value;
    }

    /**
     * Get completed lectures count
     */
    public function getCompletedLecturesAttribute(): int
    {
        return $this->lessons_completed ?? 0;
    }

    /**
     * Get total lectures count
     */
    public function getTotalLecturesAttribute(): int
    {
        return $this->total_lessons ?? 0;
    }

    /**
     * Get last activity
     */
    public function getLastActivityAttribute()
    {
        return $this->last_accessed_at;
    }
}
