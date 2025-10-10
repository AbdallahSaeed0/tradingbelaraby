<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EnrollmentsController extends Controller
{
    /**
     * Display all enrollments with filtering
     */
    public function index(Request $request)
    {
        $query = CourseEnrollment::with(['user', 'course.instructor', 'course.instructors']);

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('course', function($courseQuery) use ($search) {
                    $courseQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Apply course filter
        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply progress filter
        if ($request->filled('progress')) {
            $progressRange = explode('-', $request->progress);
            if (count($progressRange) == 2) {
                $min = (int)$progressRange[0];
                $max = (int)$progressRange[1];
                $query->whereBetween('progress_percentage', [$min, $max]);
            }
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
                $query->join('users', 'course_enrollments.user_id', '=', 'users.id')
                      ->orderBy('users.name', 'asc')
                      ->select('course_enrollments.*');
                break;
            case 'course':
                $query->join('courses', 'course_enrollments.course_id', '=', 'courses.id')
                      ->orderBy('courses.name', 'asc')
                      ->select('course_enrollments.*');
                break;
            default:
                $query->latest();
                break;
        }

        $enrollments = $query->paginate(20);

        // Calculate stats
        $stats = [
            'total_enrollments' => CourseEnrollment::count(),
            'completed_enrollments' => CourseEnrollment::where('status', 'completed')->count(),
            'in_progress_enrollments' => CourseEnrollment::where('status', 'enrolled')->count(),
            'cancelled_enrollments' => CourseEnrollment::where('status', 'cancelled')->count(),
            'average_progress' => round(CourseEnrollment::avg('progress_percentage'), 1),
            'active_courses' => Course::where('status', 'published')->count(),
        ];

        // Get all courses for filter dropdown
        $courses = Course::published()->orderBy('name')->get();

        return view('admin.enrollments.index', compact('enrollments', 'stats', 'courses'));
    }

    /**
     * Export enrollments data
     */
    public function export(Request $request)
    {
        $query = CourseEnrollment::with(['user', 'course.instructor', 'course.instructors']);

        // Apply same filters as index method
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('course', function($courseQuery) use ($search) {
                    $courseQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('progress')) {
            $progressRange = explode('-', $request->progress);
            if (count($progressRange) == 2) {
                $min = (int)$progressRange[0];
                $max = (int)$progressRange[1];
                $query->whereBetween('progress_percentage', [$min, $max]);
            }
        }

        $enrollments = $query->get();

        // Generate CSV
        $filename = 'enrollments_' . date('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($enrollments) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Student Name',
                'Student Email',
                'Course Name',
                'Instructor',
                'Enrollment Date',
                'Progress (%)',
                'Status',
                'Completed Lectures',
                'Total Lectures',
                'Last Activity'
            ]);

            // CSV data
            foreach ($enrollments as $enrollment) {
                fputcsv($file, [
                    $enrollment->user->name,
                    $enrollment->user->email,
                    $enrollment->course->name,
                    $enrollment->course->instructor->name ?? 'N/A',
                    $enrollment->created_at->format('Y-m-d H:i:s'),
                    $enrollment->progress_percentage,
                    $enrollment->status,
                    $enrollment->completed_lectures,
                    $enrollment->total_lectures,
                    $enrollment->last_activity ? $enrollment->last_activity->format('Y-m-d H:i:s') : 'N/A'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show enrollment details
     */
    public function show(CourseEnrollment $enrollment)
    {
        $enrollment->load(['user', 'course.instructor', 'course.sections.lectures']);

        return view('admin.enrollments.show', compact('enrollment'));
    }

    /**
     * Update enrollment status
     */
    public function updateStatus(Request $request, CourseEnrollment $enrollment)
    {
        $request->validate([
            'status' => 'required|in:enrolled,completed,cancelled'
        ]);

        $enrollment->update(['status' => $request->status]);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Enrollment status updated successfully'
            ]);
        }

        return back()->with('success', 'Enrollment status updated successfully');
    }

    /**
     * Get enrollment analytics
     */
    public function analytics()
    {
        $analytics = [
            'total_enrollments' => CourseEnrollment::count(),
            'monthly_enrollments' => CourseEnrollment::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->whereYear('created_at', date('Y'))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            'course_enrollments' => CourseEnrollment::selectRaw('courses.name, COUNT(*) as count')
                ->join('courses', 'course_enrollments.course_id', '=', 'courses.id')
                ->groupBy('courses.id', 'courses.name')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),
            'completion_rates' => [
                'completed' => CourseEnrollment::where('status', 'completed')->count(),
                'in_progress' => CourseEnrollment::where('status', 'enrolled')->count(),
                'cancelled' => CourseEnrollment::where('status', 'cancelled')->count(),
            ],
            'average_progress' => round(CourseEnrollment::avg('progress_percentage'), 1),
        ];

        return response()->json($analytics);
    }
}
