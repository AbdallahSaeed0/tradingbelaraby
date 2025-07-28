<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LectureCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lecture_id',
        'course_id',
        'is_completed',
        'completed_at',
        'progress_percentage',
        'watch_time_seconds',
        'total_duration_seconds',
        'watched_entire_video',
        'last_accessed_at',
        'notes',
        'bookmarks',
        'playback_speed',
        'quality_preference',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
        'is_completed' => 'boolean',
        'progress_percentage' => 'float',
        'watch_time_seconds' => 'integer',
        'total_duration_seconds' => 'integer',
        'watched_entire_video' => 'boolean',
        'bookmarks' => 'array',
        'playback_speed' => 'float',
    ];

    protected $appends = [
        'formatted_watch_time',
        'formatted_total_duration',
        'formatted_last_accessed',
        'formatted_completed_at',
        'watch_percentage',
        'is_recently_accessed',
    ];

    /**
     * Get the user that owns the completion
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the lecture that owns the completion
     */
    public function lecture(): BelongsTo
    {
        return $this->belongsTo(CourseLecture::class, 'lecture_id');
    }

    /**
     * Get the course that owns the completion
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Scope for completed lectures
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope for in progress lectures
     */
    public function scopeInProgress($query)
    {
        return $query->where('is_completed', false)
            ->where('progress_percentage', '>', 0);
    }

    /**
     * Scope for not started lectures
     */
    public function scopeNotStarted($query)
    {
        return $query->where('progress_percentage', 0);
    }

    /**
     * Scope for recently accessed lectures
     */
    public function scopeRecentlyAccessed($query, $days = 7)
    {
        return $query->where('last_accessed_at', '>=', now()->subDays($days));
    }

    /**
     * Scope for completions by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for completions by course
     */
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope for completions by lecture
     */
    public function scopeByLecture($query, $lectureId)
    {
        return $query->where('lecture_id', $lectureId);
    }

    /**
     * Get formatted watch time attribute
     */
    public function getFormattedWatchTimeAttribute(): string
    {
        if (!$this->watch_time_seconds) {
            return '0m 0s';
        }

        $hours = floor($this->watch_time_seconds / 3600);
        $minutes = floor(($this->watch_time_seconds % 3600) / 60);
        $seconds = $this->watch_time_seconds % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm ' . $seconds . 's';
        }
        return $minutes . 'm ' . $seconds . 's';
    }

    /**
     * Get formatted total duration attribute
     */
    public function getFormattedTotalDurationAttribute(): string
    {
        if (!$this->total_duration_seconds) {
            return '0m 0s';
        }

        $hours = floor($this->total_duration_seconds / 3600);
        $minutes = floor(($this->total_duration_seconds % 3600) / 60);
        $seconds = $this->total_duration_seconds % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm ' . $seconds . 's';
        }
        return $minutes . 'm ' . $seconds . 's';
    }

    /**
     * Get formatted last accessed attribute
     */
    public function getFormattedLastAccessedAttribute(): ?string
    {
        return $this->last_accessed_at ? $this->last_accessed_at->format('M d, Y \a\t g:i A') : null;
    }

    /**
     * Get formatted completed at attribute
     */
    public function getFormattedCompletedAtAttribute(): ?string
    {
        return $this->completed_at ? $this->completed_at->format('M d, Y \a\t g:i A') : null;
    }

    /**
     * Get watch percentage attribute
     */
    public function getWatchPercentageAttribute(): float
    {
        if (!$this->total_duration_seconds) {
            return 0;
        }
        return round(($this->watch_time_seconds / $this->total_duration_seconds) * 100, 2);
    }

    /**
     * Get is recently accessed attribute
     */
    public function getIsRecentlyAccessedAttribute(): bool
    {
        return $this->last_accessed_at && $this->last_accessed_at->isAfter(now()->subDays(7));
    }

    /**
     * Mark lecture as completed
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'progress_percentage' => 100,
            'watched_entire_video' => true,
            'last_accessed_at' => now(),
        ]);
    }

    /**
     * Update progress
     */
    public function updateProgress(float $progressPercentage, int $watchTimeSeconds = 0): void
    {
        $this->update([
            'progress_percentage' => $progressPercentage,
            'watch_time_seconds' => $watchTimeSeconds,
            'last_accessed_at' => now(),
        ]);

        // Auto-complete if progress is 100%
        if ($progressPercentage >= 100) {
            $this->markAsCompleted();
        }
    }

    /**
     * Add bookmark
     */
    public function addBookmark(int $timestamp, string $note = null): void
    {
        $bookmarks = $this->bookmarks ?? [];
        $bookmarks[] = [
            'timestamp' => $timestamp,
            'note' => $note,
            'created_at' => now()->toISOString(),
        ];

        $this->update(['bookmarks' => $bookmarks]);
    }

    /**
     * Remove bookmark
     */
    public function removeBookmark(int $index): void
    {
        $bookmarks = $this->bookmarks ?? [];
        if (isset($bookmarks[$index])) {
            unset($bookmarks[$index]);
            $this->update(['bookmarks' => array_values($bookmarks)]);
        }
    }

    /**
     * Get bookmarks sorted by timestamp
     */
    public function getSortedBookmarksAttribute(): array
    {
        $bookmarks = $this->bookmarks ?? [];
        usort($bookmarks, function ($a, $b) {
            return $a['timestamp'] <=> $b['timestamp'];
        });
        return $bookmarks;
    }

    /**
     * Get formatted bookmark time
     */
    public function getFormattedBookmarkTime(int $timestamp): string
    {
        $minutes = floor($timestamp / 60);
        $seconds = $timestamp % 60;
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Check if user has watched enough to be considered "viewed"
     */
    public function isConsideredViewed(): bool
    {
        // Consider viewed if watched at least 80% or marked as completed
        return $this->is_completed || $this->watch_percentage >= 80;
    }

    /**
     * Get time remaining to complete
     */
    public function getTimeRemainingAttribute(): int
    {
        if (!$this->total_duration_seconds) {
            return 0;
        }
        return max(0, $this->total_duration_seconds - $this->watch_time_seconds);
    }

    /**
     * Get formatted time remaining
     */
    public function getFormattedTimeRemainingAttribute(): string
    {
        $remaining = $this->time_remaining;
        if ($remaining === 0) {
            return 'Completed';
        }

        $minutes = floor($remaining / 60);
        $seconds = $remaining % 60;
        return $minutes . 'm ' . $seconds . 's remaining';
    }

    /**
     * Get completion status for display
     */
    public function getStatusAttribute(): string
    {
        if ($this->is_completed) {
            return 'Completed';
        }

        if ($this->progress_percentage > 0) {
            return 'In Progress';
        }

        return 'Not Started';
    }

    /**
     * Get completion status color for display
     */
    public function getStatusColorAttribute(): string
    {
        switch ($this->status) {
            case 'Completed':
                return 'green';
            case 'In Progress':
                return 'blue';
            case 'Not Started':
                return 'gray';
            default:
                return 'gray';
        }
    }

    /**
     * Get progress bar width for display
     */
    public function getProgressBarWidthAttribute(): string
    {
        return $this->progress_percentage . '%';
    }
}
