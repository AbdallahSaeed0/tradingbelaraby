<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = Auth::user()->enrollments()->with('course')->paginate(20);
        return view('enrollments.index', compact('enrollments'));
    }

    public function enroll(Request $request, Course $course)
    {
        try {
            Log::info('Enrollment request received', [
                'course_id' => $course->id,
                'user_id' => Auth::id(),
                'expects_json' => $request->expectsJson(),
                'accept_header' => $request->header('Accept'),
                'content_type' => $request->header('Content-Type')
            ]);

            $user = Auth::user();

            if (!$user) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'User not authenticated'
                    ], 401);
                }
                return redirect()->route('login');
            }

        // Check if user is already enrolled
        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'You are already enrolled in this course!'
                ]);
            }
            return redirect()->route('courses.show', $course->id)->with('info', 'You are already enrolled in this course!');
        }

        // Create enrollment
        $user->enrollments()->create([
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);

        // Return JSON for AJAX requests, redirect for regular requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Successfully enrolled in course!'
            ]);
        }

        return redirect()->route('courses.show', $course->id)->with('success', 'Enrolled successfully!');

        } catch (\Exception $e) {
            Log::error('Enrollment error', [
                'error' => $e->getMessage(),
                'course_id' => $course->id,
                'user_id' => Auth::id()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred during enrollment'
                ], 500);
            }

            return redirect()->back()->with('error', 'An error occurred during enrollment');
        }
    }

    public function export()
    {
        // Export logic here
        return response()->json(['message' => 'Export not implemented.']);
    }
}
