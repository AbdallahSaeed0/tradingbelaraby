<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Bundle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'slug',
        'description',
        'description_ar',
        'image',
        'price',
        'original_price',
        'status',
        'is_featured',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_featured' => 'boolean',
    ];

    protected $appends = [
        'formatted_price',
        'formatted_original_price',
        'discount_percentage',
        'is_discounted',
    ];

    /**
     * Boot method to automatically generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($bundle) {
            if (empty($bundle->slug)) {
                $bundle->slug = Str::slug($bundle->name);
            }
        });

        static::updating(function ($bundle) {
            if ($bundle->isDirty('name') && empty($bundle->slug)) {
                $bundle->slug = Str::slug($bundle->name);
            }
        });
    }

    /**
     * Get the courses in this bundle
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'bundle_course')
                    ->withTimestamps();
    }

    /**
     * Scope for published bundles
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope for featured bundles
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Get formatted price attribute
     */
    public function getFormattedPriceAttribute(): string
    {
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
     * Get is discounted attribute
     */
    public function getIsDiscountedAttribute(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }

    /**
     * Get bundle image URL
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/default-bundle.jpg');
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
     * Calculate the total original price of all courses in bundle
     */
    public function getTotalCoursePriceAttribute(): float
    {
        return $this->courses->sum('price');
    }

    /**
     * Calculate savings amount
     */
    public function getSavingsAmountAttribute(): float
    {
        return max(0, $this->total_course_price - $this->price);
    }
}

