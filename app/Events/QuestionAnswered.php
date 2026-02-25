<?php

namespace App\Events;

use App\Models\QuestionsAnswer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionAnswered
{
    use Dispatchable, SerializesModels;

    public function __construct(public QuestionsAnswer $question)
    {}
}
