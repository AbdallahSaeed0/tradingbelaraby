<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'name_ar',
        'position',
        'position_ar',
        'company',
        'company_ar',
        'content',
        'content_ar',
        'avatar',
        'rating',
        'order',
        'is_active',
    ];

    protected $casts = [
        'rating' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    // Accessors
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Generate avatar from name using UI Avatars API
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&size=200&background=random';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('created_at');
    }

    // Methods
    public function getDisplayName()
    {
        return get_current_language_code() === 'ar' && $this->name_ar ? $this->name_ar : $this->name;
    }

    public function getDisplayPosition()
    {
        return get_current_language_code() === 'ar' && $this->position_ar ? $this->position_ar : $this->position;
    }

    public function getDisplayCompany()
    {
        return get_current_language_code() === 'ar' && $this->company_ar ? $this->company_ar : $this->company;
    }

    public function getDisplayContent()
    {
        return get_current_language_code() === 'ar' && $this->content_ar ? $this->content_ar : $this->content;
    }
}
