<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'welcome_text',
        'welcome_text_ar',
        'subtitle',
        'subtitle_ar',
        'background_image',
        'button_text',
        'button_text_ar',
        'button_url',
        'search_placeholder',
        'search_placeholder_ar',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Scope a query to only include active sliders.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order sliders by their order field.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Get the full background image URL.
     */
    public function getBackgroundImageUrlAttribute()
    {
        if (filter_var($this->background_image, FILTER_VALIDATE_URL)) {
            return $this->background_image;
        }

        return asset('storage/' . $this->background_image);
    }
}
