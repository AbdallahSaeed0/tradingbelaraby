<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseRatingController extends Controller
{
    /**
     * Store or update a course rating/review.
     */
    public function store(Request $request, Course $course)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'You must be logged in to submit a review.',
            ], 401);
        }

        // Optional: ensure user is enrolled before reviewing
        if (!$course->isEnrolledBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You must be enrolled in this course to leave a review.',
            ], 403);
        }

        $validated = $request->validate([
            'review' => 'nullable|string|max:2000',
            'content_quality' => 'nullable|integer|min:1|max:5',
            'instructor_quality' => 'nullable|integer|min:1|max:5',
            'value_for_money' => 'nullable|integer|min:1|max:5',
            'course_material' => 'nullable|integer|min:1|max:5',
        ]);

        // Compute overall rating on the backend to avoid frontend rounding issues
        $ratings = array_filter([
            $validated['content_quality'] ?? null,
            $validated['instructor_quality'] ?? null,
            $validated['value_for_money'] ?? null,
            $validated['course_material'] ?? null,
        ], fn($value) => !is_null($value) && $value > 0);

        if (empty($ratings)) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide at least one rating.',
            ], 422);
        }

        $overallRating = (int) round(array_sum($ratings) / count($ratings));
        $overallRating = max(1, min(5, $overallRating));

        // Create or update (one rating per user per course)
        $rating = CourseRating::updateOrCreate(
            [
                'course_id' => $course->id,
                'user_id' => $user->id,
            ],
            array_merge($validated, [
                'rating' => $overallRating,
                // Default status can be pending for moderation; adjust as needed
                'status' => 'approved',
            ])
        );

        // Recalculate course average rating (ratings_count column doesn't exist)
        $course->average_rating = $course->ratings()->avg('rating') ?? 0;
        $course->save();

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your review!',
            'rating' => $rating,
            'average_rating' => $course->average_rating,
            'ratings_count' => $course->ratings()->count(),
        ]);
    }
}


