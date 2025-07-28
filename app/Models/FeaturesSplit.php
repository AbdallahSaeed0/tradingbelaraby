<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturesSplit extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'description_ar',
        'background_image',
        'main_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessors
    public function getBackgroundImageUrlAttribute()
    {
        if ($this->background_image) {
            return asset('storage/' . $this->background_image);
        }
        return 'https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-10.png';
    }

    public function getMainImageUrlAttribute()
    {
        if ($this->main_image) {
            return asset('storage/' . $this->main_image);
        }
        return 'https://eclass.mediacity.co.in/demo2/public/images/services/1695116354video-img3.png';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function getDisplayTitle()
    {
        return get_current_language_code() === 'ar' && $this->title_ar ? $this->title_ar : $this->title;
    }

    public function getDisplayDescription()
    {
        return get_current_language_code() === 'ar' && $this->description_ar ? $this->description_ar : $this->description;
    }
}
