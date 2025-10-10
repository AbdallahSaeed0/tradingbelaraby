<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'email',
        'address',
        'map_embed_url',
        'map_latitude',
        'map_longitude',
        'office_hours',
        'social_facebook',
        'social_twitter',
        'social_youtube',
        'social_linkedin',
        'social_snapchat',
        'social_tiktok',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Scope for active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the first active contact settings
     */
    public static function getActive()
    {
        return static::active()->first();
    }

    /**
     * Get formatted office hours
     */
    public function getFormattedOfficeHoursAttribute()
    {
        if (!$this->office_hours) {
            return 'Monday - Friday: 9:00 AM - 6:00 PM';
        }
        return $this->office_hours;
    }

    /**
     * Get social media links
     */
    public function getSocialLinksAttribute()
    {
        return [
            'facebook' => $this->social_facebook,
            'twitter' => $this->social_twitter,
            'youtube' => $this->social_youtube,
            'linkedin' => $this->social_linkedin,
            'snapchat' => $this->social_snapchat,
            'tiktok' => $this->social_tiktok,
        ];
    }

    /**
     * Check if any social media links exist
     */
    public function hasSocialLinks()
    {
        return !empty(array_filter($this->social_links));
    }
}
