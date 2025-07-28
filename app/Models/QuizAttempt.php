<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'user_id',
        'attempt_number',
        'started_at',
        'completed_at',
        'submitted_at',
        'score_earned',
        'total_possible_score',
        'percentage_score',
        'is_passed',
        'time_taken_minutes',
        'was_timed_out',
        'status',
        'answers',
        'correct_answers',
        'is_graded',
        'graded_at',
        'graded_by',
        'instructor_notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'answers' => 'array',
        'correct_answers' => 'array',
        'is_passed' => 'boolean',
        'was_timed_out' => 'boolean',
        'is_graded' => 'boolean',
        'score_earned' => 'integer',
        'total_possible_score' => 'integer',
        'percentage_score' => 'decimal:2',
        'time_taken_minutes' => 'integer',
        'attempt_number' => 'integer',
    ];

    protected $appends = [
        'formatted_time_taken',
        'formatted_started_at',
        'formatted_completed_at',
        'formatted_duration',
        'status_color',
        'is_timed_out',
    ];

    /**
     * Get the quiz that owns the attempt
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Get the user that owns the attempt
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the instructor who graded the attempt
     */
    public function gradedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'graded_by');
    }

    /**
     * Get formatted time taken
     */
    public function getFormattedTimeTakenAttribute(): string
    {
        if (!$this->time_taken_minutes) {
            return 'N/A';
        }

        $hours = floor($this->time_taken_minutes / 60);
        $minutes = $this->time_taken_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }

    /**
     * Get formatted started at
     */
    public function getFormattedStartedAtAttribute(): string
    {
        return $this->started_at ? $this->started_at->format('M d, Y g:i A') : 'N/A';
    }

    /**
     * Get formatted completed at
     */
    public function getFormattedCompletedAtAttribute(): string
    {
        return $this->completed_at ? $this->completed_at->format('M d, Y g:i A') : 'N/A';
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->started_at || !$this->completed_at) {
            return 'N/A';
        }

        $duration = $this->started_at->diffInMinutes($this->completed_at);
        $hours = floor($duration / 60);
        $minutes = $duration % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . 'm';
    }

    /**
     * Get status color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'in_progress' => 'warning',
            'completed' => 'success',
            'abandoned' => 'danger',
            'timed_out' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Check if attempt was timed out
     */
    public function getIsTimedOutAttribute(): bool
    {
        return $this->was_timed_out || $this->status === 'timed_out';
    }

    /**
     * Get user's answer for a specific question
     */
    public function getUserAnswer(int $questionId): mixed
    {
        return $this->answers[$questionId]['answer'] ?? null;
    }

    /**
     * Get correct answer for a specific question
     */
    public function getCorrectAnswer(int $questionId): mixed
    {
        return $this->correct_answers[$questionId] ?? null;
    }

    /**
     * Check if answer is correct for a specific question
     */
    public function isAnswerCorrect(int $questionId): bool
    {
        $userAnswer = $this->getUserAnswer($questionId);
        $correctAnswer = $this->getCorrectAnswer($questionId);

        if ($userAnswer === null || $correctAnswer === null) {
            return false;
        }

        // This is a simplified check - in practice, you'd want to use the question's logic
        return $userAnswer == $correctAnswer;
    }

    /**
     * Get number of correct answers
     */
    public function getCorrectAnswersCount(): int
    {
        if (!$this->answers || !$this->correct_answers) {
            return 0;
        }

        $correctCount = 0;
        foreach ($this->answers as $questionId => $answerData) {
            if ($this->isAnswerCorrect($questionId)) {
                $correctCount++;
            }
        }

        return $correctCount;
    }

    /**
     * Get number of incorrect answers
     */
    public function getIncorrectAnswersCount(): int
    {
        if (!$this->answers) {
            return 0;
        }

        $incorrectCount = 0;
        foreach ($this->answers as $questionId => $answerData) {
            if (!$this->isAnswerCorrect($questionId)) {
                $incorrectCount++;
            }
        }

        return $incorrectCount;
    }

    /**
     * Get number of skipped answers
     */
    public function getSkippedAnswersCount(): int
    {
        if (!$this->answers) {
            return 0;
        }

        $skippedCount = 0;
        foreach ($this->answers as $questionId => $answerData) {
            if (!isset($answerData['answer']) || $answerData['answer'] === null || $answerData['answer'] === '') {
                $skippedCount++;
            }
        }

        return $skippedCount;
    }

    /**
     * Get total questions count
     */
    public function getTotalQuestionsCount(): int
    {
        return $this->quiz->questions()->count();
    }

    /**
     * Get number of unanswered questions
     */
    public function getUnansweredCount(): int
    {
        if (!$this->quiz) {
            return 0;
        }

        $totalQuestions = $this->quiz->questions()->count();
        $answeredQuestions = $this->answers ? count($this->answers) : 0;

        return max(0, $totalQuestions - $answeredQuestions);
    }

    /**
     * Calculate time remaining (if quiz has time limit)
     */
    public function getTimeRemaining(): ?int
    {
        if (!$this->quiz->time_limit_minutes || !$this->started_at) {
            return null;
        }

        $elapsedMinutes = $this->started_at->diffInMinutes(now());
        $remainingMinutes = $this->quiz->time_limit_minutes - $elapsedMinutes;

        return max(0, $remainingMinutes);
    }

    /**
     * Check if time is up
     */
    public function isTimeUp(): bool
    {
        $remaining = $this->getTimeRemaining();
        return $remaining !== null && $remaining <= 0;
    }

    /**
     * Get attempt duration in minutes
     */
    public function getDuration(): int
    {
        if (!$this->started_at) {
            return 0;
        }

        $endTime = $this->completed_at ?? now();
        return $this->started_at->diffInMinutes($endTime);
    }

    /**
     * Get attempt progress percentage
     */
    public function getProgressPercentage(): float
    {
        if (!$this->quiz) {
            return 0;
        }

        $totalQuestions = $this->quiz->questions()->count();
        if ($totalQuestions === 0) {
            return 0;
        }

        $answeredQuestions = $this->answers ? count($this->answers) : 0;
        return round(($answeredQuestions / $totalQuestions) * 100, 1);
    }

    /**
     * Get score letter grade
     */
    public function getLetterGrade(): string
    {
        $percentage = $this->percentage_score;

        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    /**
     * Get score grade point
     */
    public function getGradePoint(): float
    {
        $percentage = $this->percentage_score;

        if ($percentage >= 90) return 4.0;
        if ($percentage >= 80) return 3.0;
        if ($percentage >= 70) return 2.0;
        if ($percentage >= 60) return 1.0;
        return 0.0;
    }

    /**
     * Scope for completed attempts
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for passed attempts
     */
    public function scopePassed($query)
    {
        return $query->where('is_passed', true);
    }

    /**
     * Scope for failed attempts
     */
    public function scopeFailed($query)
    {
        return $query->where('is_passed', false);
    }

    /**
     * Scope for attempts by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for attempts by quiz
     */
    public function scopeByQuiz($query, $quizId)
    {
        return $query->where('quiz_id', $quizId);
    }

    /**
     * Scope for recent attempts
     */
    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($attempt) {
            if (!$attempt->attempt_number) {
                $maxAttempt = QuizAttempt::where('quiz_id', $attempt->quiz_id)
                    ->where('user_id', $attempt->user_id)
                    ->max('attempt_number');
                $attempt->attempt_number = ($maxAttempt ?? 0) + 1;
            }

            if (!$attempt->started_at) {
                $attempt->started_at = now();
            }
        });
    }
}
