<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CourseCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_ar', 'slug', 'description', 'description_ar', 'image', 'is_featured'];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    protected $appends = [
        'image_url',
        'courses_count',
        'total_enrolled_students',
        'average_rating'
    ];

    /**
     * Boot method to automatically generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });

        static::updating(function ($category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /**
     * Get the courses for the category
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id');
    }

    /**
     * Get published courses for the category
     */
    public function publishedCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id')->published();
    }

    /**
     * Get featured courses for the category
     */
    public function featuredCourses(): HasMany
    {
        return $this->hasMany(Course::class, 'category_id')->featured();
    }

    /**
     * Get courses count for the category
     */
    public function getCoursesCountAttribute(): int
    {
        return $this->courses()->published()->count();
    }

    /**
     * Get total enrolled students in this category
     */
    public function getTotalEnrolledStudentsAttribute(): int
    {
        return $this->courses()->published()->sum('enrolled_students');
    }

    /**
     * Get average rating for this category
     */
    public function getAverageRatingAttribute(): float
    {
        $courses = $this->courses()->published()->where('average_rating', '>', 0);
        return $courses->count() > 0 ? round($courses->avg('average_rating'), 2) : 0;
    }

    /**
     * Get image URL attribute
     */
    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    /**
     * Scope for featured categories
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for active categories (those with published courses)
     */
    public function scopeActive($query)
    {
        return $query->whereHas('courses', function ($q) {
            $q->published();
        });
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
    public function getLocalizedDescriptionAttribute(): ?string
    {
        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar' && $this->description_ar) {
            return $this->description_ar;
        }
        return $this->description;
    }
}
