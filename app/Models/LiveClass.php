<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiveClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'instructor_id',
        'name',
        'name_ar',
        'description',
        'description_ar',
        'link',
        'meeting_passcode',
        'scheduled_at',
        'duration_minutes',
        'status',
        'max_participants',
        'current_participants',
        'recording_url',
        'materials',
        'is_free',
        'requires_registration',
        'instructions',
        'instructions_ar',
    ];

    /**
     * Get localized name based on current language
     */
    public function getLocalizedNameAttribute(): string
    {
        $lang = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($lang && $lang->code === 'ar' && $this->name_ar) {
            return $this->name_ar;
        }
        return $this->name ?? '';
    }

    /**
     * Get localized description
     */
    public function getLocalizedDescriptionAttribute(): ?string
    {
        $lang = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($lang && $lang->code === 'ar' && $this->description_ar) {
            return $this->description_ar;
        }
        return $this->description;
    }

    /**
     * Get localized instructions
     */
    public function getLocalizedInstructionsAttribute(): ?string
    {
        $lang = \App\Helpers\TranslationHelper::getCurrentLanguage();
        if ($lang && $lang->code === 'ar' && $this->instructions_ar) {
            return $this->instructions_ar;
        }
        return $this->instructions;
    }

    /*
     * On databases where scheduled_at is still a TIMESTAMP with
     * "ON UPDATE CURRENT_TIMESTAMP", MySQL overwrites the class time with "now"
     * on every UPDATE that leaves the column out. So every update writes
     * scheduled_at explicitly, even when it did not change.
     */
    protected static function booted(): void
    {
        static::updating(function (LiveClass $liveClass) {
            if ($liveClass->getRawOriginal('scheduled_at') !== null && !$liveClass->isDirty('scheduled_at')) {
                // Mark it dirty so Eloquent includes it in the UPDATE with its current value
                $liveClass->original['scheduled_at'] = null;
            }
        });

        static::updated(function (LiveClass $liveClass) {
            $liveClass->syncOriginalAttribute('scheduled_at');
        });
    }

    protected function incrementOrDecrement($column, $amount, $extra, $method)
    {
        if ($this->exists && $column !== 'scheduled_at' && !array_key_exists('scheduled_at', $extra) && $this->getRawOriginal('scheduled_at') !== null) {
            $extra['scheduled_at'] = $this->getRawOriginal('scheduled_at');
        }

        return parent::incrementOrDecrement($column, $amount, $extra, $method);
    }

    // The passcode is only sent to enrolled students through the Zoom SDK join endpoint
    protected $hidden = ['meeting_passcode'];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'max_participants' => 'integer',
        'current_participants' => 'integer',
        'materials' => 'array',
        'is_free' => 'boolean',
        'requires_registration' => 'boolean',
    ];

    protected $appends = [
        'formatted_duration',
        'is_upcoming',
        'is_live_now',
        'is_ended',
        'time_until_start',
        'time_since_start',
        'participant_percentage',
        'is_full',
    ];

    /**
     * Get the course that owns the live class
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the instructor that owns the live class
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'instructor_id');
    }

    /**
     * Get the registrations for the live class
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(LiveClassRegistration::class);
    }

    /**
     * Scope for scheduled live classes
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope for live classes currently happening
     */
    public function scopeLive($query)
    {
        return $query->where('status', 'live');
    }

    /**
     * Scope for completed live classes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for upcoming live classes
     */
    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', self::wallNow())
            ->where('status', 'scheduled');
    }

    /**
     * Scope for today's live classes
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', self::wallNow()->toDateString());
    }

    /**
     * Scope for this week's live classes
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('scheduled_at', [
            self::wallNow()->startOfWeek(),
            self::wallNow()->endOfWeek()
        ]);
    }

    /**
     * Scope for free live classes
     */
    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    /**
     * Get formatted duration attribute
     */
    public function getFormattedDurationAttribute(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'm';
        }
        return $minutes . 'm';
    }

    /**
     * Get is upcoming attribute
     */
    public function getIsUpcomingAttribute(): bool
    {
        return $this->scheduled_at && $this->startsAt()->isFuture();
    }

    /**
     * Get is live now attribute
     */
    public function getIsLiveNowAttribute(): bool
    {
        if (!$this->scheduled_at) {
            return false;
        }

        $startTime = $this->startsAt();
        $endTime = $this->endsAt();
        $now = now();

        return $now->between($startTime, $endTime) && $this->status === 'live';
    }

    /**
     * Get is ended attribute
     */
    public function getIsEndedAttribute(): bool
    {
        if (!$this->scheduled_at) {
            return false;
        }

        return now()->isAfter($this->endsAt());
    }

    /**
     * Get time until start attribute
     */
    public function getTimeUntilStartAttribute(): ?string
    {
        if (!$this->scheduled_at || $this->startsAt()->isPast()) {
            return null;
        }

        $diff = now()->diff($this->startsAt());

        if ($diff->days > 0) {
            return $diff->days . ' days, ' . $diff->h . ' hours';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hours, ' . $diff->i . ' minutes';
        } else {
            return $diff->i . ' minutes';
        }
    }

    /**
     * Get time since start attribute
     */
    public function getTimeSinceStartAttribute(): ?string
    {
        if (!$this->scheduled_at || $this->startsAt()->isFuture()) {
            return null;
        }

        $diff = $this->startsAt()->diff(now());

        if ($diff->days > 0) {
            return $diff->days . ' days, ' . $diff->h . ' hours ago';
        } elseif ($diff->h > 0) {
            return $diff->h . ' hours, ' . $diff->i . ' minutes ago';
        } else {
            return $diff->i . ' minutes ago';
        }
    }

    /**
     * Get participant percentage attribute
     */
    public function getParticipantPercentageAttribute(): float
    {
        if (!$this->max_participants) {
            return 0;
        }

        return round(($this->current_participants / $this->max_participants) * 100, 2);
    }

    /**
     * Get is full attribute
     */
    public function getIsFullAttribute(): bool
    {
        if (!$this->max_participants) {
            return false;
        }

        return $this->current_participants >= $this->max_participants;
    }

    /**
     * Check if user is registered for this live class
     */
    public function isRegisteredByUser(User $user): bool
    {
        return $this->registrations()->where('user_id', $user->id)->exists();
    }

    /**
     * Get user's registration for this live class
     */
    public function getUserRegistration(User $user): ?LiveClassRegistration
    {
        return $this->registrations()->where('user_id', $user->id)->first();
    }

    /**
     * Register user for live class
     */
    public function registerUser(User $user): bool
    {
        if ($this->isRegisteredByUser($user)) {
            return false; // Already registered
        }

        if ($this->is_full) {
            return false; // Class is full
        }

        $registration = new LiveClassRegistration([
            'user_id' => $user->id,
            'live_class_id' => $this->id,
            'registered_at' => now(),
        ]);

        $registration->save();

        // Update participant count
        $this->increment('current_participants');

        return true;
    }

    /**
     * Unregister user from live class
     */
    public function unregisterUser(User $user): bool
    {
        $registration = $this->getUserRegistration($user);

        if (!$registration) {
            return false; // Not registered
        }

        $registration->delete();

        // Update participant count
        $this->decrement('current_participants');

        return true;
    }

    /**
     * Start the live class
     */
    public function start(): void
    {
        $this->update([
            'status' => 'live',
        ]);
    }

    /**
     * End the live class
     */
    public function end(): void
    {
        $this->update([
            'status' => 'completed',
        ]);
    }

    /**
     * Cancel the live class
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }



    /**
     * Get material URLs
     */
    public function getMaterialUrlsAttribute(): array
    {
        if (!$this->materials) {
            return [];
        }

        return array_map(function ($material) {
            return asset('storage/' . $material);
        }, $this->materials);
    }

    /*
    |--------------------------------------------------------------------------
    | Timezone-aware times
    |--------------------------------------------------------------------------
    | scheduled_at holds the wall-clock time the admin typed (e.g. 21:00 Riyadh)
    | in a UTC app. These helpers turn it into a real moment in time.
    */

    public static function wallTimezone(): string
    {
        return config('app.live_class_timezone', 'Asia/Riyadh');
    }

    /**
     * "Now" in the same wall-clock terms as scheduled_at, for DB comparisons.
     */
    public static function wallNow(): \Carbon\Carbon
    {
        return \Carbon\Carbon::parse(now(self::wallTimezone())->format('Y-m-d H:i:s'));
    }

    /**
     * Real start moment, in the live class timezone.
     */
    public function startsAt(): ?\Carbon\Carbon
    {
        if (!$this->scheduled_at) {
            return null;
        }

        return \Carbon\Carbon::parse($this->scheduled_at->format('Y-m-d H:i:s'), self::wallTimezone());
    }

    /**
     * Joining is open from one hour before the start until 30 minutes after the end.
     */
    public function isJoinWindowOpen(): bool
    {
        $start = $this->startsAt();
        if (!$start || in_array($this->status, ['cancelled', 'completed'], true)) {
            return $this->status === 'live';
        }

        return now()->between($start->copy()->subHour(), $this->endsAt()->copy()->addMinutes(30))
            || $this->status === 'live';
    }

    public function endsAt(): ?\Carbon\Carbon
    {
        return $this->startsAt()?->copy()->addMinutes($this->duration_minutes ?: 60);
    }

    public function calendarTitle(): string
    {
        $name = $this->localized_name ?: $this->name;
        $course = $this->course?->name;

        return $course && $course !== $name ? "{$name} - {$course}" : (string) $name;
    }

    public function calendarDetails(): string
    {
        $url = $this->course ? route('courses.learn', $this->course) : url('/');

        return custom_trans('The join link will be sent to you one hour before the session.', 'front') . "\n" . $url;
    }

    public function googleCalendarUrl(): ?string
    {
        if (!$this->startsAt()) {
            return null;
        }

        return 'https://calendar.google.com/calendar/render?' . http_build_query([
            'action' => 'TEMPLATE',
            'text' => $this->calendarTitle(),
            'dates' => $this->startsAt()->utc()->format('Ymd\THis\Z') . '/' . $this->endsAt()->utc()->format('Ymd\THis\Z'),
            'details' => $this->calendarDetails(),
            'ctz' => self::wallTimezone(),
        ]);
    }

    /**
     * iCalendar (.ics) file for Apple Calendar / Outlook, with a 1-hour alarm.
     */
    public function toIcs(): string
    {
        $escape = fn (string $text) => str_replace(["\r\n", "\n"], '\\n', addcslashes($text, ',;\\'));
        $host = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
        $url = $this->course ? route('courses.learn', $this->course) : url('/');

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//' . $host . '//Live Classes//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            'UID:live-class-' . $this->id . '@' . $host,
            'DTSTAMP:' . now()->utc()->format('Ymd\THis\Z'),
            'DTSTART:' . $this->startsAt()->utc()->format('Ymd\THis\Z'),
            'DTEND:' . $this->endsAt()->utc()->format('Ymd\THis\Z'),
            'SUMMARY:' . $escape($this->calendarTitle()),
            'DESCRIPTION:' . $escape($this->calendarDetails()),
            'URL:' . $url,
            'BEGIN:VALARM',
            'TRIGGER:-PT1H',
            'ACTION:DISPLAY',
            'DESCRIPTION:' . $escape($this->calendarTitle()),
            'END:VALARM',
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        return implode("\r\n", $lines) . "\r\n";
    }
}
