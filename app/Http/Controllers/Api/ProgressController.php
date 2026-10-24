<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLecture;
use App\Models\LectureCompletion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Lecture / course progress for the mobile app.
 * Uses the same lecture_completions table as the website, so progress is shared.
 */
class ProgressController extends Controller
{
    /** Watched ratio at which a lecture counts as completed (same as the app's 80% rule) */
    private const COMPLETION_THRESHOLD = 0.8;

    /**
     * Get progress for a lecture
     */
    public function showLecture(string $lectureId): JsonResponse
    {
        $lecture = CourseLecture::findOrFail($lectureId);
        if (!$this->accessibleEnrollment($lecture->course_id)) {
            return $this->notEnrolled();
        }

        $completion = LectureCompletion::where('user_id', Auth::id())
            ->where('lecture_id', $lecture->id)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $completion ? $this->formatCompletion($completion) : null,
        ]);
    }

    /**
     * Save watch progress for a lecture
     */
    public function updateLecture(Request $request, string $lectureId): JsonResponse
    {
        $validated = $request->validate([
            'watched_duration' => 'required|integer|min:0',
            'total_duration' => 'required|integer|min:0',
            'last_position' => 'nullable|numeric|min:0|max:1',
        ]);

        $lecture = CourseLecture::findOrFail($lectureId);
        $enrollment = $this->accessibleEnrollment($lecture->course_id);
        if (!$enrollment) {
            return $this->notEnrolled();
        }

        $completion = LectureCompletion::firstOrNew([
            'user_id' => Auth::id(),
            'lecture_id' => $lecture->id,
        ]);

        $total = $validated['total_duration'];
        // Never move progress backwards (e.g. user rewatches the start)
        $watched = max((int) $completion->watch_time_seconds, min($validated['watched_duration'], $total ?: PHP_INT_MAX));
        $ratio = $total > 0 ? $watched / $total : 0;

        $completion->fill([
            'course_id' => $lecture->course_id,
            'watch_time_seconds' => $watched,
            'total_duration_seconds' => $total,
            'progress_percentage' => max((float) $completion->progress_percentage, round($ratio * 100, 2)),
            'last_accessed_at' => now(),
        ]);

        if (!$completion->is_completed && $ratio >= self::COMPLETION_THRESHOLD) {
            $completion->is_completed = true;
            $completion->completed_at = now();
            $completion->watched_entire_video = $ratio >= 1;
        }
        $completion->save();

        $this->syncEnrollment($enrollment);

        return response()->json([
            'success' => true,
            'data' => $this->formatCompletion($completion, $validated['last_position'] ?? null),
        ]);
    }

    /**
     * Mark a lecture as completed
     */
    public function completeLecture(string $lectureId): JsonResponse
    {
        $lecture = CourseLecture::findOrFail($lectureId);
        $enrollment = $this->accessibleEnrollment($lecture->course_id);
        if (!$enrollment) {
            return $this->notEnrolled();
        }

        $completion = LectureCompletion::firstOrNew([
            'user_id' => Auth::id(),
            'lecture_id' => $lecture->id,
        ]);
        $completion->course_id = $lecture->course_id;
        $completion->save();
        if (!$completion->is_completed) {
            $completion->markAsCompleted();
        }

        $this->syncEnrollment($enrollment);

        return response()->json([
            'success' => true,
            'data' => $this->formatCompletion($completion->fresh()),
        ]);
    }

    /**
     * Get progress summary (and per-lecture progress) for a course
     */
    public function showCourse(string $courseId): JsonResponse
    {
        $course = Course::findOrFail($courseId);
        $enrollment = $this->accessibleEnrollment($course->id);
        if (!$enrollment) {
            return $this->notEnrolled();
        }

        $lectureIds = $this->publishedLectureIds($course->id);
        $completions = LectureCompletion::where('user_id', Auth::id())
            ->whereIn('lecture_id', $lectureIds)
            ->get();

        $completed = $completions->where('is_completed', true)->count();
        $lastAccessed = $completions->max('last_accessed_at');

        return response()->json([
            'success' => true,
            'data' => [
                'user_id' => (string) Auth::id(),
                'course_id' => (string) $course->id,
                'total_lectures' => $lectureIds->count(),
                'completed_lectures' => $completed,
                'total_duration' => (int) $completions->sum('total_duration_seconds'),
                'watched_duration' => (int) $completions->sum('watch_time_seconds'),
                'last_accessed_at' => $lastAccessed ? $lastAccessed->toIso8601String() : null,
                'completed_at' => $enrollment->completed_at?->toIso8601String(),
                'updated_at' => $enrollment->updated_at?->toIso8601String(),
                'lectures' => $completions->map(fn ($c) => $this->formatCompletion($c))->values(),
            ],
        ]);
    }

    private function accessibleEnrollment($courseId): ?CourseEnrollment
    {
        return Auth::user()->enrollments()
            ->where('course_id', $courseId)
            ->accessible()
            ->first();
    }

    private function notEnrolled(): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => 'You are not enrolled in this course',
        ], 403);
    }

    /**
     * Published lectures in published sections (same set the course API returns)
     */
    private function publishedLectureIds($courseId)
    {
        return CourseLecture::where('course_id', $courseId)
            ->where('is_published', true)
            ->whereHas('section', fn ($q) => $q->where('is_published', true))
            ->pluck('id');
    }

    /**
     * Keep enrollment progress (shown on the website and in "My courses") in sync
     */
    private function syncEnrollment(CourseEnrollment $enrollment): void
    {
        $lectureIds = $this->publishedLectureIds($enrollment->course_id);
        $completed = LectureCompletion::where('user_id', $enrollment->user_id)
            ->whereIn('lecture_id', $lectureIds)
            ->where('is_completed', true)
            ->count();

        $enrollment->updateProgress($completed, $lectureIds->count());
    }

    private function formatCompletion(LectureCompletion $completion, ?float $lastPosition = null): array
    {
        $total = (int) $completion->total_duration_seconds;
        $watched = (int) $completion->watch_time_seconds;

        return [
            'id' => (string) $completion->id,
            'user_id' => (string) $completion->user_id,
            'course_id' => (string) $completion->course_id,
            'lecture_id' => (string) $completion->lecture_id,
            'watched_duration' => $watched,
            'total_duration' => $total,
            'last_position' => $lastPosition ?? ($total > 0 ? min(1, $watched / $total) : null),
            'is_completed' => (bool) $completion->is_completed,
            'completed_at' => $completion->completed_at?->toIso8601String(),
            'updated_at' => $completion->updated_at?->toIso8601String(),
        ];
    }
}
