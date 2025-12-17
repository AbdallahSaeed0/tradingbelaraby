<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\Session;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'avatar', 'gender', 'date_of_birth', 'country', 'bio', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'password' => 'hashed',
    ];

    public function wishlistItems(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function hasInWishlist(\App\Models\Course $course): bool
    {
        return $this->wishlistItems()->where('course_id', $course->id)->exists();
    }

    public function hasInCart(\App\Models\Course $course): bool
    {
        return $this->cartItems()->where('course_id', $course->id)->exists();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(\App\Models\Order::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(\App\Models\CourseEnrollment::class);
    }

    public function lectureCompletions(): HasMany
    {
        return $this->hasMany(\App\Models\LectureCompletion::class);
    }

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        // Get user's language preference from session or default to 'en'
        $language = Session::get('frontend_locale', config('app.locale'));
        $language = in_array($language, ['ar', 'en']) ? $language : 'en';

        $this->notify(new VerifyEmailNotification($language));
    }
}
