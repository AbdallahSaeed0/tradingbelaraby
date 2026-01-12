<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\CartItem;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Get user's cart items
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $cartItems = $user->cartItems()->with('course.category', 'course.instructor')->get();

        // Extract courses from cart items
        $courses = $cartItems
            ->map(function ($item) {
                return $item->course;
            })
            ->filter(function ($course) {
                return $course !== null;
            })
            ->values();

        // Calculate total
        $total = $cartItems->sum(function ($item) {
            return $item->getPrice();
        });

        return response()->json([
            'success' => true,
            'data' => CourseResource::collection($courses),
            'total' => $total,
        ]);
    }

    /**
     * Add course to cart
     */
    public function store(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);

        // Check if already in cart
        if ($user->hasInCart($course)) {
            return response()->json([
                'success' => false,
                'message' => 'Course is already in your cart',
            ], 400);
        }

        // Check if already enrolled
        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already enrolled in this course',
            ], 400);
        }

        // Add to cart
        CartItem::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course added to cart successfully',
            'data' => new CourseResource($course),
        ], 201);
    }

    /**
     * Remove course from cart
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

        // Remove from cart
        $deleted = $user->cartItems()->where('course_id', $course->id)->delete();

        if (!$deleted) {
            return response()->json([
                'success' => false,
                'message' => 'Course not found in cart',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Course removed from cart successfully',
        ]);
    }

    /**
     * Get cart total
     */
    public function total(Request $request): JsonResponse
    {
        $user = $request->user();
        $cartItems = $user->cartItems()->with('course', 'bundle')->get();

        $total = $cartItems->sum(function ($item) {
            return $item->getPrice();
        });

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'item_count' => $cartItems->count(),
            ],
        ]);
    }

    /**
     * Get cart item count
     */
    public function count(Request $request): JsonResponse
    {
        $user = $request->user();
        $count = $user->cartItems()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count,
            ],
        ]);
    }

    /**
     * Clear cart
     */
    public function clear(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->cartItems()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
        ]);
    }
}
