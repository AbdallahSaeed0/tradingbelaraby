<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use App\Models\Course;
use App\Models\CourseLecture;
use App\Models\LectureCompletion;
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

        [$totalSql, $doneSql, $percentSql] = $this->liveProgressSql();

        // Apply progress filter (on live lecture progress)
        if ($request->filled('progress')) {
            $progressRange = explode('-', $request->progress);
            if (count($progressRange) == 2) {
                $min = (int)$progressRange[0];
                $max = (int)$progressRange[1];
                $query->whereRaw("({$percentSql}) BETWEEN ? AND ?", [$min, $max]);
            }
        }

        // Apply sorting
        switch ($request->get('sort', 'latest')) {
            case 'oldest':
                $query->orderBy('course_enrollments.created_at', 'asc');
                break;
            case 'progress_high':
                $query->orderByRaw("({$percentSql}) DESC")->orderBy('course_enrollments.created_at', 'desc');
                break;
            case 'progress_low':
                $query->orderByRaw("({$percentSql}) ASC")->orderBy('course_enrollments.created_at', 'desc');
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

        // Pagination size (default 15, user-controllable)
        $perPage = (int) $request->get('per_page', 15);
        $allowedPerPage = [10, 15, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 15;
        }

        // addSelect() with raw expressions does not add "table.*" by itself
        if (is_null($query->getQuery()->columns)) {
            $query->select('course_enrollments.*');
        }
        $query->addSelect([
            DB::raw("({$totalSql}) as live_total"),
            DB::raw("({$doneSql}) as live_completed"),
        ]);

        $enrollments = $query->paginate($perPage)->appends($request->query());
        $this->attachLiveProgress($enrollments->getCollection());

        // Calculate stats
        $stats = [
            'total_enrollments' => CourseEnrollment::count(),
            'completed_enrollments' => CourseEnrollment::where('status', 'completed')->count(),
            'in_progress_enrollments' => CourseEnrollment::where('status', 'enrolled')->count(),
            'cancelled_enrollments' => CourseEnrollment::where('status', 'cancelled')->count(),
            'average_progress' => $this->averageLiveProgress(),
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
        $enrollment->load([
            'user',
            'course.instructor',
            'course.instructors',
            'course.sections' => fn ($q) => $q->where('is_published', true),
            'course.sections.publishedLectures',
        ]);

        $completions = LectureCompletion::where('user_id', $enrollment->user_id)
            ->where('course_id', $enrollment->course_id)
            ->get()
            ->keyBy('lecture_id');

        $sections = $enrollment->course->sections->map(function ($section) use ($completions) {
            $lectures = $section->publishedLectures->map(fn ($lecture) => [
                'lecture' => $lecture,
                'completion' => $completions->get($lecture->id),
            ]);
            $done = $lectures->filter(fn ($row) => $row['completion']?->is_completed)->count();

            return [
                'section' => $section,
                'lectures' => $lectures,
                'done' => $done,
                'total' => $lectures->count(),
            ];
        })->filter(fn ($row) => $row['total'] > 0)->values();

        $totalLectures = $sections->sum('total');
        $completedLectures = $sections->sum('done');
        $lastActivity = collect([
            $enrollment->last_accessed_at,
            $completions->max('last_accessed_at'),
            $completions->max('completed_at'),
        ])->filter()->max();

        $progress = [
            'total' => $totalLectures,
            'completed' => $completedLectures,
            'percent' => $totalLectures > 0 ? (int) round($completedLectures / $totalLectures * 100) : 0,
            'watch_seconds' => (int) $completions->sum('watch_time_seconds'),
            'last_activity' => $lastActivity ? \Illuminate\Support\Carbon::parse($lastActivity) : null,
        ];

        return view('admin.enrollments.show', compact('enrollment', 'sections', 'progress'));
    }

    /**
     * SQL for live lecture progress of a course_enrollments row.
     * The stored progress columns on enrollments are not kept in sync, so the
     * admin list, filter, sort and stats use these instead. Counts only published
     * lectures in published sections, matching the enrollment detail page.
     *
     * @return array{0: string, 1: string, 2: string} [total, completed, percent]
     */
    private function liveProgressSql(): array
    {
        $publishedLectures = 'FROM course_lectures cl
            JOIN course_sections cs ON cs.id = cl.section_id AND cs.is_published = 1
            WHERE cl.course_id = course_enrollments.course_id AND cl.is_published = 1';

        $total = "SELECT COUNT(*) {$publishedLectures}";
        $done = "SELECT COUNT(*) {$publishedLectures}
            AND EXISTS (SELECT 1 FROM lecture_completions lc
                WHERE lc.lecture_id = cl.id AND lc.user_id = course_enrollments.user_id AND lc.is_completed = 1)";
        $percent = "CASE WHEN ({$total}) = 0 THEN 0 ELSE ROUND(({$done}) * 100 / ({$total})) END";

        return [$total, $done, $percent];
    }

    private function averageLiveProgress(): float
    {
        [, , $percentSql] = $this->liveProgressSql();

        // Per-row percent in a derived table first: MySQL (ONLY_FULL_GROUP_BY) rejects
        // correlated subqueries on outer columns directly inside an aggregate.
        $perEnrollment = CourseEnrollment::query()->selectRaw("({$percentSql}) as live_percent");

        return round((float) DB::query()->fromSub($perEnrollment, 'live_progress')->avg('live_percent'), 1);
    }

    /**
     * Fill in percent and last activity for a page of enrollments whose
     * live_total / live_completed were selected by the index query.
     */
    private function attachLiveProgress($enrollments): void
    {
        if ($enrollments->isEmpty()) {
            return;
        }

        $lastActivity = LectureCompletion::whereIn('course_id', $enrollments->pluck('course_id')->unique())
            ->whereIn('user_id', $enrollments->pluck('user_id')->unique())
            ->selectRaw('user_id, course_id, MAX(updated_at) as last_at')
            ->groupBy('user_id', 'course_id')
            ->get()
            ->keyBy(fn ($row) => $row->user_id . '-' . $row->course_id);

        foreach ($enrollments as $enrollment) {
            $total = (int) $enrollment->live_total;
            $completed = (int) $enrollment->live_completed;
            $row = $lastActivity->get($enrollment->user_id . '-' . $enrollment->course_id);
            $lastAt = collect([$enrollment->last_accessed_at, $row->last_at ?? null])->filter()->max();

            $enrollment->live_total = $total;
            $enrollment->live_completed = $completed;
            $enrollment->live_percent = $total > 0 ? (int) round($completed / $total * 100) : 0;
            $enrollment->live_last_activity = $lastAt ? \Illuminate\Support\Carbon::parse($lastAt) : null;
        }
    }

    /**
     * Update enrollment status
     */
    public function updateStatus(Request $request, CourseEnrollment $enrollment)
    {
        $request->validate([
            'status' => 'required|in:active,enrolled,completed,cancelled,pending'
        ]);

        $updateData = ['status' => $request->status];

        // When admin confirms a bank transfer (activates a pending enrollment), set enrolled_at
        if ($request->status === 'active' && !$enrollment->enrolled_at) {
            $updateData['enrolled_at'] = now();
        }

        $enrollment->update($updateData);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Enrollment status updated successfully'
            ]);
        }

        return back()->with('success', 'Enrollment status updated successfully');
    }

    /**
     * Remove a student from a course (deletes the enrollment, revoking access)
     */
    public function destroy(CourseEnrollment $enrollment)
    {
        $studentName = $enrollment->user->name ?? 'Student';

        $enrollment->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $studentName . ' was removed from the course'
            ]);
        }

        return back()->with('success', $studentName . ' was removed from the course');
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
            'average_progress' => $this->averageLiveProgress(),
        ];

        return response()->json($analytics);
    }
}
