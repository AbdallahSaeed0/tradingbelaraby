<?php

namespace App\Listeners;

use App\Events\QuestionVoteCast;
use App\Notifications\QaLikedNotification;
use Illuminate\Support\Facades\Cache;

class SendQaLikedNotification
{
    private const THROTTLE_HOURS = 6;

    public function handle(QuestionVoteCast $event): void
    {
        if ($event->voteType !== 'helpful') {
            return;
        }
        $question = $event->question;
        $voterId = $event->voter->id;
        $owner = $question->user;
        if (!$owner || $owner->id === $voterId) {
            return;
        }
        $cacheKey = 'qa_liked_sent:' . $question->id . ':' . $voterId;
        if (Cache::has($cacheKey)) {
            return;
        }
        $owner->notify(new QaLikedNotification($question, 1));
        Cache::put($cacheKey, true, now()->addHours(self::THROTTLE_HOURS));
    }
}
