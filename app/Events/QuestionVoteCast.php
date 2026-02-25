<?php

namespace App\Events;

use App\Models\QuestionsAnswer;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionVoteCast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public QuestionsAnswer $question,
        public User $voter,
        public string $voteType
    ) {}
}
