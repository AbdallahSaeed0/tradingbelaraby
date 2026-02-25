<?php

namespace App\Notifications;

use App\Models\QuestionsAnswer;
use App\Support\NotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class QaAnsweredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const KEY = 'QA_ANSWERED';

    public function __construct(public QuestionsAnswer $question)
    {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $courseName = $this->question->course?->name ?? 'Course';
        $title = $this->question->question_title ?: 'Your question was answered';

        $titleEn = "New answer to your question";
        $titleAr = "إجابة جديدة على سؤالك";
        $bodyEn = "Your question in \"{$courseName}\" was answered: \"{$title}\"";
        $bodyAr = "تم الإجابة على سؤالك في \"{$courseName}\": \"{$title}\"";

        return NotificationPayload::build(
            self::KEY,
            $titleEn,
            $titleAr,
            $bodyEn,
            $bodyAr,
            [
                'type' => NotificationPayload::ACTION_TYPE_DEEPLINK,
                'value' => '/courses/' . $this->question->course_id . '/qa',
                'meta' => ['question_id' => $this->question->id, 'course_id' => $this->question->course_id],
            ],
            ['model' => 'questions_answer', 'id' => $this->question->id],
            NotificationPayload::PRIORITY_NORMAL,
            NotificationPayload::CREATED_BY_SYSTEM,
            NotificationPayload::AUDIENCE_SINGLE,
            ['question_id' => $this->question->id, 'course_id' => $this->question->course_id]
        );
    }
}
