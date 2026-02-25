<?php

namespace App\Notifications;

use App\Support\NotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CourseNewContentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const KEY_NEW_LESSON = 'COURSE_NEW_LESSON';
    public const KEY_NEW_LECTURE = 'COURSE_NEW_LECTURE';
    public const KEY_NEW_HOMEWORK = 'COURSE_NEW_HOMEWORK';
    public const KEY_NEW_QUIZ = 'COURSE_NEW_QUIZ';
    public const KEY_NEW_LIVE_CLASS_ANNOUNCED = 'COURSE_NEW_LIVE_CLASS_ANNOUNCED';

    public function __construct(
        public string $key,
        public string $courseName,
        public string $courseNameAr,
        public int $courseId,
        public string $itemTitle,
        public string $itemTitleAr = '',
        public ?int $entityId = null,
        public ?string $deeplinkPath = null
    ) {
        if ($this->deeplinkPath === null) {
            $this->deeplinkPath = '/courses/' . $courseId;
        }
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $labels = $this->labels();
        $titleEn = $labels['title_en'];
        $titleAr = $labels['title_ar'];
        $bodyEn = "New content in \"{$this->courseName}\": {$this->itemTitle}";
        $bodyAr = $this->courseNameAr
            ? "محتوى جديد في \"{$this->courseNameAr}\": " . ($this->itemTitleAr ?: $this->itemTitle)
            : $bodyEn;

        return NotificationPayload::build(
            $this->key,
            $titleEn,
            $titleAr,
            $bodyEn,
            $bodyAr,
            [
                'type' => NotificationPayload::ACTION_TYPE_DEEPLINK,
                'value' => $this->deeplinkPath,
                'meta' => ['course_id' => $this->courseId, 'entity_id' => $this->entityId],
            ],
            $this->entityId ? ['model' => $this->entityModel(), 'id' => $this->entityId] : null,
            NotificationPayload::PRIORITY_NORMAL,
            NotificationPayload::CREATED_BY_SYSTEM,
            NotificationPayload::AUDIENCE_SEGMENT,
            ['course_id' => $this->courseId, 'item_title' => $this->itemTitle]
        );
    }

    private function labels(): array
    {
        $titles = [
            self::KEY_NEW_LESSON => ['New lesson', 'درس جديد'],
            self::KEY_NEW_LECTURE => ['New lecture', 'محاضرة جديدة'],
            self::KEY_NEW_HOMEWORK => ['New homework', 'واجب جديد'],
            self::KEY_NEW_QUIZ => ['New quiz', 'اختبار جديد'],
            self::KEY_NEW_LIVE_CLASS_ANNOUNCED => ['New live class', 'حصة مباشرة جديدة'],
        ];
        $t = $titles[$this->key] ?? ['New content', 'محتوى جديد'];
        return ['title_en' => $t[0], 'title_ar' => $t[1]];
    }

    private function entityModel(): string
    {
        $models = [
            self::KEY_NEW_LESSON => 'course_section',
            self::KEY_NEW_LECTURE => 'course_lecture',
            self::KEY_NEW_HOMEWORK => 'homework',
            self::KEY_NEW_QUIZ => 'quiz',
            self::KEY_NEW_LIVE_CLASS_ANNOUNCED => 'live_class',
        ];
        return $models[$this->key] ?? 'course';
    }
}
