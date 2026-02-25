<?php

namespace App\Notifications;

use App\Models\QuestionsAnswer;
use App\Support\NotificationPayload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class QaLikedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public const KEY = 'QA_LIKED';

    public function __construct(public QuestionsAnswer $question, public int $likeCount = 1)
    {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $titleEn = $this->likeCount > 1
            ? "{$this->likeCount} people found your question helpful"
            : "Someone found your question helpful";
        $titleAr = $this->likeCount > 1
            ? "{$this->likeCount} أشخاص وجدوا سؤالك مفيداً"
            : "شخص وجد سؤالك مفيداً";
        $bodyEn = $this->question->question_title
            ? "\"{$this->question->question_title}\""
            : "Your question received a helpful vote.";
        $bodyAr = $this->question->question_title
            ? "\"{$this->question->question_title}\""
            : "تلقى سؤالك تصويتاً مفيداً.";

        return NotificationPayload::build(
            self::KEY,
            $titleEn,
            $titleAr,
            $bodyEn,
            $bodyAr,
            [
                'type' => NotificationPayload::ACTION_TYPE_DEEPLINK,
                'value' => '/courses/' . $this->question->course_id . '/qa',
                'meta' => ['question_id' => $this->question->id],
            ],
            ['model' => 'questions_answer', 'id' => $this->question->id],
            NotificationPayload::PRIORITY_LOW,
            NotificationPayload::CREATED_BY_SYSTEM,
            NotificationPayload::AUDIENCE_SINGLE,
            ['question_id' => $this->question->id, 'course_id' => $this->question->course_id]
        );
    }
}
