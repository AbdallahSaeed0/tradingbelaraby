<?php

namespace App\Notifications;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Support\NotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CourseProgressNudgeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const KEY = 'COURSE_PROGRESS_NUDGE_INACTIVE_7D';

    public function __construct(public CourseEnrollment $enrollment)
    {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $course = $this->enrollment->course ?? new Course();
        $name = $course->name ?? 'Your course';
        $nameAr = $course->name_ar ?? $name;
        $pct = $this->enrollment->progress_percentage ?? 0;

        $titleEn = "We miss you in \"{$name}\"";
        $titleAr = "نفتقدك في \"{$nameAr}\"";
        $bodyEn = "You haven't been active for 7 days. You're {$pct}% done — keep going!";
        $bodyAr = "لم تكن نشطاً منذ 7 أيام. أنت عند {$pct}% — واصل التقدم!";

        return NotificationPayload::build(
            self::KEY,
            $titleEn,
            $titleAr,
            $bodyEn,
            $bodyAr,
            [
                'type' => NotificationPayload::ACTION_TYPE_DEEPLINK,
                'value' => '/courses/' . $this->enrollment->course_id . '/learn',
                'meta' => ['course_id' => $this->enrollment->course_id, 'enrollment_id' => $this->enrollment->id],
            ],
            ['model' => 'course_enrollment', 'id' => $this->enrollment->id],
            NotificationPayload::PRIORITY_LOW,
            NotificationPayload::CREATED_BY_SYSTEM,
            NotificationPayload::AUDIENCE_SINGLE,
            ['course_id' => $this->enrollment->course_id, 'progress_percentage' => $pct]
        );
    }
}
