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
        'slug',
        'description',
        'image',
        'duration',
        'price',
        'original_price',
        'certificate_template',
        'certificate_text',
        'book',
        'category_id',
        'what_to_learn',
        'faq_course',
        'instructor_id',
        'status',
        'is_featured',
        'is_free',
        'total_lessons',
        'total_duration_minutes',
        'enrolled_students',
        'completion_rate',
        'average_rating',
        'total_ratings',
        'total_reviews',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'what_to_learn' => 'array',
        'faq_course' => 'array',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'average_rating' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_free' => 'boolean',
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
     * Get the instructor that owns the course
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'instructor_id');
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
        return $this->is_free ? 'Free' : '$' . number_format($this->price, 2);
    }

    /**
     * Get formatted original price attribute
     */
    public function getFormattedOriginalPriceAttribute(): ?string
    {
        return $this->original_price ? '$' . number_format($this->original_price, 2) : null;
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
}
