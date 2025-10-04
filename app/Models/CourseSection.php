<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'title_ar',
        'description',
        'description_ar',
        'order',
        'is_published',
        'is_free',
        'total_lectures',
        'total_duration_minutes',
        'learning_objectives',
        'learning_objectives_ar',
        'resources',
        'resources_ar',
        'section_type',
        'difficulty_level',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_free' => 'boolean',
        'total_lectures' => 'integer',
        'total_duration_minutes' => 'integer',
        'learning_objectives' => 'array',
        'learning_objectives_ar' => 'array',
        'resources' => 'array',
        'resources_ar' => 'array',
    ];

    protected $appends = [
        'formatted_duration',
        'lectures_count',
        'completed_lectures_count',
    ];

    /**
     * Get the course that owns the section
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the lectures for the section
     */
    public function lectures(): HasMany
    {
        return $this->hasMany(CourseLecture::class, 'section_id')->orderBy('order');
    }

    /**
     * Get the published lectures for the section
     */
    public function publishedLectures(): HasMany
    {
        return $this->hasMany(CourseLecture::class, 'section_id')
            ->where('is_published', true)
            ->orderBy('order');
    }

    /**
     * Get the quizzes for the section
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'section_id');
    }

    /**
     * Get the published quizzes for the section
     */
    public function publishedQuizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'section_id')->where('is_published', true);
    }

    /**
     * Get the homework for the section
     */
    public function homework(): HasMany
    {
        return $this->hasMany(Homework::class, 'section_id');
    }

    /**
     * Get the published homework for the section
     */
    public function publishedHomework(): HasMany
    {
        return $this->hasMany(Homework::class, 'section_id')->where('is_published', true);
    }

    /**
     * Get the questions and answers for the section
     */
    public function questionsAnswers(): HasMany
    {
        return $this->hasMany(QuestionsAnswer::class, 'section_id');
    }

    /**
     * Get the published questions and answers for the section
     */
    public function publishedQuestionsAnswers(): HasMany
    {
        return $this->hasMany(QuestionsAnswer::class, 'section_id')
            ->where('is_public', true)
            ->where('status', 'answered');
    }

    /**
     * Scope for published sections
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for free sections
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope for sections by order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Get formatted duration attribute
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->total_duration_minutes / 60);
        $minutes = $this->total_duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . 'm';
    }

    /**
     * Get lectures count attribute
     */
    public function getLecturesCountAttribute(): int
    {
        return $this->publishedLectures()->count();
    }

    /**
     * Get completed lectures count for a specific user
     */
    public function getCompletedLecturesCountAttribute(): int
    {
        return 0; // This will be calculated per user
    }

    /**
     * Get user's completed lectures count
     */
    public function getUserCompletedLecturesCount(User $user): int
    {
        return $this->lectures()
            ->whereHas('completions', function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->where('is_completed', true);
            })
            ->count();
    }

    /**
     * Get user's progress percentage for this section
     */
    public function getUserProgressPercentage(User $user): float
    {
        $totalLectures = $this->publishedLectures()->count();
        if ($totalLectures === 0) {
            return 0;
        }

        $completedLectures = $this->getUserCompletedLecturesCount($user);
        return round(($completedLectures / $totalLectures) * 100, 2);
    }

    /**
     * Check if section is completed by user
     */
    public function isCompletedByUser(User $user): bool
    {
        $totalLectures = $this->publishedLectures()->count();
        if ($totalLectures === 0) {
            return false;
        }

        $completedLectures = $this->getUserCompletedLecturesCount($user);
        return $completedLectures >= $totalLectures;
    }

    /**
     * Update section statistics
     */
    public function updateStatistics(): void
    {
        $this->total_lectures = $this->publishedLectures()->count();
        $this->total_duration_minutes = $this->publishedLectures()->sum('duration_minutes');
        $this->save();
    }

    /**
     * Get next section in course
     */
    public function getNextSection(): ?self
    {
        return $this->course->sections()
            ->where('order', '>', $this->order)
            ->where('is_published', true)
            ->orderBy('order')
            ->first();
    }

    /**
     * Get previous section in course
     */
    public function getPreviousSection(): ?self
    {
        return $this->course->sections()
            ->where('order', '<', $this->order)
            ->where('is_published', true)
            ->orderBy('order', 'desc')
            ->first();
    }

    /**
     * Get localized title based on current locale
     */
    public function getLocalizedTitleAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->title_ar) {
            return $this->title_ar;
        }
        return $this->title;
    }

    /**
     * Get localized description based on current locale
     */
    public function getLocalizedDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->description_ar) {
            return $this->description_ar;
        }
        return $this->description;
    }

    /**
     * Get localized learning objectives based on current locale
     */
    public function getLocalizedLearningObjectivesAttribute(): ?array
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->learning_objectives_ar) {
            return $this->learning_objectives_ar;
        }
        return $this->learning_objectives;
    }

    /**
     * Get localized resources based on current locale
     */
    public function getLocalizedResourcesAttribute(): ?array
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->resources_ar) {
            return $this->resources_ar;
        }
        return $this->resources;
    }

    /**
     * Get difficulty level badge color
     */
    public function getDifficultyBadgeColorAttribute(): string
    {
        return match ($this->difficulty_level) {
            'beginner' => 'success',
            'intermediate' => 'warning',
            'advanced' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get section type display name
     */
    public function getSectionTypeDisplayAttribute(): string
    {
        return match ($this->section_type) {
            'theory' => 'Theory',
            'practice' => 'Practice',
            'assessment' => 'Assessment',
            'project' => 'Project',
            'discussion' => 'Discussion',
            default => 'Content',
        };
    }
}
