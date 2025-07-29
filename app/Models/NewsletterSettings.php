<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsletterSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'description_ar',
        'button_text',
        'button_text_ar',
        'placeholder',
        'placeholder_ar',
        'icon',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Scope to get only active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get display title based on current language
     */
    public function getDisplayTitle()
    {
        $currentLang = get_current_language_code();
        return $currentLang === 'ar' && $this->title_ar ? $this->title_ar : $this->title;
    }

    /**
     * Get display description based on current language
     */
    public function getDisplayDescription()
    {
        $currentLang = get_current_language_code();
        return $currentLang === 'ar' && $this->description_ar ? $this->description_ar : $this->description;
    }

    /**
     * Get display button text based on current language
     */
    public function getDisplayButtonText()
    {
        $currentLang = get_current_language_code();
        return $currentLang === 'ar' && $this->button_text_ar ? $this->button_text_ar : $this->button_text;
    }

    /**
     * Get display placeholder based on current language
     */
    public function getDisplayPlaceholder()
    {
        $currentLang = get_current_language_code();
        return $currentLang === 'ar' && $this->placeholder_ar ? $this->placeholder_ar : $this->placeholder;
    }
}
