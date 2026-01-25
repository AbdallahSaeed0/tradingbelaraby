<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'requirements',
        'requirements_ar',
        'image',
        'preview_video_url',
        'duration',
        'price',
        'original_price',
        'certificate_template',
        'certificate_text',
        'certificate_text_ar',
        'enable_certificate',
        'book',
        'category_id',
        'what_to_learn',
        'what_to_learn_ar',
        'faq_course',
        'faq_course_ar',
        'instructor_id',
        'status',
        'is_featured',
        'show_in_top_discounted',
        'show_in_subscription_bundles',
        'show_in_live_meeting',
        'show_in_recent_courses',
        'is_free',
        'total_lessons',
        'total_duration_minutes',
        'enrolled_students',
        'completion_rate',
        'average_rating',
        'total_ratings',
        'total_reviews',
        'meta_title',
        'meta_title_ar',
        'meta_description',
        'meta_description_ar',
        'meta_keywords',
        'meta_keywords_ar',
        'canonical_url',
        'robots',
        'og_title',
        'og_title_ar',
        'og_description',
        'og_description_ar',
        'og_image',
        'twitter_title',
        'twitter_title_ar',
        'twitter_description',
        'twitter_description_ar',
        'twitter_image',
        'structured_data',
        'structured_data_ar',
        'default_language',
        'supported_languages',
    ];

    protected $casts = [
        'what_to_learn' => 'array',
        'what_to_learn_ar' => 'array',
        'faq_course' => 'array',
        'faq_course_ar' => 'array',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'is_featured' => 'boolean',
        'show_in_top_discounted' => 'boolean',
        'show_in_subscription_bundles' => 'boolean',
        'show_in_live_meeting' => 'boolean',
        'show_in_recent_courses' => 'boolean',
        'is_free' => 'boolean',
        'enable_certificate' => 'boolean',
        'structured_data' => 'array',
        'structured_data_ar' => 'array',
        'supported_languages' => 'array',
    ];

    protected $appends = [
        'formatted_price',
        'formatted_original_price',
        'discount_percentage',
        'rating_stars',
        'is_discounted',
    ];

    /**
     * Boot method to automatically generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = Str::slug($course->name);
            }
        });

        static::updating(function ($course) {
            if ($course->isDirty('name') && empty($course->slug)) {
                $course->slug = Str::slug($course->name);
            }
        });
    }

    /**
     * Get the category that owns the course
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    /**
     * Get the instructor that owns the course (legacy - single instructor)
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'instructor_id');
    }

    /**
     * Get the instructors for the course (many-to-many)
     */
    public function instructors()
    {
        return $this->belongsToMany(Admin::class, 'course_instructor', 'course_id', 'instructor_id')
                    ->withTimestamps();
    }

    /**
     * Get the ratings for the course
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(CourseRating::class);
    }

    /**
     * Get the approved ratings for the course
     */
    public function approvedRatings(): HasMany
    {
        return $this->hasMany(CourseRating::class)->where('status', 'approved');
    }

    /**
     * Get the enrollments for the course
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * Get the active enrollments for the course
     */
    public function activeEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class)->where('status', 'active');
    }

    /**
     * Get the completed enrollments for the course
     */
    public function completedEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class)->where('status', 'completed');
    }

    /**
     * Get the sections for the course
     */
    public function sections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->orderBy('order');
    }

    /**
     * Get the published sections for the course
     */
    public function publishedSections(): HasMany
    {
        return $this->hasMany(CourseSection::class)->where('is_published', true)->orderBy('order');
    }

    /**
     * Get the lectures for the course
     */
    public function lectures(): HasMany
    {
        return $this->hasMany(CourseLecture::class);
    }

    /**
     * Get the published lectures for the course
     */
    public function publishedLectures(): HasMany
    {
        return $this->hasMany(CourseLecture::class)->where('is_published', true);
    }

    /**
     * Get the live classes for the course
     */
    public function liveClasses(): HasMany
    {
        return $this->hasMany(LiveClass::class);
    }

    /**
     * Get the upcoming live classes for the course
     */
    public function upcomingLiveClasses(): HasMany
    {
        return $this->hasMany(LiveClass::class)->where('scheduled_at', '>', now())->where('status', 'scheduled');
    }

    /**
     * Get the questions and answers for the course
     */
    public function questionsAnswers(): HasMany
    {
        return $this->hasMany(QuestionsAnswer::class);
    }

    /**
     * Get the published questions and answers for the course
     */
    public function publishedQuestionsAnswers(): HasMany
    {
        return $this->hasMany(QuestionsAnswer::class)
            ->where('is_public', true)
            ->whereIn('status', ['pending', 'answered']);
    }

    /**
     * Get the approved questions and answers for the course (for display)
     */
    public function approvedQuestionsAnswers(): HasMany
    {
        return $this->hasMany(QuestionsAnswer::class)
            ->where('is_public', true)
            ->where('status', 'answered');
    }

    /**
     * Get the quizzes for the course
     */
    public function quizzes(): HasMany
    {
        return $this->hasMany(Quiz::class);
    }

    /**
     * Get the published quizzes for the course
     */
    public function publishedQuizzes(): HasMany
    {
        return $this->hasMany(Quiz::class)->where('is_published', true);
    }

    /**
     * Get the homework for the course
     */
    public function homework(): HasMany
    {
        return $this->hasMany(Homework::class);
    }

    /**
     * Get the published homework for the course
     */
    public function publishedHomework(): HasMany
    {
        return $this->hasMany(Homework::class)->where('is_published', true);
    }

    /**
     * Check if a user is enrolled in this course
     */
    public function isEnrolledBy(User $user): bool
    {
        return $this->enrollments()->where('user_id', $user->id)->exists();
    }

    /**
     * Get user's enrollment for this course
     */
    public function getUserEnrollment(User $user): ?CourseEnrollment
    {
        return $this->enrollments()->where('user_id', $user->id)->first();
    }

    /**
     * Check if a user has rated this course
     */
    public function isRatedBy(User $user): bool
    {
        return $this->ratings()->where('user_id', $user->id)->exists();
    }

    /**
     * Get user's rating for this course
     */
    public function getUserRating(User $user): ?CourseRating
    {
        return $this->ratings()->where('user_id', $user->id)->first();
    }

    /**
     * Scope for published courses
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for featured courses
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for free courses
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Scope for courses displayed in Top Discounted section
     */
    public function scopeTopDiscounted($query)
    {
        return $query->where('show_in_top_discounted', true);
    }

    /**
     * Scope for courses displayed in Subscription Bundles section
     */
    public function scopeSubscriptionBundles($query)
    {
        return $query->where('show_in_subscription_bundles', true);
    }

    /**
     * Scope for courses displayed in Live Meeting section
     */
    public function scopeLiveMeeting($query)
    {
        return $query->where('show_in_live_meeting', true);
    }

    /**
     * Scope for courses displayed in Recent Courses section
     */
    public function scopeRecentCourses($query)
    {
        return $query->where('show_in_recent_courses', true);
    }

    /**
     * Scope for paid courses
     */
    public function scopePaid($query)
    {
        return $query->where('is_free', false);
    }

    /**
     * Scope for courses by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for courses by instructor
     */
    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    /**
     * Scope for courses by level
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope for courses by price range
     */
    public function scopeByPriceRange($query, $min, $max)
    {
        return $query->whereBetween('price', [$min, $max]);
    }

    /**
     * Scope for courses by rating
     */
    public function scopeByRating($query, $minRating)
    {
        return $query->where('average_rating', '>=', $minRating);
    }

    /**
     * Get formatted price attribute
     */
    public function getFormattedPriceAttribute(): string
    {
        if ($this->is_free) {
            return 'Free';
        }
        $currency = config('app.currency', 'SAR');
        return number_format($this->price, 2) . ' ' . $currency;
    }

    /**
     * Get formatted original price attribute
     */
    public function getFormattedOriginalPriceAttribute(): ?string
    {
        if (!$this->original_price) {
            return null;
        }
        $currency = config('app.currency', 'SAR');
        return number_format($this->original_price, 2) . ' ' . $currency;
    }

    /**
     * Get discount percentage attribute
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if ($this->original_price && $this->original_price > $this->price) {
            return round((($this->original_price - $this->price) / $this->original_price) * 100);
        }
        return null;
    }

    /**
     * Get rating stars attribute
     */
    public function getRatingStarsAttribute(): string
    {
        $rating = $this->average_rating;
        $fullStars = floor($rating);
        $halfStar = $rating - $fullStars >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

        $stars = str_repeat('★', $fullStars);
        if ($halfStar) $stars .= '☆';
        $stars .= str_repeat('☆', $emptyStars);

        return $stars;
    }

    /**
     * Get is discounted attribute
     */
    public function getIsDiscountedAttribute(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }

    /**
     * Update course statistics
     */
    public function updateStatistics(): void
    {
        // Update enrollment count
        $this->enrolled_students = $this->enrollments()->where('status', 'active')->count();

        // Update completion rate
        $totalEnrollments = $this->enrollments()->where('status', 'active')->count();
        $completedEnrollments = $this->enrollments()->where('status', 'completed')->count();
        $this->completion_rate = $totalEnrollments > 0 ? round(($completedEnrollments / $totalEnrollments) * 100) : 0;

        // Update rating statistics
        $approvedRatings = $this->approvedRatings();
        $this->total_ratings = $approvedRatings->count();
        $this->total_reviews = $approvedRatings->whereNotNull('review')->count();
        $this->average_rating = $this->total_ratings > 0 ? round($approvedRatings->avg('rating'), 2) : 0;

        $this->save();
    }

    /**
     * Generate certificate for a user
     */
    public function generateCertificate(User $user): ?string
    {
        if (!$this->has_certificate || !$this->certificate_template) {
            return null;
        }

        // Check if user has completed the course
        $enrollment = $this->getUserEnrollment($user);
        if (!$enrollment || $enrollment->status !== 'completed') {
            return null;
        }

        // Generate certificate logic here
        // This would typically involve using a PDF library like DomPDF
        // to merge the template with user data

        $certificatePath = 'certificates/' . $this->id . '_' . $user->id . '_' . time() . '.pdf';

        // Update enrollment with certificate path
        $enrollment->update([
            'certificate_path' => $certificatePath,
            'certificate_issued_at' => now(),
        ]);

        return $certificatePath;
    }

    /**
     * Get course image URL
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-course.jpg');
    }

    /**
     * Get certificate template URL
     */
    public function getCertificateTemplateUrlAttribute(): ?string
    {
        if ($this->certificate_template) {
            return asset('storage/' . $this->certificate_template);
        }
        return null;
    }

    /**
     * Get book URL
     */
    public function getBookUrlAttribute(): ?string
    {
        if ($this->book) {
            return asset('storage/' . $this->book);
        }
        return null;
    }

    /**
     * Get localized name based on current locale
     */
    public function getLocalizedNameAttribute(): string
    {
        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar' && $this->name_ar) {
            return $this->name_ar;
        }
        return $this->name;
    }

    /**
     * Get localized description based on current locale
     */
    public function getLocalizedDescriptionAttribute(): string
    {
        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar' && $this->description_ar) {
            return $this->description_ar;
        }
        return $this->description;
    }

    /**
     * Get the localized requirements
     */
    public function getLocalizedRequirementsAttribute(): ?string
    {
        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar' && $this->requirements_ar) {
            return $this->requirements_ar;
        }
        return $this->requirements;
    }

    /**
     * Get localized what to learn based on current locale
     */
    public function getLocalizedWhatToLearnAttribute(): ?array
    {
        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar' && $this->what_to_learn_ar) {
            return $this->what_to_learn_ar;
        }
        return $this->what_to_learn;
    }

    /**
     * Get localized FAQ based on current locale
     */
    public function getLocalizedFaqCourseAttribute(): ?array
    {
        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar' && $this->faq_course_ar) {
            return $this->faq_course_ar;
        }
        return $this->faq_course;
    }

    /**
     * Get localized meta title based on current locale
     */
    public function getLocalizedMetaTitleAttribute(): ?string
    {
        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar' && $this->meta_title_ar) {
            return $this->meta_title_ar;
        }
        return $this->meta_title;
    }

    /**
     * Get localized meta description based on current locale
     */
    public function getLocalizedMetaDescriptionAttribute(): ?string
    {
        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar' && $this->meta_description_ar) {
            return $this->meta_description_ar;
        }
        return $this->meta_description;
    }

    /**
     * Get localized meta keywords based on current locale
     */
    public function getLocalizedMetaKeywordsAttribute(): ?string
    {
        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar' && $this->meta_keywords_ar) {
            return $this->meta_keywords_ar;
        }
        return $this->meta_keywords;
    }

    /**
     * Check if course supports a specific language
     */
    public function supportsLanguage(string $language): bool
    {
        if (!$this->supported_languages) {
            return $language === $this->default_language;
        }
        return in_array($language, $this->supported_languages);
    }

    /**
     * Get available languages for this course
     */
    public function getAvailableLanguagesAttribute(): array
    {
        $languages = [$this->default_language];
        if ($this->supported_languages) {
            $languages = array_unique(array_merge($languages, $this->supported_languages));
        }
        return $languages;
    }

    /**
     * Get embed URL for preview video
     */
    public function getPreviewVideoEmbedUrlAttribute(): ?string
    {
        if (!$this->preview_video_url) {
            return null;
        }

        $url = $this->preview_video_url;

        // YouTube URL patterns
        if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
        if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return 'https://www.youtube.com/embed/' . $matches[1];
        }
        if (preg_match('/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            return $url; // Already in embed format
        }

        // Vimeo URL patterns
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $matches)) {
            return 'https://player.vimeo.com/video/' . $matches[1];
        }
        if (preg_match('/player\.vimeo\.com\/video\/(\d+)/', $url, $matches)) {
            return $url; // Already in embed format
        }

        return null;
    }
}
