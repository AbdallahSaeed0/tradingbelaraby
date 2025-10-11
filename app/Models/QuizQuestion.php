<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'quiz_id',
        'question_text',
        'question_text_ar',
        'question_type',
        'points',
        'order',
        'options',
        'options_ar',
        'correct_answers',
        'correct_answer_boolean',
        'correct_answers_text',
        'correct_answers_text_ar',
        'sample_answer',
        'sample_answer_ar',
        'word_limit',
        'is_required',
        'shuffle_options',
        'explanation',
        'explanation_ar',
        'image',
        'audio',
        'video',
    ];

    protected $casts = [
        'options' => 'array',
        'options_ar' => 'array',
        'correct_answers' => 'array',
        'correct_answers_text' => 'array',
        'correct_answers_text_ar' => 'array',
        'correct_answer_boolean' => 'boolean',
        'is_required' => 'boolean',
        'shuffle_options' => 'boolean',
        'points' => 'integer',
        'order' => 'integer',
        'word_limit' => 'integer',
    ];

    protected $appends = [
        'formatted_question_type',
        'has_media',
    ];

    /**
     * Get the quiz that owns the question
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class);
    }



    /**
     * Get formatted question type
     */
    public function getFormattedQuestionTypeAttribute(): string
    {
        return match($this->question_type) {
            'multiple_choice' => 'Multiple Choice',
            'true_false' => 'True/False',
            'fill_blank' => 'Fill in the Blank',
            'essay' => 'Essay',
            'matching' => 'Matching',
            default => 'Question'
        };
    }

    /**
     * Check if question has media
     */
    public function getHasMediaAttribute(): bool
    {
        return !empty($this->image) || !empty($this->audio) || !empty($this->video);
    }

    /**
     * Get correct answer for display
     */
    public function getCorrectAnswerForDisplay(): string
    {
        return match($this->question_type) {
            'multiple_choice' => $this->getMultipleChoiceCorrectAnswer(),
            'true_false' => $this->correct_answer_boolean ? 'True' : 'False',
            'fill_blank' => $this->getFillBlankCorrectAnswer(),
            'essay' => $this->sample_answer ?? 'Manual grading required',
            default => 'N/A'
        };
    }

    /**
     * Get multiple choice correct answer
     */
    private function getMultipleChoiceCorrectAnswer(): string
    {
        if (!$this->options || !$this->correct_answers) {
            return 'N/A';
        }

        $correctOptions = [];
        foreach ($this->correct_answers as $index) {
            if (isset($this->options[$index])) {
                $correctOptions[] = $this->options[$index];
            }
        }

        return implode(', ', $correctOptions);
    }

    /**
     * Get fill in the blank correct answer
     */
    private function getFillBlankCorrectAnswer(): string
    {
        if (!$this->correct_answers_text) {
            return 'N/A';
        }

        return implode(', ', $this->correct_answers_text);
    }

    /**
     * Check if answer is correct
     */
    public function isAnswerCorrect($userAnswer): bool
    {
        if ($userAnswer === null) {
            return false;
        }

        return match($this->question_type) {
            'multiple_choice' => $this->isMultipleChoiceCorrect($userAnswer),
            'true_false' => $this->isTrueFalseCorrect($userAnswer),
            'fill_blank' => $this->isFillBlankCorrect($userAnswer),
            'essay' => null, // Manual grading required
            default => false
        };
    }

    /**
     * Check if multiple choice answer is correct
     */
    private function isMultipleChoiceCorrect($userAnswer): bool
    {
        if (!$this->correct_answers) {
            return false;
        }

        return in_array($userAnswer, $this->correct_answers);
    }

    /**
     * Check if true/false answer is correct
     */
    private function isTrueFalseCorrect($userAnswer): bool
    {
        if ($this->correct_answer_boolean === null) {
            return false;
        }

        return $userAnswer == $this->correct_answer_boolean;
    }

    /**
     * Check if fill in the blank answer is correct
     */
    private function isFillBlankCorrect($userAnswer): bool
    {
        if (!$this->correct_answers_text) {
            return false;
        }

        $userAnswer = strtolower(trim($userAnswer));
        $correctAnswers = array_map('strtolower', $this->correct_answers_text);

        return in_array($userAnswer, $correctAnswers);
    }

    /**
     * Get shuffled options
     */
    public function getShuffledOptions(): array
    {
        if (!$this->options) {
            return [];
        }

        $options = $this->options;

        if ($this->shuffle_options) {
            shuffle($options);
        }

        return $options;
    }

    /**
     * Get question preview text
     */
    public function getPreviewText(): string
    {
        $text = strip_tags($this->question_text);
        return strlen($text) > 100 ? substr($text, 0, 100) . '...' : $text;
    }

    /**
     * Scope for required questions
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope for questions by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('question_type', $type);
    }

    /**
     * Scope for questions with media
     */
    public function scopeWithMedia($query)
    {
        return $query->where(function($q) {
            $q->whereNotNull('image')
              ->orWhereNotNull('audio')
              ->orWhereNotNull('video');
        });
    }

    /**
     * Get localized question text based on current locale
     */
    public function getLocalizedQuestionTextAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->question_text_ar) {
            return $this->question_text_ar;
        }
        return $this->question_text;
    }

    /**
     * Get localized options based on current locale
     */
    public function getLocalizedOptionsAttribute(): ?array
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->options_ar) {
            return $this->options_ar;
        }
        return $this->options;
    }

    /**
     * Get localized correct answers text based on current locale
     */
    public function getLocalizedCorrectAnswersTextAttribute(): ?array
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->correct_answers_text_ar) {
            return $this->correct_answers_text_ar;
        }
        return $this->correct_answers_text;
    }

    /**
     * Get localized sample answer based on current locale
     */
    public function getLocalizedSampleAnswerAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->sample_answer_ar) {
            return $this->sample_answer_ar;
        }
        return $this->sample_answer;
    }

    /**
     * Get localized explanation based on current locale
     */
    public function getLocalizedExplanationAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'ar' && $this->explanation_ar) {
            return $this->explanation_ar;
        }
        return $this->explanation;
    }

    /**
     * Get shuffled options with localization
     */
    public function getLocalizedShuffledOptions(): array
    {
        $options = $this->localized_options;

        if (!$options) {
            return [];
        }

        if ($this->shuffle_options) {
            shuffle($options);
        }

        return $options;
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($question) {
            if (!$question->order) {
                $maxOrder = QuizQuestion::where('quiz_id', $question->quiz_id)->max('order');
                $question->order = ($maxOrder ?? 0) + 1;
            }
        });
    }
}
