<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class MainContentSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo',
        'logo_alt_text',
        'site_name',
        'site_description',
        'site_keywords',
        'site_author',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the active main content settings
     */
    public static function getActive()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Get the logo URL
     */
    public function getLogoUrlAttribute()
    {
        if (!$this->logo) {
            return asset('images/default-logo.png');
        }

        if (filter_var($this->logo, FILTER_VALIDATE_URL)) {
            return $this->logo;
        }

        return asset('storage/' . $this->logo);
    }

    /**
     * Get the logo alt text
     */
    public function getLogoAltTextAttribute($value)
    {
        return $value ?: 'Site Logo';
    }

    /**
     * Scope to get active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
