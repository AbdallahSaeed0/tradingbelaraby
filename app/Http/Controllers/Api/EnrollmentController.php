<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EnrollmentResource;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Get user's enrollments
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $enrollments = $user->enrollments()
            ->with(['course.category', 'course.instructor', 'course.sections.lectures'])
            ->orderBy('enrolled_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => EnrollmentResource::collection($enrollments->items()),
            'meta' => [
                'current_page' => $enrollments->currentPage(),
                'last_page' => $enrollments->lastPage(),
                'per_page' => $enrollments->perPage(),
                'total' => $enrollments->total(),
            ],
        ]);
    }

    /**
     * Enroll in a course
     */
    public function store(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);

        // Check if already enrolled
        $existingEnrollment = $user->enrollments()
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Already enrolled in this course',
            ], 400);
        }

        // For paid courses, require payment first
        if (!$course->is_free && $course->price > 0) {
            return response()->json([
                'success' => false,
                'message' => 'This is a paid course. Please create an order first.',
            ], 400);
        }

        // Create enrollment for free courses
        $enrollment = $user->enrollments()->create([
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully enrolled in course',
            'data' => new EnrollmentResource($enrollment->load('course')),
        ], 201);
    }

    /**
     * Get enrollment details
     */
    public function show(string $id): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $enrollment = $user->enrollments()
            ->with('course')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new EnrollmentResource($enrollment),
        ]);
    }

    /**
     * Check if user is enrolled in a course
     */
    public function check(Request $request, string $courseId): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => true,
                'enrolled' => false,
            ]);
        }

        $enrolled = $user->enrollments()
            ->where('course_id', $courseId)
            ->exists();

        return response()->json([
            'success' => true,
            'enrolled' => $enrolled,
        ]);
    }
}
