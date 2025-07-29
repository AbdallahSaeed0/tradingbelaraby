<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'user_id',
        'rating',
        'review',
        'title',
        'content_quality',
        'instructor_quality',
        'value_for_money',
        'course_material',
        'status',
        'admin_notes',
        'helpful_votes',
        'total_votes',
    ];

    protected $casts = [
        'rating' => 'integer',
        'content_quality' => 'integer',
        'instructor_quality' => 'integer',
        'value_for_money' => 'integer',
        'course_material' => 'integer',
        'helpful_votes' => 'integer',
        'total_votes' => 'integer',
    ];

    /**
     * Get the course that owns the rating
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the user that owns the rating
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for approved ratings
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for pending ratings
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get rating stars attribute
     */
    public function getRatingStarsAttribute(): string
    {
        $rating = $this->rating;
        $fullStars = floor($rating);
        $halfStar = $rating - $fullStars >= 0.5;
        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);

        $stars = str_repeat('★', $fullStars);
        if ($halfStar) $stars .= '☆';
        $stars .= str_repeat('☆', $emptyStars);

        return $stars;
    }

    /**
     * Get helpful percentage attribute
     */
    public function getHelpfulPercentageAttribute(): int
    {
        if ($this->total_votes === 0) {
            return 0;
        }
        return round(($this->helpful_votes / $this->total_votes) * 100);
    }

    /**
     * Get average detailed rating
     */
    public function getAverageDetailedRatingAttribute(): ?float
    {
        $ratings = array_filter([
            $this->content_quality,
            $this->instructor_quality,
            $this->value_for_money,
            $this->course_material,
        ]);

        if (empty($ratings)) {
            return null;
        }

        return round(array_sum($ratings) / count($ratings), 1);
    }
}
