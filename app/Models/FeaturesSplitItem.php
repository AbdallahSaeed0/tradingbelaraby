<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturesSplitItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'title_ar',
        'description',
        'description_ar',
        'icon',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

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
    public function getDisplayTitle()
    {
        return get_current_language_code() === 'ar' && $this->title_ar ? $this->title_ar : $this->title;
    }

    public function getDisplayDescription()
    {
        return get_current_language_code() === 'ar' && $this->description_ar ? $this->description_ar : $this->description;
    }
}
