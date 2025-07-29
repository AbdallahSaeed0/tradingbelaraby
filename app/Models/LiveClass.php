<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'instructor_id',
        'name',
        'description',
        'link',
        'scheduled_at',
        'duration_minutes',
        'status',
        'max_participants',
        'current_participants',
        'recording_url',
        'materials',
        'is_free',
        'requires_registration',
        'instructions',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'max_participants' => 'integer',
        'current_participants' => 'integer',
        'materials' => 'array',
        'is_free' => 'boolean',
        'requires_registration' => 'boolean',
    ];

    protected $appends = [
        'formatted_duration',
        'is_upcoming',
        'is_live_now',
        'is_ended',
        'time_until_start',
        'time_since_start',
        'participant_percentage',
        'is_full',
    ];

    /**
     * Get the course that owns the live class
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the instructor that owns the live class
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'instructor_id');
    }

    /**
     * Get the registrations for the live class
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(LiveClassRegistration::class);
    }

    /**
     * Scope for scheduled live classes
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope for live classes currently happening
     */
    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    /**
     * Scope for completed live classes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for upcoming live classes
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now())
            ->where('status', 'scheduled');
    }

    /**
     * Scope for today's live classes
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    /**
     * Scope for this week's live classes
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('scheduled_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope for free live classes
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Get formatted duration attribute
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . 'm';
    }

    /**
     * Get is upcoming attribute
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isFuture();
    }

    /**
     * Get is live now attribute
     */
    public function getIsLiveNowAttribute(): bool
    {
        if (!$this->scheduled_at) {
            return false;
        }

        $startTime = $this->scheduled_at;
        $endTime = $this->scheduled_at->addMinutes($this->duration_minutes);
        $now = now();

        return $now->between($startTime, $endTime) && $this->status === 'live';
    }

    /**
     * Get is ended attribute
     */
    public function getIsEndedAttribute(): bool
    {
        if (!$this->scheduled_at) {
            return false;
        }

        $endTime = $this->scheduled_at->addMinutes($this->duration_minutes);
        return now()->isAfter($endTime);
    }

    /**
     * Get time until start attribute
     */
    public function getTimeUntilStartAttribute(): ?string
    {
        if (!$this->scheduled_at || $this->scheduled_at->isPast()) {
            return null;
        }

        $diff = now()->diff($this->scheduled_at);

        if ($diff->days > 0) {
            return $diff->days . ' days, ' . $diff->h . ' hours';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hours, ' . $diff->i . ' minutes';
        } else {
            return $diff->i . ' minutes';
        }
    }

    /**
     * Get time since start attribute
     */
    public function getTimeSinceStartAttribute(): ?string
    {
        if (!$this->scheduled_at || $this->scheduled_at->isFuture()) {
            return null;
        }

        $diff = $this->scheduled_at->diff(now());

        if ($diff->days > 0) {
            return $diff->days . ' days, ' . $diff->h . ' hours ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hours, ' . $diff->i . ' minutes ago';
        } else {
            return $diff->i . ' minutes ago';
        }
    }

    /**
     * Get participant percentage attribute
     */
    public function getParticipantPercentageAttribute(): float
    {
        if (!$this->max_participants) {
            return 0;
        }

        return round(($this->current_participants / $this->max_participants) * 100, 2);
    }

    /**
     * Get is full attribute
     */
    public function getIsFullAttribute(): bool
    {
        if (!$this->max_participants) {
            return false;
        }

        return $this->current_participants >= $this->max_participants;
    }

    /**
     * Check if user is registered for this live class
     */
    public function isRegisteredByUser(User $user): bool
    {
        return $this->registrations()->where('user_id', $user->id)->exists();
    }

    /**
     * Get user's registration for this live class
     */
    public function getUserRegistration(User $user): ?LiveClassRegistration
    {
        return $this->registrations()->where('user_id', $user->id)->first();
    }

    /**
     * Register user for live class
     */
    public function registerUser(User $user): bool
    {
        if ($this->isRegisteredByUser($user)) {
            return false; // Already registered
        }

        if ($this->is_full) {
            return false; // Class is full
        }

        $registration = new LiveClassRegistration([
            'user_id' => $user->id,
            'live_class_id' => $this->id,
            'registered_at' => now(),
        ]);

        $registration->save();

        // Update participant count
        $this->increment('current_participants');

        return true;
    }

    /**
     * Unregister user from live class
     */
    public function unregisterUser(User $user): bool
    {
        $registration = $this->getUserRegistration($user);

        if (!$registration) {
            return false; // Not registered
        }

        $registration->delete();

        // Update participant count
        $this->decrement('current_participants');

        return true;
    }

    /**
     * Start the live class
     */
    public function start(): void
    {
        $this->update([
            'status' => 'live',
        ]);
    }

    /**
     * End the live class
     */
    public function end(): void
    {
        $this->update([
            'status' => 'completed',
        ]);
    }

    /**
     * Cancel the live class
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }



    /**
     * Get material URLs
     */
    public function getMaterialUrlsAttribute(): array
    {
        if (!$this->materials) {
            return [];
        }

        return array_map(function ($material) {
            return asset('storage/' . $material);
        }, $this->materials);
    }
}
