<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CourseLecture extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'section_id',
        'title',
        'title_ar',
        'description',
        'description_ar',
        'order',
        'content_type',
        'video_file',
        'video_url',
        'document_file',
        'book',
        'content_text',
        'content_text_ar',
        'live_link',
        'live_scheduled_at',
        'is_live',
        'duration_minutes',
        'is_free',
        'is_published',
        'is_completed',
        'attachments',
        'notes',
        'notes_ar',
        'learning_objectives',
        'learning_objectives_ar',
        'lecture_resources',
        'lecture_resources_ar',
        'lecture_type',
        'difficulty_level',
        'prerequisites',
        'prerequisites_ar',
        'tags',
        'tags_ar',
        'transcript',
        'transcript_ar',
        'subtitles',
        'subtitles_ar',
    ];

    protected $casts = [
        'live_scheduled_at' => 'datetime',
        'is_live' => 'boolean',
        'duration_minutes' => 'integer',
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'is_completed' => 'boolean',
        'attachments' => 'array',
        'order' => 'integer',
        'learning_objectives' => 'array',
        'learning_objectives_ar' => 'array',
        'lecture_resources' => 'array',
        'lecture_resources_ar' => 'array',
        'prerequisites' => 'array',
        'prerequisites_ar' => 'array',
        'tags' => 'array',
        'tags_ar' => 'array',
        'subtitles' => 'array',
        'subtitles_ar' => 'array',
    ];

    protected $appends = [
        'formatted_duration',
        'video_url_formatted',
        'is_video',
        'is_document',
        'is_text',
        'is_live_lecture',
        'live_status',
    ];

    /**
     * Get the course that owns the lecture
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the section that owns the lecture
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    /**
     * Get the completions for the lecture
     */
    public function completions(): HasMany
    {
        return $this->hasMany(LectureCompletion::class, 'lecture_id');
    }

    /**
     * Get the quizzes for the lecture
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'lecture_id');
    }

    /**
     * Get the published quizzes for the lecture
     */
    public function publishedQuizzes(): HasMany
    {
        return $this->hasMany(Quiz::class, 'lecture_id')->where('is_published', true);
    }

    /**
     * Get the homework for the lecture
     */
    public function homework(): HasMany
    {
        return $this->hasMany(Homework::class, 'lecture_id');
    }

    /**
     * Get the published homework for the lecture
     */
    public function publishedHomework(): HasMany
    {
        return $this->hasMany(Homework::class, 'lecture_id')->where('is_published', true);
    }

    /**
     * Get the questions and answers for the lecture
     */
    public function questionsAnswers(): HasMany
    {
        return $this->hasMany(QuestionsAnswer::class, 'lecture_id');
    }

    /**
     * Get the published questions and answers for the lecture
     */
    public function publishedQuestionsAnswers(): HasMany
    {
        return $this->hasMany(QuestionsAnswer::class, 'lecture_id')
            ->where('is_public', true)
            ->where('status', 'answered');
    }

    /**
     * Get user's completion for this lecture
     */
    public function getUserCompletion(User $user): ?LectureCompletion
    {
        return $this->completions()->where('user_id', $user->id)->first();
    }

    /**
     * Check if lecture is completed by user
     */
    public function isCompletedByUser(User $user): bool
    {
        $completion = $this->getUserCompletion($user);
        return $completion ? $completion->is_completed : false;
    }

    /**
     * Get user's progress percentage for this lecture
     */
    public function getUserProgressPercentage(User $user): float
    {
        $completion = $this->getUserCompletion($user);
        return $completion ? $completion->progress_percentage : 0;
    }

    /**
     * Scope for published lectures
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for free lectures
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope for live lectures
     */
    public function scopeLive($query)
    {
        return $query->where('is_live', true);
    }

    /**
     * Scope for upcoming live lectures
     */
    public function scopeUpcomingLive($query)
    {
        return $query->where('is_live', true)
            ->where('live_scheduled_at', '>', now());
    }

    /**
     * Scope for lectures by content type
     */
    public function scopeByContentType($query, $type)
    {
        return $query->where('content_type', $type);
    }

    /**
     * Scope for lectures by order
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
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . 'm';
    }

    /**
     * Get video URL formatted attribute
     */
    public function getVideoUrlFormattedAttribute(): ?string
    {
        if ($this->video_url) {
            // Convert YouTube URLs to embed format
            if (str_contains($this->video_url, 'youtube.com/watch')) {
                $videoId = substr($this->video_url, strrpos($this->video_url, 'v=') + 2);
                // Remove any additional parameters after video ID
                if (str_contains($videoId, '&')) {
                    $videoId = substr($videoId, 0, strpos($videoId, '&'));
                }
                return 'https://www.youtube.com/embed/' . $videoId;
            }
            // Handle YouTube short URLs (youtu.be)
            if (str_contains($this->video_url, 'youtu.be/')) {
                $videoId = substr($this->video_url, strrpos($this->video_url, '/') + 1);
                // Remove any query parameters
                if (str_contains($videoId, '?')) {
                    $videoId = substr($videoId, 0, strpos($videoId, '?'));
                }
                return 'https://www.youtube.com/embed/' . $videoId;
            }
            // Convert Vimeo URLs to embed format
            if (str_contains($this->video_url, 'vimeo.com/')) {
                $videoId = substr($this->video_url, strrpos($this->video_url, '/') + 1);
                // Remove any query parameters
                if (str_contains($videoId, '?')) {
                    $videoId = substr($videoId, 0, strpos($videoId, '?'));
                }
                return 'https://player.vimeo.com/video/' . $videoId;
            }
            // Convert Google Drive URLs to embed format
            if (str_contains($this->video_url, 'drive.google.com')) {
                return $this->convertGoogleDriveUrl($this->video_url);
            }
            return $this->video_url;
        }
        return null;
    }

    /**
     * Convert Google Drive URL to embeddable format
     * Supports:
     * - https://drive.google.com/file/d/{FILE_ID}/view
     * - https://drive.google.com/file/d/{FILE_ID}/view?usp=sharing
     * - https://drive.google.com/open?id={FILE_ID}
     * - https://drive.google.com/file/d/{FILE_ID}/preview (already embed format)
     */
    public function convertGoogleDriveUrl(string $url): ?string
    {
        // If already in preview/embed format, return as is
        if (str_contains($url, '/preview')) {
            return $url;
        }

        $fileId = null;

        // Pattern 1: /file/d/{FILE_ID}/
        if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $fileId = $matches[1];
        }
        // Pattern 2: ?id={FILE_ID} or &id={FILE_ID}
        elseif (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $fileId = $matches[1];
        }

        if ($fileId) {
            return 'https://drive.google.com/file/d/' . $fileId . '/preview';
        }

        // Return original URL if pattern not recognized
        return $url;
    }

    /**
     * Get is video attribute
     */
    public function getIsVideoAttribute(): bool
    {
        return $this->content_type === 'video';
    }

    /**
     * Get is document attribute
     */
    public function getIsDocumentAttribute(): bool
    {
        return $this->content_type === 'document';
    }

    /**
     * Get is text attribute
     */
    public function getIsTextAttribute(): bool
    {
        return $this->content_type === 'text';
    }

    /**
     * Get is live lecture attribute
     */
    public function getIsLiveLectureAttribute(): bool
    {
        return $this->content_type === 'live';
    }

    /**
     * Get live status attribute
     */
    public function getLiveStatusAttribute(): string
    {
        if (!$this->is_live || !$this->live_scheduled_at) {
            return 'not_live';
        }

        $now = now();
        $scheduled = $this->live_scheduled_at;

        if ($now->lt($scheduled)) {
            return 'upcoming';
        } elseif ($now->diffInMinutes($scheduled) <= 30) {
            return 'live_now';
        } else {
            return 'ended';
        }
    }

    /**
     * Get video file URL
     */
    public function getVideoFileUrlAttribute(): ?string
    {
        if ($this->video_file) {
            return asset('storage/' . $this->video_file);
        }
        return null;
    }

    /**
     * Get document file URL
     */
    public function getDocumentFileUrlAttribute(): ?string
    {
        if ($this->document_file) {
            return asset('storage/' . $this->document_file);
        }
        return null;
    }

    /**
     * Get attachment URLs
     */
    public function getAttachmentUrlsAttribute(): array
    {
        if (!$this->attachments) {
            return [];
        }

        return array_map(function ($attachment) {
            return asset('storage/' . $attachment);
        }, $this->attachments);
    }

    /**
     * Get next lecture in section
     */
    public function getNextLecture(): ?self
    {
        return $this->section->lectures()
            ->where('order', '>', $this->order)
            ->where('is_published', true)
            ->orderBy('order')
            ->first();
    }

    /**
     * Get previous lecture in section
     */
    public function getPreviousLecture(): ?self
    {
        return $this->section->lectures()
            ->where('order', '<', $this->order)
            ->where('is_published', true)
            ->orderBy('order', 'desc')
            ->first();
    }

    /**
     * Mark lecture as completed for user
     */
    public function markAsCompleted(User $user): void
    {
        $completion = $this->getUserCompletion($user);

        if (!$completion) {
            $completion = new LectureCompletion([
                'user_id' => $user->id,
                'lecture_id' => $this->id,
                'course_id' => $this->course_id,
            ]);
        }

        $completion->is_completed = true;
        $completion->completed_at = now();
        $completion->progress_percentage = 100;
        $completion->watched_entire_video = true;
        $completion->save();
    }

    /**
     * Update lecture progress for user
     */
    public function updateProgress(User $user, float $progressPercentage, int $watchTimeSeconds = 0): void
    {
        $completion = $this->getUserCompletion($user);

        if (!$completion) {
            $completion = new LectureCompletion([
                'user_id' => $user->id,
                'lecture_id' => $this->id,
                'course_id' => $this->course_id,
            ]);
        }

        $completion->progress_percentage = $progressPercentage;
        $completion->watch_time_seconds = $watchTimeSeconds;
        $completion->last_accessed_at = now();

        if ($progressPercentage >= 100) {
            $completion->is_completed = true;
            $completion->completed_at = now();
            $completion->watched_entire_video = true;
        }

        $completion->save();
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
     * Get localized content text based on current locale
     */
    public function getLocalizedContentTextAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->content_text_ar) {
            return $this->content_text_ar;
        }
        return $this->content_text;
    }

    /**
     * Get localized notes based on current locale
     */
    public function getLocalizedNotesAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->notes_ar) {
            return $this->notes_ar;
        }
        return $this->notes;
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
     * Get localized lecture resources based on current locale
     */
    public function getLocalizedLectureResourcesAttribute(): ?array
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->lecture_resources_ar) {
            return $this->lecture_resources_ar;
        }
        return $this->lecture_resources;
    }

    /**
     * Get localized prerequisites based on current locale
     */
    public function getLocalizedPrerequisitesAttribute(): ?array
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->prerequisites_ar) {
            return $this->prerequisites_ar;
        }
        return $this->prerequisites;
    }

    /**
     * Get localized tags based on current locale
     */
    public function getLocalizedTagsAttribute(): ?array
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->tags_ar) {
            return $this->tags_ar;
        }
        return $this->tags;
    }

    /**
     * Get localized transcript based on current locale
     */
    public function getLocalizedTranscriptAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->transcript_ar) {
            return $this->transcript_ar;
        }
        return $this->transcript;
    }

    /**
     * Get localized subtitles based on current locale
     */
    public function getLocalizedSubtitlesAttribute(): ?array
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->subtitles_ar) {
            return $this->subtitles_ar;
        }
        return $this->subtitles;
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
     * Get lecture type display name
     */
    public function getLectureTypeDisplayAttribute(): string
    {
        return match ($this->lecture_type) {
            'theory' => 'Theory',
            'demo' => 'Demo',
            'exercise' => 'Exercise',
            'quiz' => 'Quiz',
            'assignment' => 'Assignment',
            'project' => 'Project',
            'discussion' => 'Discussion',
            default => 'Content',
        };
    }

    /**
     * Get content type icon
     */
    public function getContentTypeIconAttribute(): string
    {
        return match ($this->content_type) {
            'video' => 'fa-play-circle',
            'document' => 'fa-file-pdf',
            'audio' => 'fa-headphones',
            'text' => 'fa-file-text',
            'live' => 'fa-video',
            default => 'fa-file',
        };
    }
}
