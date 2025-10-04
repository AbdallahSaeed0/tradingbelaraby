<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'content',
        'content_ar',
        'order',
        'is_active',
        'is_expanded',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_expanded' => 'boolean',
        'order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    public function scopeExpanded($query)
    {
        return $query->where('is_expanded', true);
    }

    // Multilingual display methods
    public function getDisplayTitleAttribute()
    {
        return \App\Helpers\TranslationHelper::getLocalizedContent($this->title, $this->title_ar);
    }

    public function getDisplayContentAttribute()
    {
        return \App\Helpers\TranslationHelper::getLocalizedContent($this->content, $this->content_ar);
    }

    // Legacy method names for backward compatibility
    public function getDisplayTitle()
    {
        return $this->getDisplayTitleAttribute();
    }

    public function getDisplayDescription()
    {
        return $this->getDisplayContentAttribute();
    }

    public function getDisplayQuestion()
    {
        return $this->getDisplayTitleAttribute();
    }

    public function getDisplayAnswer()
    {
        return $this->getDisplayContentAttribute();
    }
}
