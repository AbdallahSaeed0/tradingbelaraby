<?php

namespace App\Jobs;

use App\Models\CourseEnrollment;
use App\Models\LiveClass;
use App\Models\NotificationSend;
use App\Models\User;
use App\Notifications\LiveClassUpcomingReminderNotification;
use App\Services\WhatsAppService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Two automatic reminders per live session, by email + in-app (+ WhatsApp when a
 * reminder template is configured):
 *  - one day before the session
 *  - one hour before, with the join link
 *
 * Sent to everyone registered for the session AND every student with access to its
 * course, so free sign-ups get reminded without an extra "register" click.
 * Runs every five minutes; NotificationSend makes each reminder go out only once.
 */
class SendLiveClassUpcomingRemindersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $now = LiveClass::wallNow();

        // Day before: session starts 22-24 hours from now (wide window survives scheduler gaps)
        $this->sendFor(
            $now->copy()->addHours(22),
            $now->copy()->addHours(24),
            LiveClassUpcomingReminderNotification::KEY_DAY_BEFORE
        );

        // Hour before: session starts within the next hour
        $this->sendFor(
            $now->copy(),
            $now->copy()->addHour(),
            LiveClassUpcomingReminderNotification::KEY_HOUR_BEFORE
        );
    }

    private function sendFor($from, $to, string $key): void
    {
        $classes = LiveClass::where('status', 'scheduled')
            ->whereBetween('scheduled_at', [$from, $to])
            ->with('course')
            ->get();

        foreach ($classes as $liveClass) {
            foreach ($this->recipients($liveClass) as $user) {
                $sent = NotificationSend::recordSend($user->id, $key, $liveClass->id, $liveClass->scheduled_at);
                if (!$sent) {
                    continue;
                }

                try {
                    $user->notify(new LiveClassUpcomingReminderNotification($liveClass, $key));
                } catch (\Throwable $e) {
                    Log::error('Live class reminder failed', [
                        'user_id' => $user->id,
                        'live_class_id' => $liveClass->id,
                        'error' => $e->getMessage(),
                    ]);
                }

                $this->sendWhatsapp($user, $liveClass, $key);
            }
        }
    }

    private function recipients(LiveClass $liveClass)
    {
        $userIds = $liveClass->registrations()->pluck('user_id');

        if ($liveClass->course_id) {
            $userIds = $userIds->merge(
                CourseEnrollment::where('course_id', $liveClass->course_id)->accessible()->pluck('user_id')
            );
        }

        return User::whereIn('id', $userIds->unique()->values())->get();
    }

    private function sendWhatsapp(User $user, LiveClass $liveClass, string $key): void
    {
        $template = config('services.meta_whatsapp.reminder_template');
        if (!$template || !$user->phone) {
            return;
        }

        $startsAt = $liveClass->startsAt();
        $isHour = $key === LiveClassUpcomingReminderNotification::KEY_HOUR_BEFORE;
        $link = $isHour && $liveClass->link
            ? $liveClass->link
            : ($liveClass->course ? route('courses.learn', $liveClass->course) : url('/'));

        app(WhatsAppService::class)->sendTemplate(
            $user->phone,
            $template,
            config('services.meta_whatsapp.reminder_template_lang', 'ar'),
            [
                $user->name,
                $liveClass->name_ar ?: $liveClass->name,
                $startsAt->copy()->locale('ar')->translatedFormat('l j F - g:i A'),
                $link,
            ]
        );
    }
}
