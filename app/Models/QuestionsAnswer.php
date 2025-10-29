<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionsAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'instructor_id',
        'lecture_id',
        'section_id',
        'question_title',
        'question_content',
        'question_type',
        'answer_content',
        'answer_audio',
        'answered_at',
        'status',
        'is_public',
        'is_anonymous',
        'views_count',
        'helpful_votes',
        'total_votes',
        'tags',
        'priority',
        'moderation_notes',
        'moderated_by',
        'moderated_at',
    ];

    protected $casts = [
        'answered_at' => 'datetime',
        'moderated_at' => 'datetime',
        'is_public' => 'boolean',
        'is_anonymous' => 'boolean',
        'views_count' => 'integer',
        'helpful_votes' => 'integer',
        'total_votes' => 'integer',
        'tags' => 'array',
    ];

    protected $appends = [
        'helpful_percentage',
        'is_answered',
        'is_urgent',
        'formatted_question_date',
        'formatted_answer_date',
        'audio_url',
    ];

    /**
     * Get the course that owns the question
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user that asked the question
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the instructor that answered the question
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'instructor_id');
    }

    /**
     * Get the lecture that the question is about
     */
    public function lecture(): BelongsTo
    {
        return $this->belongsTo(CourseLecture::class, 'lecture_id');
    }

    /**
     * Get the section that the question is about
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    /**
     * Get the admin that moderated the question
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'moderated_by');
    }

    /**
     * Scope for pending questions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for answered questions
     */
    public function scopeAnswered($query)
    {
        return $query->where('status', 'answered');
    }

    /**
     * Scope for public questions
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for approved questions (public and not flagged)
     */
    public function scopeApproved($query)
    {
        return $query->where('is_public', true)
                    ->where('status', '!=', 'flagged');
    }

    /**
     * Scope for urgent questions
     */
    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    /**
     * Scope for questions by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('question_type', $type);
    }

    /**
     * Scope for questions by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for questions by instructor
     */
    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Scope for questions by course
     */
    public function scopeByCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Scope for questions by lecture
     */
    public function scopeByLecture($query, $lectureId)
    {
        return $query->where('lecture_id', $lectureId);
    }

    /**
     * Scope for questions by section
     */
    public function scopeBySection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }

    /**
     * Get helpful percentage attribute
     */
    public function getHelpfulPercentageAttribute(): int
    {
        if ($this->total_votes === 0) {
            return 0;
        }
        return round(($this->helpful_votes / $this->total_votes) * 100);
    }

    /**
     * Get is answered attribute
     */
    public function getIsAnsweredAttribute(): bool
    {
        return $this->status === 'answered' && !empty($this->answer_content);
    }

    /**
     * Get is urgent attribute
     */
    public function getIsUrgentAttribute(): bool
    {
        return $this->priority === 'urgent';
    }

    /**
     * Get formatted question date attribute
     */
    public function getFormattedQuestionDateAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('M d, Y \a\t g:i A') : 'N/A';
    }

    /**
     * Get formatted answer date attribute
     */
    public function getFormattedAnswerDateAttribute(): ?string
    {
        return $this->answered_at ? $this->answered_at->format('M d, Y \a\t g:i A') : null;
    }

    /**
     * Increment view count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Vote helpful
     */
    public function voteHelpful(): void
    {
        $this->increment('helpful_votes');
        $this->increment('total_votes');
    }

    /**
     * Vote not helpful
     */
    public function voteNotHelpful(): void
    {
        $this->increment('total_votes');
    }

    /**
     * Answer the question
     */
    public function answer(string $answerContent, int $instructorId): void
    {
        $this->update([
            'answer_content' => $answerContent,
            'instructor_id' => $instructorId,
            'answered_at' => now(),
            'status' => 'answered',
        ]);
    }

    /**
     * Close the question
     */
    public function close(): void
    {
        $this->update(['status' => 'closed']);
    }

    /**
     * Flag the question
     */
    public function flag(): void
    {
        $this->update(['status' => 'flagged']);
    }

    /**
     * Approve the question
     */
    public function approve(int $moderatorId): void
    {
        $this->update([
            'status' => 'pending',
            'moderated_by' => $moderatorId,
            'moderated_at' => now(),
        ]);
    }

    /**
     * Reject the question
     */
    public function reject(int $moderatorId, string $notes = null): void
    {
        $this->update([
            'status' => 'flagged',
            'moderated_by' => $moderatorId,
            'moderated_at' => now(),
            'moderation_notes' => $notes,
        ]);
    }

    /**
     * Get question author name (respects anonymity)
     */
    public function getAuthorNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anonymous Student';
        }
        return $this->user ? $this->user->name : 'Unknown User';
    }

    /**
     * Get question author avatar (respects anonymity)
     */
    public function getAuthorAvatarAttribute(): ?string
    {
        if ($this->is_anonymous) {
            return null;
        }
        return $this->user ? $this->user->avatar : null;
    }

    /**
     * Check if question can be answered by instructor
     */
    public function canBeAnsweredBy(Admin $instructor): bool
    {
        return $this->status === 'pending' &&
               ($this->course->instructor_id === $instructor->id ||
                $instructor->type === 'admin');
    }

    /**
     * Check if question can be moderated by admin
     */
    public function canBeModeratedBy(Admin $admin): bool
    {
        return $admin->type === 'admin' ||
               $this->course->instructor_id === $admin->id;
    }

    /**
     * Get audio URL attribute
     */
    public function getAudioUrlAttribute(): ?string
    {
        if (!$this->answer_audio) {
            return null;
        }

        return asset('storage/' . $this->answer_audio);
    }

    /**
     * Check if answer has audio
     */
    public function hasAudio(): bool
    {
        return !empty($this->answer_audio);
    }
}
