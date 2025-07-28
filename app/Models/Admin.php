<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'admin_type_id',
        'phone',
        'avatar',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function adminType()
    {
        return $this->belongsTo(AdminType::class);
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'instructor_id');
    }

    // Accessors
    public function getTypeNameAttribute()
    {
        return $this->adminType ? $this->adminType->name : 'Unknown';
    }

    public function getDisplayTypeAttribute()
    {
        return $this->adminType ? $this->adminType->display_name : 'Unknown';
    }

    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        // Generate avatar from name using UI Avatars API
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&size=400&background=random';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $typeId)
    {
        return $query->where('admin_type_id', $typeId);
    }

    // Methods
    public function hasPermission($permission)
    {
        if (!$this->adminType) {
            return false;
        }
        return $this->adminType->hasPermission($permission);
    }

    public function isType($typeName)
    {
        return $this->adminType && $this->adminType->name === $typeName;
    }

    public function isAdmin()
    {
        return $this->isType('admin');
    }

    public function isInstructor()
    {
        return $this->isType('instructor');
    }

    public function isEmployee()
    {
        return $this->isType('employee');
    }
}
