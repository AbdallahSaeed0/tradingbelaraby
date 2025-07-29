<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InfoSplit extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'description_ar',
        'image',
        'button_text',
        'button_text_ar',
        'button_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessors
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return 'https://images.unsplash.com/photo-1513258496099-48168024aec0?auto=format&fit=crop&w=600&q=80';
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

    public function getDisplayButtonText()
    {
        return get_current_language_code() === 'ar' && $this->button_text_ar ? $this->button_text_ar : $this->button_text;
    }
}
