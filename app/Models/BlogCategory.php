<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'image',
        'status',
        'order',
        'is_featured'
    ];

    protected $casts = [
        'order' => 'integer',
        'is_featured' => 'boolean',
    ];

    protected $appends = [
        'blogs_count',
        'published_blogs_count'
    ];

    /**
     * Get the blogs for this category
     */
    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'category_id');
    }

    /**
     * Get the published blogs for this category
     */
    public function publishedBlogs(): HasMany
    {
        return $this->hasMany(Blog::class, 'category_id')->where('status', 'published');
    }

    /**
     * Scope for active categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for ordered categories
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    /**
     * Get blogs count attribute
     */
    public function getBlogsCountAttribute(): int
    {
        return $this->blogs()->count();
    }

    /**
     * Get published blogs count attribute
     */
    public function getPublishedBlogsCountAttribute(): int
    {
        return $this->publishedBlogs()->count();
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
     * Check if category has blogs
     */
    public function hasBlogs(): bool
    {
        return $this->blogs()->exists();
    }

    /**
     * Check if category has published blogs
     */
    public function hasPublishedBlogs(): bool
    {
        return $this->publishedBlogs()->exists();
    }

    /**
     * Get the route key name for Laravel.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKey()
    {
        return $this->getAttribute($this->getRouteKeyName());
    }
}
