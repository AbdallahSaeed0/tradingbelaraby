<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'slug',
        'slug_ar',
        'custom_slug',
        'image',
        'image_ar',
        'description',
        'description_ar',
        'excerpt',
        'excerpt_ar',
        'category_id',
        'author_id',
        'status',
        'views_count',
        'is_featured',
        'meta_title',
        'meta_title_ar',
        'meta_description',
        'meta_description_ar',
        'meta_keywords',
        'meta_keywords_ar',
        'tags',
        'reading_time'
    ];

    protected $casts = [
        'views_count' => 'integer',
        'is_featured' => 'boolean',
        'tags' => 'array',
        'meta_keywords' => 'array',
        'meta_keywords_ar' => 'array',
        'reading_time' => 'integer',
    ];

    protected $appends = [
        'image_url',
        'image_ar_url',
        'category_name',
        'author_name'
    ];

    /**
     * Get the category that owns the blog
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    /**
     * Get the author that owns the blog
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'author_id');
    }

    /**
     * Scope for published blogs
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for featured blogs
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope for blogs by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope for active category blogs
     */
    public function scopeWithActiveCategory($query)
    {
        return $query->whereHas('category', function ($q) {
            $q->where('status', 'active');
        });
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
     * Get Arabic image URL attribute
     */
    public function getImageArUrlAttribute(): ?string
    {
        if ($this->image_ar) {
            return asset('storage/' . $this->image_ar);
        }
        return null;
    }

    /**
     * Get category name attribute
     */
    public function getCategoryNameAttribute(): ?string
    {
        return $this->category?->name;
    }

    /**
     * Get author name attribute
     */
    public function getAuthorNameAttribute(): ?string
    {
        return $this->author?->name;
    }

    /**
     * Increment views count
     */
    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    /**
     * Check if blog is published
     */
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    /**
     * Check if blog is featured
     */
    public function isFeatured(): bool
    {
        return $this->is_featured;
    }

    /**
     * Get excerpt or generate from description
     */
    public function getExcerptOrGenerated(): string
    {
        if ($this->excerpt) {
            return $this->excerpt;
        }

        return Str::limit(strip_tags($this->description), 150);
    }

    /**
     * Get excerpt or generate from description for Arabic
     */
    public function getExcerptArOrGenerated(): string
    {
        if ($this->excerpt_ar) {
            return $this->excerpt_ar;
        }

        return Str::limit(strip_tags($this->description_ar), 150);
    }

    /**
     * Get localized title based on current language
     */
    public function getLocalizedTitle(): string
    {
        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar' && $this->title_ar) {
            return $this->title_ar;
        }
        return $this->title;
    }

    /**
     * Get localized description based on current language
     */
    public function getLocalizedDescription(): string
    {
        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar' && $this->description_ar) {
            return $this->description_ar;
        }
        return $this->description;
    }

    /**
     * Get localized excerpt based on current language
     */
    public function getLocalizedExcerpt(): string
    {
        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar') {
            return $this->getExcerptArOrGenerated();
        }
        return $this->getExcerptOrGenerated();
    }

    /**
     * Get localized image based on current language
     */
    public function getLocalizedImageUrl(): ?string
    {
        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar' && $this->image_ar_url) {
            return $this->image_ar_url;
        }
        return $this->image_url;
    }

    /**
     * Get the final slug (custom or generated)
     */
    public function getFinalSlug(): string
    {
        if ($this->custom_slug) {
            return $this->custom_slug;
        }

        $currentLanguage = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($currentLanguage && $currentLanguage->code === 'ar' && $this->slug_ar) {
            return $this->slug_ar;
        }

        return $this->slug;
    }

    /**
     * Calculate estimated reading time
     */
    public function calculateReadingTime(): int
    {
        if ($this->reading_time) {
            return $this->reading_time;
        }

        $content = strip_tags($this->getLocalizedDescription());
        $wordCount = str_word_count($content);
        $readingTime = ceil($wordCount / 200); // Average reading speed: 200 words per minute

        return max(1, $readingTime); // Minimum 1 minute
    }

    /**
     * Get tags as array safely
     */
    public function getTagsArray(): array
    {
        if (is_array($this->tags)) {
            return $this->tags;
        }

        if (is_string($this->tags)) {
            $decoded = json_decode($this->tags, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
