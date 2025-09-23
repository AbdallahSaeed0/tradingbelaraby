<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    public function index()
    {
        $enrollments = Auth::user()->enrollments()->with('course')->paginate(20);
        return view('enrollments.index', compact('enrollments'));
    }

    public function enroll(Request $request, Course $course)
    {
        $user = Auth::user();
        if (!$user->enrollments()->where('course_id', $course->id)->exists()) {
            $user->enrollments()->create([
                'course_id' => $course->id,
                'status' => 'active',
                'enrolled_at' => now(),
                'progress_percentage' => 0,
            ]);
        }
        return redirect()->route('courses.show', $course->id)->with('success', 'Enrolled successfully!');
    }

    public function export()
    {
        // Export logic here
        return response()->json(['message' => 'Export not implemented.']);
    }
}
