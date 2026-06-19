<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\WishlistItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    /**
     * Get user's wishlist items
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $wishlistItems = $user->wishlistItems()->with('course.category', 'course.instructor')->get();

        $courses = $wishlistItems
            ->map(function ($item) {
                return $item->course;
            })
            ->filter(function ($course) {
                return $course !== null;
            })
            ->values();

        return response()->json([
            'success' => true,
            'data' => CourseResource::collection($courses),
        ]);
    }

    /**
     * Add course to wishlist
     */
    public function store(Request $request, string $courseId): JsonResponse
    {
        $user = $request->user();
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found',
            ], 404);
        }

        if ($user->hasInWishlist($course)) {
            return response()->json([
                'success' => false,
                'message' => 'Course is already in your wishlist',
            ], 400);
        }

        WishlistItem::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course added to wishlist successfully',
            'data' => new CourseResource($course),
        ], 201);
    }

    /**
     * Remove course from wishlist
     */
    public function destroy(Request $request, string $courseId): JsonResponse
    {
        $user = $request->user();
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found',
            ], 404);
        }

        $deleted = $user->wishlistItems()->where('course_id', $course->id)->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found in wishlist',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Course removed from wishlist successfully',
        ]);
    }

    /**
     * Toggle wishlist status
     */
    public function toggle(Request $request, string $courseId): JsonResponse
    {
        $user = $request->user();
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found',
            ], 404);
        }

        if ($user->hasInWishlist($course)) {
            $user->wishlistItems()->where('course_id', $course->id)->delete();
            $message = 'Course removed from wishlist';
            $inWishlist = false;
        } else {
            WishlistItem::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
            $message = 'Course added to wishlist';
            $inWishlist = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'in_wishlist' => $inWishlist,
                'course' => new CourseResource($course),
            ],
        ]);
    }

    /**
     * Check if course is in wishlist
     */
    public function check(Request $request, string $courseId): JsonResponse
    {
        $user = $request->user();
        $course = Course::find($courseId);

        if (!$course) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found',
                'data' => [
                    'in_wishlist' => false,
                ],
            ], 404);
        }

        $inWishlist = $user->hasInWishlist($course);

        return response()->json([
            'success' => true,
            'data' => [
                'in_wishlist' => $inWishlist,
            ],
        ]);
    }
}
