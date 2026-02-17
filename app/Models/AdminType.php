<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdminType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getDisplayNameAttribute()
    {
        return ucfirst($this->name);
    }

    // Mutators
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // Methods
    public function hasPermission($permission)
    {
        // Admin type always has all permissions
        if ($this->name === 'admin') {
            return true;
        }

        // Instructors always have manage_own_* and view_own_analytics (even if not in DB)
        if ($this->name === 'instructor') {
            $instructorPermissions = [
                'manage_own_courses',
                'manage_own_quizzes',
                'manage_own_homework',
                'manage_own_live_classes',
                'view_own_analytics',
                'manage_own_questions_answers',
            ];
            if (in_array($permission, $instructorPermissions)) {
                return true;
            }
        }

        if (!$this->permissions) {
            return false;
        }
        return in_array($permission, $this->permissions);
    }

    public function isAdminType()
    {
        return $this->name === 'admin';
    }

    public function addPermission($permission)
    {
        // Cannot modify admin type permissions
        if ($this->isAdminType()) {
            return false;
        }

        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    public function removePermission($permission)
    {
        // Cannot modify admin type permissions
        if ($this->isAdminType()) {
            return false;
        }

        $permissions = $this->permissions ?? [];
        $permissions = array_diff($permissions, [$permission]);
        $this->update(['permissions' => array_values($permissions)]);
    }
}
