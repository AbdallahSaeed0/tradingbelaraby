<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trader extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'sex',
        'birthdate',
        'email',
        'phone_number',
        'whatsapp_number',
        'linkedin',
        'website',
        'trading_community',
        'certificates',
        'trading_experience',
        'training_experience',
        'first_language',
        'second_language',
        'available_appointments',
        'comments',
        'is_active',
    ];

    protected $casts = [
        'birthdate' => 'date',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getFormattedBirthdateAttribute()
    {
        return $this->birthdate ? $this->birthdate->format('M d, Y') : null;
    }

    public function getAgeAttribute()
    {
        return $this->birthdate ? $this->birthdate->age : null;
    }
}
