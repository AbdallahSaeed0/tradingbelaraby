<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Quiz extends Model
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
        'time_limit_minutes',
        'total_questions',
        'total_marks',
        'passing_score',
        'is_randomized',
        'show_results_immediately',
        'allow_retake',
        'max_attempts',
        'is_published',
        'quiz_type',
        'difficulty',
        'available_from',
        'available_until',
        'total_attempts',
        'average_score',
        'passing_attempts',
    ];

    protected $casts = [
        'is_randomized' => 'boolean',
        'show_results_immediately' => 'boolean',
        'allow_retake' => 'boolean',
        'is_published' => 'boolean',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'total_attempts' => 'integer',
        'average_score' => 'integer',
        'passing_attempts' => 'integer',
    ];

    protected $dates = [
        'available_from',
        'available_until',
    ];

    /**
     * Get the course that owns the quiz
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the instructor that created the quiz
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'instructor_id');
    }

    /**
     * Get the section that owns the quiz
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    /**
     * Get the lecture that owns the quiz
     */
    public function lecture(): BelongsTo
    {
        return $this->belongsTo(CourseLecture::class, 'lecture_id');
    }

    /**
     * Get the questions for the quiz
     */
    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    /**
     * Get the attempts for the quiz
     */
    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Get the users who have attempted this quiz
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'quiz_attempts')
            ->withPivot(['score_earned', 'percentage_score', 'is_passed', 'attempt_number'])
            ->withTimestamps();
    }

    /**
     * Check if a user can take this quiz
     */
    public function canUserTake(User $user): bool
    {
        // Check if quiz is published
        if (!$this->is_published) {
            return false;
        }

        // Check availability dates
        if ($this->available_from && now()->lt($this->available_from)) {
            return false;
        }

        if ($this->available_until && now()->gt($this->available_until)) {
            return false;
        }

        // Check if user is enrolled in the course
        if (!$this->course->enrollments()->where('user_id', $user->id)->exists()) {
            return false;
        }

        // Check max attempts
        if ($this->max_attempts > 0) {
            $userAttempts = $this->attempts()->where('user_id', $user->id)->count();
            if ($userAttempts >= $this->max_attempts) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get the remaining attempts for a user
     */
    public function getRemainingAttempts(User $user): int
    {
        if ($this->max_attempts == 0) {
            return -1; // Unlimited
        }

        $usedAttempts = $this->attempts()->where('user_id', $user->id)->count();
        return max(0, $this->max_attempts - $usedAttempts);
    }

    /**
     * Get the best attempt for a user
     */
    public function getBestAttempt(User $user): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('percentage_score', 'desc')
            ->first();
    }

    /**
     * Get the latest attempt for a user
     */
    public function getLatestAttempt(User $user): ?QuizAttempt
    {
        return $this->attempts()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }

    /**
     * Check if quiz is currently available
     */
    public function isAvailable(): bool
    {
        if (!$this->is_published) {
            return false;
        }

        if ($this->available_from && now()->lt($this->available_from)) {
            return false;
        }

        if ($this->available_until && now()->gt($this->available_until)) {
            return false;
        }

        return true;
    }

    /**
     * Get quiz status
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_published) {
            return 'draft';
        }

        if ($this->available_from && now()->lt($this->available_from)) {
            return 'scheduled';
        }

        if ($this->available_until && now()->gt($this->available_until)) {
            return 'expired';
        }

        return 'active';
    }

    /**
     * Get formatted time limit
     */
    public function getFormattedTimeLimitAttribute(): string
    {
        if (!$this->time_limit_minutes) {
            return 'No time limit';
        }

        $hours = floor($this->time_limit_minutes / 60);
        $minutes = $this->time_limit_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }

        return $minutes . ' minutes';
    }

    /**
     * Get quiz difficulty color
     */
    public function getDifficultyColorAttribute(): string
    {
        return match($this->difficulty) {
            'easy' => 'success',
            'medium' => 'warning',
            'hard' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get quiz type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->quiz_type) {
            'practice' => 'Practice Quiz',
            'assessment' => 'Assessment',
            'final_exam' => 'Final Exam',
            default => 'Quiz'
        };
    }

    /**
     * Get the status color for display
     */
    public function getStatusColorAttribute(): string
    {
        switch ($this->status) {
            case 'active':
                return 'success';
            case 'draft':
                return 'warning';
            case 'scheduled':
                return 'info';
            case 'expired':
                return 'danger';
            default:
                return 'secondary';
        }
    }

    /**
     * Scope for published quizzes
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for available quizzes
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_published', true)
            ->where(function($q) {
                $q->whereNull('available_from')
                  ->orWhere('available_from', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('available_until')
                  ->orWhere('available_until', '>=', now());
            });
    }

    /**
     * Scope for quizzes by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('quiz_type', $type);
    }

    /**
     * Scope for quizzes by difficulty
     */
    public function scopeByDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Boot method to set total questions and marks
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($quiz) {
            // Update total questions and marks
            $quiz->total_questions = $quiz->questions()->count();
            $quiz->total_marks = $quiz->questions()->sum('points');
        });
    }
}
