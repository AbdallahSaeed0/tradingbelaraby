<?php

namespace App\Listeners;

use App\Events\QuestionAnswered;
use App\Notifications\QaAnsweredNotification;

class SendQaAnsweredNotification
{
    public function handle(QuestionAnswered $event): void
    {
        $question = $event->question;
        $owner = $question->user;
        if (!$owner || $owner->id === $question->instructor_id) {
            return;
        }
        $owner->notify(new QaAnsweredNotification($question));
    }
}
