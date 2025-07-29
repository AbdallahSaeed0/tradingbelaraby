<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Homework extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'instructor_id',
        'section_id',
        'lecture_id',
        'name',
        'description',
        'instructions',
        'attachment_file',
        'additional_files',
        'max_score',
        'weight_percentage',
        'assigned_at',
        'due_date',
        'late_submission_until',
        'is_published',
        'allow_late_submission',
        'require_file_upload',
        'allow_text_submission',
        'total_assignments',
        'submitted_assignments',
        'graded_assignments',
        'average_score',
        'on_time_submissions',
        'late_submissions',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'assigned_at' => 'datetime',
        'late_submission_until' => 'datetime',
        'is_published' => 'boolean',
        'allow_late_submission' => 'boolean',
        'require_file_upload' => 'boolean',
        'allow_text_submission' => 'boolean',
        'additional_files' => 'array',
        'max_score' => 'integer',
        'weight_percentage' => 'integer',
        'total_assignments' => 'integer',
        'submitted_assignments' => 'integer',
        'graded_assignments' => 'integer',
        'average_score' => 'decimal:2',
        'on_time_submissions' => 'integer',
        'late_submissions' => 'integer',
    ];

    protected $appends = [
        'is_available',
        'is_expired',
        'is_upcoming',
        'is_overdue',
        'formatted_due_date',
        'formatted_start_date',
        'formatted_end_date',
        'time_until_due',
        'time_since_due',
        'submissions_count',
        'average_score',
        'completion_rate',
    ];

    /**
     * Get the course that owns the homework
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the instructor that owns the homework
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'instructor_id');
    }

    /**
     * Get the section that owns the homework
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    /**
     * Get the lecture that owns the homework
     */
    public function lecture(): BelongsTo
    {
        return $this->belongsTo(CourseLecture::class, 'lecture_id');
    }

    /**
     * Get the submissions for the homework
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(HomeworkSubmission::class);
    }

    /**
     * Get the published submissions for the homework
     */
    public function publishedSubmissions(): HasMany
    {
        return $this->hasMany(HomeworkSubmission::class)->whereNotNull('submitted_at');
    }

    /**
     * Scope for published homework
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for free homework
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope for available homework
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope for upcoming homework
     */
    public function scopeUpcoming($query)
    {
        return $query->where('is_published', true)
            ->where('start_date', '>', now());
    }

    /**
     * Scope for overdue homework
     */
    public function scopeOverdue($query)
    {
        return $query->where('is_published', true)
            ->where('due_date', '<', now())
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope for homework by difficulty
     */
    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty_level', $difficulty);
    }

    /**
     * Get is available attribute
     */
    public function getIsAvailableAttribute(): bool
    {
        if (!$this->is_published) {
            return false;
        }

        $now = now();

        // Check start date
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        // Check end date
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Get is expired attribute
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->end_date && now()->gt($this->end_date);
    }

    /**
     * Get is upcoming attribute
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->start_date && now()->lt($this->start_date);
    }

    /**
     * Get is overdue attribute
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->due_date && now()->gt($this->due_date) && !$this->is_expired;
    }

    /**
     * Get formatted due date attribute
     */
    public function getFormattedDueDateAttribute(): ?string
    {
        return $this->due_date ? $this->due_date->format('M d, Y \a\t g:i A') : null;
    }

    /**
     * Get formatted start date attribute
     */
    public function getFormattedStartDateAttribute(): ?string
    {
        return $this->start_date ? $this->start_date->format('M d, Y \a\t g:i A') : null;
    }

    /**
     * Get formatted end date attribute
     */
    public function getFormattedEndDateAttribute(): ?string
    {
        return $this->end_date ? $this->end_date->format('M d, Y \a\t g:i A') : null;
    }

    /**
     * Get time until due attribute
     */
    public function getTimeUntilDueAttribute(): ?string
    {
        if (!$this->due_date || $this->due_date->isPast()) {
            return null;
        }

        $diff = now()->diff($this->due_date);

        if ($diff->days > 0) {
            return $diff->days . ' days, ' . $diff->h . ' hours';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hours, ' . $diff->i . ' minutes';
        } else {
            return $diff->i . ' minutes';
        }
    }

    /**
     * Get time since due attribute
     */
    public function getTimeSinceDueAttribute(): ?string
    {
        if (!$this->due_date || $this->due_date->isFuture()) {
            return null;
        }

        $diff = $this->due_date->diff(now());

        if ($diff->days > 0) {
            return $diff->days . ' days, ' . $diff->h . ' hours ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hours, ' . $diff->i . ' minutes ago';
        } else {
            return $diff->i . ' minutes ago';
        }
    }

    /**
     * Get submissions count attribute
     */
    public function getSubmissionsCountAttribute(): int
    {
        return $this->publishedSubmissions()->count();
    }

    /**
     * Get average score attribute
     */
    public function getAverageScoreAttribute(): float
    {
        $gradedSubmissions = $this->publishedSubmissions()->where('is_graded', true);
        $count = $gradedSubmissions->count();

        if ($count === 0) {
            return 0;
        }

        return round($gradedSubmissions->avg('percentage_score'), 2);
    }

    /**
     * Get completion rate attribute
     */
    public function getCompletionRateAttribute(): float
    {
        $totalEnrolled = $this->course->enrollments()->count();

        if ($totalEnrolled === 0) {
            return 0;
        }

        $submittedCount = $this->publishedSubmissions()->count();
        return round(($submittedCount / $totalEnrolled) * 100, 2);
    }

    /**
     * Get user's submissions for this homework
     */
    public function getUserSubmissions(User $user): HasMany
    {
        return $this->submissions()->where('user_id', $user->id);
    }

    /**
     * Get user's latest submission for this homework
     */
    public function getUserLatestSubmission(User $user): ?HomeworkSubmission
    {
        return $this->getUserSubmissions($user)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Get user's best submission for this homework
     */
    public function getUserBestSubmission(User $user): ?HomeworkSubmission
    {
        return $this->getUserSubmissions($user)
            ->where('is_graded', true)
            ->orderBy('percentage_score', 'desc')
            ->first();
    }

    /**
     * Check if user can submit homework
     */
    public function canUserSubmit(User $user): bool
    {
        if (!$this->is_available) {
            return false;
        }

        // Check if user has reached max submissions
        if ($this->max_submissions && $this->getUserSubmissions($user)->count() >= $this->max_submissions) {
            return false;
        }

        return true;
    }

    /**
     * Check if user has submitted homework
     */
    public function hasUserSubmitted(User $user): bool
    {
        return $this->getUserSubmissions($user)->whereNotNull('submitted_at')->exists();
    }

    /**
     * Check if user has passed homework
     */
    public function hasUserPassed(User $user): bool
    {
        $bestSubmission = $this->getUserBestSubmission($user);
        return $bestSubmission && $bestSubmission->percentage_score >= 60; // 60% passing threshold
    }

    /**
     * Get user's progress for this homework
     */
    public function getUserProgress(User $user): array
    {
        $submissions = $this->getUserSubmissions($user);
        $totalSubmissions = $submissions->count();
        $submittedCount = $submissions->whereNotNull('submitted_at')->count();
        $bestScore = $submissions->where('is_graded', true)->max('percentage_score') ?? 0;
        $hasPassed = $this->hasUserPassed($user);

        return [
            'total_submissions' => $totalSubmissions,
            'submitted_count' => $submittedCount,
            'best_score' => $bestScore,
            'has_passed' => $hasPassed,
            'can_submit' => $this->canUserSubmit($user),
        ];
    }

    /**
     * Calculate late penalty for submission
     */
    public function calculateLatePenalty(\Carbon\Carbon $submittedAt): float
    {
        if (!$this->allow_late_submission || !$this->due_date || $submittedAt->lte($this->due_date)) {
            return 0;
        }

        $daysLate = $this->due_date->diffInDays($submittedAt);
        return min($this->late_penalty_percentage * $daysLate, 100); // Cap at 100%
    }

    /**
     * Get file attachment URLs
     */
    public function getFileAttachmentUrlsAttribute(): array
    {
        if (!$this->file_attachments) {
            return [];
        }

        return array_map(function ($attachment) {
            return asset('storage/' . $attachment);
        }, $this->file_attachments);
    }

    /**
     * Get rubric as formatted text
     */
    public function getFormattedRubricAttribute(): string
    {
        if (!$this->rubric) {
            return '';
        }

        $formatted = '';
        foreach ($this->rubric as $criterion => $details) {
            $formatted .= "**{$criterion}** ({$details['points']} points)\n";
            $formatted .= "{$details['description']}\n\n";
        }

        return trim($formatted);
    }
}
