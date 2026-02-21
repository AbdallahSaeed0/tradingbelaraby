<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\LectureCompletion;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show student dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Get user's enrollments with course details
        $enrollments = $user->enrollments()
            ->with(['course.instructor', 'course.instructors', 'course.category'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $stats = [
            'total_courses' => $enrollments->count(),
            'completed_courses' => $enrollments->where('status', 'completed')->count(),
            'in_progress_courses' => $enrollments->where('status', 'enrolled')->count(),
            'average_progress' => round($enrollments->avg('progress_percentage'), 1),
            'total_learning_hours' => $enrollments->sum(function($enrollment) {
                return ($enrollment->course->total_duration_minutes ?? 0) / 60; // Convert minutes to hours
            }),
        ];

        // Get recent activity
        $recent_activity = $user->enrollments()
            ->with(['course'])
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('student.dashboard', compact('enrollments', 'stats', 'recent_activity'));
    }

    /**
     * Show my courses page
     */
    public function myCourses(Request $request)
    {
        $user = Auth::user();

        $query = $user->enrollments()
            ->with(['course.instructor', 'course.instructors', 'course.category']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('course', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        switch ($request->get('sort', 'latest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'progress_high':
                $query->orderBy('progress_percentage', 'desc');
                break;
            case 'progress_low':
                $query->orderBy('progress_percentage', 'asc');
                break;
            case 'name':
                $query->join('courses', 'course_enrollments.course_id', '=', 'courses.id')
                      ->orderBy('courses.name', 'asc')
                      ->select('course_enrollments.*');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $enrollments = $query->paginate(12);

        // Calculate statistics
        $stats = [
            'total_courses' => $user->enrollments()->count(),
            'completed_courses' => $user->enrollments()->where('status', 'completed')->count(),
            'in_progress_courses' => $user->enrollments()->where('status', 'enrolled')->count(),
            'average_progress' => round($user->enrollments()->avg('progress_percentage'), 1),
            'total_learning_hours' => $user->enrollments()->join('courses', 'course_enrollments.course_id', '=', 'courses.id')
                ->sum('courses.total_duration_minutes') / 60, // Convert minutes to hours
        ];

        return view('student.my-courses', compact('enrollments', 'stats'));
    }

    /**
     * Show course learning page
     */
    public function learnCourse($courseId)
    {
        $user = Auth::user();

        $enrollment = $user->enrollments()
            ->with(['course.sections.lectures', 'course.instructor', 'course.instructors'])
            ->where('course_id', $courseId)
            ->firstOrFail();

        $course = $enrollment->course;
        
        // Refresh course to ensure enable_certificate is loaded
        $course->refresh();

        // Load homework with user submissions
        $course->load(['homework' => function($query) use ($user) {
            $query->with(['submissions' => function($subQuery) use ($user) {
                $subQuery->where('user_id', $user->id);
            }]);
        }]);

        // Load live classes with user registration status
        $course->load(['liveClasses' => function($query) use ($user) {
            $query->with(['registrations' => function($regQuery) use ($user) {
                $regQuery->where('user_id', $user->id);
            }]);
        }]);

        // Load quizzes with question count for the Quiz tab
        $course->load(['quizzes' => function($query) {
            $query->withCount('questions')->where('is_published', true)->orderBy('id');
        }]);

        // Compute last/next lecture for "Continue to Lecture" modal
        $resumeLecture = null;
        $lastCompletion = LectureCompletion::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->whereIn('lecture_id', $course->sections->flatMap->lectures->pluck('id'))
            ->orderByDesc('last_accessed_at')
            ->with('lecture.section')
            ->first();
        if ($lastCompletion && $lastCompletion->lecture && $lastCompletion->lecture->section) {
            $lecture = $lastCompletion->lecture;
            $resumeLecture = [
                'id' => $lecture->id,
                'section_id' => $lecture->section->id,
                'title' => $lecture->title,
                'title_ar' => $lecture->title_ar,
            ];
        } else {
            $firstSection = $course->sections()->orderBy('order')->first();
            $firstLecture = $firstSection ? $firstSection->lectures()->orderBy('order')->first() : null;
            if ($firstLecture) {
                $resumeLecture = [
                    'id' => $firstLecture->id,
                    'section_id' => $firstLecture->section_id,
                    'title' => $firstLecture->title,
                    'title_ar' => $firstLecture->title_ar,
                ];
            }
        }

        return view('student.learn-course', compact('enrollment', 'course', 'resumeLecture'));
    }

    /**
     * Mark lecture as completed
     */
    public function completeLecture(Request $request, $courseId, $lectureId)
    {
        $user = Auth::user();

        $enrollment = $user->enrollments()
            ->where('course_id', $courseId)
            ->firstOrFail();

        // Mark lecture as completed
        $completion = LectureCompletion::firstOrCreate([
            'user_id' => $user->id,
            'lecture_id' => $lectureId,
            'course_id' => $courseId
        ], [
            'is_completed' => true,
            'completed_at' => now(),
            'progress_percentage' => 100,
            'watch_time_seconds' => 0
        ]);

        if (!$completion->wasRecentlyCreated) {
            $completion->update([
                'is_completed' => true,
                'completed_at' => now(),
                'progress_percentage' => 100
            ]);
        }

        // Update progress
        $totalLectures = $enrollment->course->sections->sum(function($section) {
            return $section->lectures->count();
        });

        $completedLectures = $enrollment->lectureCompletions()->count();
        $progress = $totalLectures > 0 ? round(($completedLectures / $totalLectures) * 100) : 0;

        $enrollment->update([
            'progress_percentage' => $progress,
            'completed_lectures' => $completedLectures,
            'total_lectures' => $totalLectures
        ]);

        // Check if course is completed
        if ($progress >= 100) {
            $enrollment->update(['status' => 'completed']);
        }

        return response()->json([
            'success' => true,
            'progress' => $progress,
            'completed_lectures' => $completedLectures,
            'total_lectures' => $totalLectures
        ]);
    }
}
