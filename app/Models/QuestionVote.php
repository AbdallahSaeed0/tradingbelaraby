<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionVote extends Model
{
    protected $fillable = ['user_id', 'questions_answer_id', 'vote_type'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuestionsAnswer::class, 'questions_answer_id');
    }
}
