<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveClassRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'live_class_id',
        'user_id',
        'registered_at',
        'attended',
        'joined_at',
        'left_at',
        'attendance_minutes',
        'notes',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'attended' => 'boolean',
        'attendance_minutes' => 'integer',
    ];

    /**
     * Get the live class that owns the registration
     */
    public function liveClass(): BelongsTo
    {
        return $this->belongsTo(LiveClass::class);
    }

    /**
     * Get the user that owns the registration
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark user as attended
     */
    public function markAsAttended(): void
    {
        $this->update([
            'attended' => true,
            'joined_at' => now(),
        ]);
    }

    /**
     * Mark user as left
     */
    public function markAsLeft(): void
    {
        $this->update([
            'left_at' => now(),
        ]);

        // Calculate attendance minutes
        if ($this->joined_at) {
            $attendanceMinutes = $this->joined_at->diffInMinutes($this->left_at);
            $this->update(['attendance_minutes' => $attendanceMinutes]);
        }
    }

    /**
     * Get attendance percentage
     */
    public function getAttendancePercentageAttribute(): float
    {
        if (!$this->liveClass || !$this->attended) {
            return 0;
        }

        $totalDuration = $this->liveClass->duration_minutes;
        if ($totalDuration === 0) {
            return 0;
        }

        return round(($this->attendance_minutes / $totalDuration) * 100, 2);
    }

    /**
     * Get formatted attendance duration
     */
    public function getFormattedAttendanceDurationAttribute(): string
    {
        $hours = floor($this->attendance_minutes / 60);
        $minutes = $this->attendance_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . 'm';
    }
}
