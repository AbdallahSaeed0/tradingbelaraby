<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Admin;

class HomeworkSubmission extends Model
{
    protected $fillable = [
        'homework_id',
        'user_id',
        'submission_text',
        'submission_file',
        'additional_files',
        'submitted_at',
        'is_late',
        'days_late',
        'score_earned',
        'max_score',
        'percentage_score',
        'feedback',
        'instructor_notes',
        'status',
        'is_graded',
        'graded_at',
        'graded_by',
        'plagiarism_checked',
        'originality_score',
        'plagiarism_report',
        'student_notes',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'is_late' => 'boolean',
        'is_graded' => 'boolean',
        'plagiarism_checked' => 'boolean',
        'additional_files' => 'array',
        'feedback' => 'array',
        'plagiarism_report' => 'array',
        'score_earned' => 'integer',
        'max_score' => 'integer',
        'percentage_score' => 'decimal:2',
        'originality_score' => 'decimal:2',
        'days_late' => 'integer',
    ];

    /**
     * Get the homework that owns the submission
     */
    public function homework(): BelongsTo
    {
        return $this->belongsTo(Homework::class);
    }

    /**
     * Get the user that owns the submission
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the admin who graded the submission
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'graded_by');
    }

    /**
     * Scope for submitted submissions
     */
    public function scopeSubmitted($query)
    {
        return $query->whereNotNull('submitted_at');
    }

    /**
     * Scope for draft submissions
     */
    public function scopeDrafts($query)
    {
        return $query->whereNull('submitted_at');
    }

    /**
     * Scope for graded submissions
     */
    public function scopeGraded($query)
    {
        return $query->where('is_graded', true);
    }

    /**
     * Scope for late submissions
     */
    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }

    /**
     * Scope for submissions by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for submissions by homework
     */
    public function scopeByHomework($query, $homeworkId)
    {
        return $query->where('homework_id', $homeworkId);
    }

    /**
     * Get is late attribute
     */
    public function getIsLateAttribute(): bool
    {
        return (bool) $this->attributes['is_late'] ?? false;
    }

    /**
     * Get is graded attribute
     */
    public function getIsGradedAttribute(): bool
    {
        return (bool) $this->attributes['is_graded'] ?? false;
    }

    /**
     * Get is submitted attribute
     */
    public function getIsSubmittedAttribute(): bool
    {
        return !is_null($this->submitted_at);
    }

    /**
     * Get is passed attribute
     */
    public function getIsPassedAttribute(): bool
    {
        if (!$this->is_graded) {
            return false;
        }
        return ($this->percentage_score ?? 0) >= 60; // 60% passing threshold
    }

    /**
     * Get is failed attribute
     */
    public function getIsFailedAttribute(): bool
    {
        if (!$this->is_graded) {
            return false;
        }
        return ($this->percentage_score ?? 0) < 60;
    }

    /**
     * Get percentage score attribute
     */
    public function getPercentageScoreAttribute(): float
    {
        if (!$this->is_graded || ($this->max_score ?? 0) === 0) {
            return 0;
        }
        return round((($this->score_earned ?? 0) / $this->max_score) * 100, 2);
    }

    /**
     * Get formatted submitted at attribute
     */
    public function getFormattedSubmittedAtAttribute(): ?string
    {
        return $this->submitted_at ? $this->submitted_at->format('M d, Y \a\t g:i A') : null;
    }

    /**
     * Get formatted graded at attribute
     */
    public function getFormattedGradedAtAttribute(): ?string
    {
        return $this->graded_at ? $this->graded_at->format('M d, Y \a\t g:i A') : null;
    }

    /**
     * Get days late attribute
     */
    public function getDaysLateAttribute(): int
    {
        return (int) ($this->attributes['days_late'] ?? 0);
    }

    /**
     * Get formatted feedback attribute
     */
    public function getFormattedFeedbackAttribute(): string
    {
        if (!$this->feedback) {
            return '';
        }

        $formatted = '';
        foreach ($this->feedback as $criterion => $details) {
            $formatted .= "**{$criterion}**\n";
            $formatted .= "Score: {$details['score']}/{$details['max_score']}\n";
            $formatted .= "Comments: {$details['comments']}\n\n";
        }

        return trim($formatted);
    }

    /**
     * Get file attachment URLs
     */
    public function getFileAttachmentUrlsAttribute(): array
    {
        if (!$this->additional_files) {
            return [];
        }

        return array_map(function ($attachment) {
            return asset('storage/' . $attachment);
        }, $this->additional_files);
    }

    /**
     * Submit the homework
     */
    public function submit(): void
    {
        $this->update([
            'submitted_at' => now(),
            'status' => 'submitted',
        ]);

        // Calculate if submission is late
        if ($this->homework->due_date && $this->submitted_at->gt($this->homework->due_date)) {
            $this->update([
                'is_late' => true,
                'days_late' => $this->submitted_at->diffInDays($this->homework->due_date),
                'status' => 'late',
            ]);
        }
    }

    /**
     * Save as draft
     */
    public function saveAsDraft(): void
    {
        $this->update([
            'submitted_at' => null,
            'status' => 'draft',
        ]);
    }

    /**
     * Grade the submission
     */
    public function grade(float $score, array $feedback, int $graderId, string $notes = null): void
    {
        $this->update([
            'score_earned' => $score,
            'feedback' => $feedback,
            'graded_by' => $graderId,
            'graded_at' => now(),
            'instructor_notes' => $notes,
            'is_graded' => true,
            'status' => 'graded',
            'percentage_score' => round(($score / $this->max_score) * 100, 2),
        ]);
    }

    /**
     * Return the submission to student
     */
    public function returnToStudent(): void
    {
        $this->update([
            'status' => 'returned',
        ]);
    }

    /**
     * Get feedback for a specific criterion
     */
    public function getFeedbackForCriterion(string $criterion): ?array
    {
        return $this->feedback[$criterion] ?? null;
    }

    /**
     * Get score for a specific criterion
     */
    public function getScoreForCriterion(string $criterion): float
    {
        $feedback = $this->getFeedbackForCriterion($criterion);
        return $feedback['score'] ?? 0;
    }

    /**
     * Get comments for a specific criterion
     */
    public function getCommentsForCriterion(string $criterion): ?string
    {
        $feedback = $this->getFeedbackForCriterion($criterion);
        return $feedback['comments'] ?? null;
    }

    /**
     * Check if the submission can be graded by the given admin
     */
    public function canBeGradedBy(Admin $admin): bool
    {
        return $this->is_submitted &&
            ($this->homework->instructor_id === $admin->id ||
             $this->homework->course->instructor_id === $admin->id);
    }

    /**
     * Get status attribute
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_submitted) {
            return 'draft';
        }

        if ($this->is_graded) {
            return 'graded';
        }

        if ($this->is_late) {
            return 'late';
        }

        return 'submitted';
    }

    /**
     * Get status color attribute
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft' => 'secondary',
            'submitted' => 'info',
            'late' => 'warning',
            'graded' => 'success',
            'returned' => 'primary',
            default => 'secondary',
        };
    }

    /**
     * Get submission file URL
     */
    public function getSubmissionFileUrlAttribute(): ?string
    {
        return $this->submission_file ? asset('storage/' . $this->submission_file) : null;
    }

    /**
     * Get additional files URLs
     */
    public function getAdditionalFilesUrlsAttribute(): array
    {
        if (!$this->additional_files) {
            return [];
        }

        return array_map(function ($file) {
            return asset('storage/' . $file);
        }, $this->additional_files);
    }

    /**
     * Check if submission has files
     */
    public function getHasFilesAttribute(): bool
    {
        return !empty($this->submission_file) || !empty($this->additional_files);
    }

    /**
     * Check if submission has text
     */
    public function getHasTextAttribute(): bool
    {
        return !empty($this->submission_text);
    }

    /**
     * Get submission type
     */
    public function getSubmissionTypeAttribute(): string
    {
        if ($this->has_files && $this->has_text) {
            return 'mixed';
        } elseif ($this->has_files) {
            return 'files';
        } elseif ($this->has_text) {
            return 'text';
        }
        return 'none';
    }
}
