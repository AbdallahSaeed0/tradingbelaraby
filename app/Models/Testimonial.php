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
        'voice',
        'voice_url',
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

    /**
     * Get voice URL for playback (either uploaded file or external URL)
     * Note: This accessor name conflicts with the database column, so we use a different approach
     */
    public function getVoicePlaybackUrlAttribute()
    {
        // If voice_url column is set (Google Drive link), return it directly
        if (!empty($this->attributes['voice_url'] ?? null)) {
            return $this->attributes['voice_url'];
        }
        
        // Otherwise, return the uploaded file URL
        if (!empty($this->attributes['voice'] ?? null)) {
            return asset('storage/' . $this->attributes['voice']);
        }

        return null;
    }

    /**
     * Get voice file URL (only for uploaded files, not external URLs)
     */
    public function getVoiceFileUrlAttribute()
    {
        if (!empty($this->attributes['voice'] ?? null)) {
            return asset('storage/' . $this->attributes['voice']);
        }

        return null;
    }

    /**
     * Check if voice is from Google Drive
     */
    public function getIsGoogleDriveVoiceAttribute()
    {
        return !empty($this->attributes['voice_url'] ?? null);
    }

    /**
     * Get the voice URL column value (for Google Drive links)
     */
    public function getVoiceUrlColumnAttribute()
    {
        return $this->attributes['voice_url'] ?? null;
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
        $content = get_current_language_code() === 'ar' && $this->content_ar ? $this->content_ar : $this->content;
        return $content ?: null;
    }
}
