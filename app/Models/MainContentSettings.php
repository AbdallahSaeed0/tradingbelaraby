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
        'favicon',
        'footer_copyright',
        'footer_copyright_ar',
        'is_active',
        'coming_soon_enabled'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'coming_soon_enabled' => 'boolean',
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
            return asset('images/default-logo.svg');
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
     * Get the favicon URL
     */
    public function getFaviconUrlAttribute()
    {
        if (!$this->favicon) {
            return asset('favicon.ico');
        }
        if (filter_var($this->favicon, FILTER_VALIDATE_URL)) {
            return $this->favicon;
        }
        return asset('storage/' . $this->favicon);
    }

    /**
     * Get the footer copyright text for the current language, with {year} replaced
     */
    public function getLocalizedFooterCopyright()
    {
        $text = get_current_language_code() === 'ar' && $this->footer_copyright_ar
            ? $this->footer_copyright_ar
            : $this->footer_copyright;

        return $text ? str_replace('{year}', date('Y'), $text) : null;
    }

    /**
     * Scope to get active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
