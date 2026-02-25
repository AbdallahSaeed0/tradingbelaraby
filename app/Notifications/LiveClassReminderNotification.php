<?php

namespace App\Notifications;

use App\Models\LiveClass;
use App\Support\NotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class LiveClassReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const KEY_TODAY = 'LIVE_CLASS_REMINDER_TODAY';
    public const KEY_T_MINUS_10 = 'LIVE_CLASS_REMINDER_T_MINUS_10';

    public function __construct(
        public LiveClass $liveClass,
        public string $key
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $name = $this->liveClass->localized_name ?: $this->liveClass->name;
        $startAt = $this->liveClass->scheduled_at->format('Y-m-d H:i');
        $courseName = $this->liveClass->course?->name ?? '';

        $titleEn = "Live class: {$name}";
        $titleAr = "حصة مباشرة: {$name}";
        $bodyEn = $this->key === self::KEY_TODAY
            ? "Your live class \"{$name}\" is today at {$startAt}. Don't miss it!"
            : "Your live class \"{$name}\" starts in 10 minutes.";
        $bodyAr = $this->key === self::KEY_TODAY
            ? "حصتك المباشرة \"{$name}\" اليوم في {$startAt}. لا تفوتها!"
            : "حصتك المباشرة \"{$name}\" تبدأ خلال 10 دقائق.";

        return NotificationPayload::build(
            $this->key,
            $titleEn,
            $titleAr,
            $bodyEn,
            $bodyAr,
            [
                'type' => NotificationPayload::ACTION_TYPE_DEEPLINK,
                'value' => '/live-classes/' . $this->liveClass->id,
                'meta' => ['live_class_id' => $this->liveClass->id],
            ],
            ['model' => 'live_class', 'id' => $this->liveClass->id],
            NotificationPayload::PRIORITY_HIGH,
            NotificationPayload::CREATED_BY_SYSTEM,
            NotificationPayload::AUDIENCE_SINGLE,
            [
                'class_id' => $this->liveClass->id,
                'course_id' => $this->liveClass->course_id,
                'start_at' => $this->liveClass->scheduled_at->toIso8601String(),
            ]
        );
    }
}
