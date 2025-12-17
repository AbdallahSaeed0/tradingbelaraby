<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Notifications\CourseEnrollmentNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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

        // Prevent direct enrollment for paid courses - they must go through checkout/cart
        if (!$course->is_free && $course->price > 0) {
            $message = 'This is a paid course. Please add it to your cart and complete checkout.';

            if ($request->expectsJson()) {
                // Return 200 with success=false so frontend can handle gracefully without JS error
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ]);
            }

            return redirect()->route('cart.index')->with('error', $message);
        }

        // Create enrollment (free courses)
        $enrollment = $user->enrollments()->create([
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
            'progress_percentage' => 0,
        ]);

        // Send enrollment notification email
        try {
            $language = Session::get('frontend_locale', config('app.locale'));
            $language = in_array($language, ['ar', 'en']) ? $language : 'en';
            $user->notify(new CourseEnrollmentNotification($course, null, $language));
        } catch (\Exception $e) {
            Log::error('Failed to send enrollment notification', [
                'user_id' => $user->id,
                'course_id' => $course->id,
                'error' => $e->getMessage()
            ]);
        }

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
