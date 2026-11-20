<?php

namespace App\Notifications;

use App\Models\LiveClass;
use App\Support\NotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Email + in-app reminder before a live session:
 * - KEY_DAY_BEFORE: one day before (date, time, add to calendar)
 * - KEY_HOUR_BEFORE: one hour before, with the join link
 *
 * The email is bilingual (Arabic first) because there is no stored language per user.
 */
class LiveClassUpcomingReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const KEY_DAY_BEFORE = 'LIVE_CLASS_REMINDER_DAY_BEFORE';
    public const KEY_HOUR_BEFORE = 'LIVE_CLASS_REMINDER_HOUR_BEFORE';

    public function __construct(
        public LiveClass $liveClass,
        public string $key
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Skip placeholder emails (X sign-ins without an email)
        if (!empty($notifiable->email) && !str_ends_with($notifiable->email, '@users.noreply.local')) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $class = $this->liveClass;
        $startsAt = $class->startsAt();
        $nameAr = $class->name_ar ?: $class->name;
        $nameEn = $class->name ?: $class->name_ar;
        $dateAr = $startsAt->copy()->locale('ar')->translatedFormat('l j F - g:i A');
        $dateEn = $startsAt->copy()->locale('en')->translatedFormat('l, F j - g:i A');
        $tz = LiveClass::wallTimezone() === 'Asia/Riyadh' ? ['بتوقيت الرياض', 'Riyadh time'] : [LiveClass::wallTimezone(), LiveClass::wallTimezone()];
        $isHour = $this->key === self::KEY_HOUR_BEFORE;
        $learnUrl = $class->course ? route('courses.learn', $class->course) : url('/');

        $mail = (new MailMessage)
            ->subject($isHour
                ? "تبدأ خلال ساعة: {$nameAr} | Starts in 1 hour: {$nameEn}"
                : "تذكير: {$nameAr} غداً | Reminder: {$nameEn} tomorrow")
            ->greeting('مرحباً ' . $notifiable->name . ' 👋')
            ->line($isHour
                ? "جلستك \"{$nameAr}\" تبدأ خلال ساعة."
                : "تذكير بجلستك \"{$nameAr}\" غداً.")
            ->line("الموعد: {$dateAr} {$tz[0]}");

        if ($isHour && $class->link) {
            // In-site room (the Zoom link itself is never sent to students)
            $roomUrl = route('live.share', $class);
            $mail->action('ادخل الجلسة | Join the session', $roomUrl)
                ->line('رابط الدخول | Join link: ' . $roomUrl);
        } else {
            $mail->line('سنرسل لك رابط الدخول قبل الموعد بساعة.')
                ->action('أضف للتقويم | Add to calendar', $class->googleCalendarUrl());
        }

        return $mail
            ->line('---')
            ->line($isHour
                ? "Your session \"{$nameEn}\" starts in one hour ({$dateEn} {$tz[1]})."
                : "Reminder: your session \"{$nameEn}\" is tomorrow ({$dateEn} {$tz[1]}). We'll email you the join link one hour before.")
            ->line('صفحة الدورة | Course page: ' . $learnUrl);
    }

    public function toArray(object $notifiable): array
    {
        $class = $this->liveClass;
        $name = $class->localized_name ?: $class->name;
        $time = $class->startsAt()->format('Y-m-d H:i');
        $isHour = $this->key === self::KEY_HOUR_BEFORE;

        return NotificationPayload::build(
            $this->key,
            "Live class: {$name}",
            "حصة مباشرة: {$name}",
            $isHour ? "\"{$name}\" starts in one hour. Tap to join." : "Reminder: \"{$name}\" is tomorrow at {$time}.",
            $isHour ? "\"{$name}\" تبدأ خلال ساعة. اضغط للدخول." : "تذكير: \"{$name}\" غداً في {$time}.",
            [
                'type' => NotificationPayload::ACTION_TYPE_DEEPLINK,
                'value' => '/live-classes/' . $class->id,
                'meta' => ['live_class_id' => $class->id],
            ],
            ['model' => 'live_class', 'id' => $class->id],
            NotificationPayload::PRIORITY_HIGH,
            NotificationPayload::CREATED_BY_SYSTEM,
            NotificationPayload::AUDIENCE_SINGLE,
            [
                'class_id' => $class->id,
                'course_id' => $class->course_id,
                'start_at' => $class->startsAt()->toIso8601String(),
            ]
        );
    }
}
