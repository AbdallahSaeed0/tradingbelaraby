<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\LectureCompletion;
use App\Models\QuizAttempt;
use App\Models\HomeworkSubmission;
use App\Models\LiveClassRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProgressController extends Controller
{
    /**
     * Get overall progress for a student
     */
    public function overview()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Get all courses user has activity in
        $courseIds = LectureCompletion::where('user_id', $user->id)
            ->with('lecture.section.course')
            ->get()
            ->pluck('lecture.section.course.id')
            ->unique();

        $courses = Course::whereIn('id', $courseIds)->with(['sections.lectures'])->get();

        $overallStats = [
            'enrolled_courses' => $courses->count(),
            'completed_courses' => 0,
            'total_lectures' => 0,
            'completed_lectures' => 0,
            'total_quizzes' => 0,
            'passed_quizzes' => 0,
            'total_homework' => 0,
            'submitted_homework' => 0,
            'live_classes_attended' => LiveClassRegistration::where('user_id', $user->id)
                ->where('status', 'attended')->count(),
        ];

        foreach ($courses as $course) {
            $totalLectures = $course->sections->sum(function($section) {
                return $section->lectures->count();
            });

            $completedLectures = LectureCompletion::where('user_id', $user->id)
                ->whereIn('lecture_id',
                    $course->sections->flatMap(function($section) {
                        return $section->lectures->pluck('id');
                    })
                )
                ->where('is_completed', true)
                ->count();

            $overallStats['total_lectures'] += $totalLectures;
            $overallStats['completed_lectures'] += $completedLectures;

            // Check if course is completed (all lectures done)
            if ($totalLectures > 0 && $completedLectures >= $totalLectures) {
                $overallStats['completed_courses']++;
            }
        }

        // Quiz statistics
        $quizAttempts = QuizAttempt::where('user_id', $user->id)
            ->where('status', 'completed')
            ->get();

        $overallStats['total_quizzes'] = $quizAttempts->pluck('quiz_id')->unique()->count();
        $overallStats['passed_quizzes'] = $quizAttempts->where('passed', true)
            ->pluck('quiz_id')->unique()->count();

        // Homework statistics
        $homeworkSubmissions = HomeworkSubmission::where('user_id', $user->id)->get();
        $overallStats['total_homework'] = $homeworkSubmissions->pluck('homework_id')->unique()->count();
        $overallStats['submitted_homework'] = $homeworkSubmissions->count();

        return response()->json($overallStats);
    }

    /**
     * Get detailed progress for a specific course
     */
    public function courseProgress(Course $course)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $course->load(['sections.lectures', 'quizzes', 'homework', 'liveClasses']);

        // Lecture progress
        $lectureProgress = [];
        foreach ($course->sections as $section) {
            $sectionData = [
                'section' => $section,
                'lectures' => []
            ];

            foreach ($section->lectures as $lecture) {
                $completion = LectureCompletion::where('user_id', $user->id)
                    ->where('lecture_id', $lecture->id)
                    ->first();

                $sectionData['lectures'][] = [
                    'lecture' => $lecture,
                    'is_completed' => $completion?->is_completed ?? false,
                    'watch_time' => $completion?->watch_time ?? 0,
                    'last_position' => $completion?->last_position ?? 0,
                    'completed_at' => $completion?->completed_at,
                    'bookmarks_count' => $completion ? count($completion->bookmarks ?? []) : 0
                ];
            }

            $lectureProgress[] = $sectionData;
        }

        // Quiz progress
        $quizProgress = [];
        foreach ($course->quizzes as $quiz) {
            $attempts = QuizAttempt::where('user_id', $user->id)
                ->where('quiz_id', $quiz->id)
                ->orderByDesc('created_at')
                ->get();

            $bestAttempt = $attempts->where('status', 'completed')->sortByDesc('percentage_score')->first();

            $quizProgress[] = [
                'quiz' => $quiz,
                'attempts_count' => $attempts->count(),
                'best_score' => $bestAttempt?->percentage_score,
                'passed' => $bestAttempt?->is_passed ?? false,
                'last_attempt' => $attempts->first()?->created_at
            ];
        }

        // Homework progress
        $homeworkProgress = [];
        foreach ($course->homework as $homework) {
            $submission = HomeworkSubmission::where('user_id', $user->id)
                ->where('homework_id', $homework->id)
                ->first();

            $homeworkProgress[] = [
                'homework' => $homework,
                'is_submitted' => (bool) $submission,
                'submission_status' => $submission?->status,
                'score' => $submission?->score_earned,
                'submitted_at' => $submission?->submitted_at,
                'is_late' => $submission && $submission->submitted_at > $homework->due_date
            ];
        }

        // Live classes progress
        $liveClassProgress = [];
        foreach ($course->liveClasses as $liveClass) {
            $registration = LiveClassRegistration::where('user_id', $user->id)
                ->where('live_class_id', $liveClass->id)
                ->first();

            $liveClassProgress[] = [
                'live_class' => $liveClass,
                'is_registered' => (bool) $registration,
                'status' => $registration?->status,
                'attended' => $registration?->status === 'attended',
                'joined_at' => $registration?->joined_at
            ];
        }

        // Overall course progress calculation
        $totalLectures = $course->sections->sum(function($section) {
            return $section->lectures->count();
        });

        $completedLectures = LectureCompletion::where('user_id', $user->id)
            ->whereIn('lecture_id',
                $course->sections->flatMap(function($section) {
                    return $section->lectures->pluck('id');
                })
            )
            ->where('is_completed', true)
            ->count();

        $overallProgress = $totalLectures > 0 ? ($completedLectures / $totalLectures) * 100 : 0;

        return response()->json([
            'course' => $course,
            'overall_progress' => round($overallProgress, 2),
            'lecture_progress' => $lectureProgress,
            'quiz_progress' => $quizProgress,
            'homework_progress' => $homeworkProgress,
            'live_class_progress' => $liveClassProgress,
            'stats' => [
                'total_lectures' => $totalLectures,
                'completed_lectures' => $completedLectures,
                'total_quizzes' => $course->quizzes->count(),
                'passed_quizzes' => collect($quizProgress)->where('passed', true)->count(),
                'total_homework' => $course->homework->count(),
                'submitted_homework' => collect($homeworkProgress)->where('is_submitted', true)->count(),
                'total_live_classes' => $course->liveClasses->count(),
                'attended_live_classes' => collect($liveClassProgress)->where('attended', true)->count()
            ]
        ]);
    }

    /**
     * Complete multiple lectures for a course
     */
    public function completeLectures(Request $request, Course $course)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if user is enrolled in the course
        $enrollment = CourseEnrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
        if (!$enrollment) {
            return response()->json(['error' => 'Not enrolled in this course'], 403);
        }

        $request->validate([
            'lecture_ids' => 'required|array',
            'lecture_ids.*' => 'integer|exists:course_lectures,id'
        ]);

        $lectureIds = $request->lecture_ids;
        $completedCount = 0;

        foreach ($lectureIds as $lectureId) {
            // Check if lecture belongs to this course
            $lecture = $course->sections->flatMap(function($section) {
                return $section->lectures;
            })->where('id', $lectureId)->first();

            if ($lecture) {
                // Mark lecture as completed
                $completion = LectureCompletion::firstOrCreate([
                    'user_id' => $user->id,
                    'lecture_id' => $lectureId
                ], [
                    'course_id' => $course->id,
                    'is_completed' => true,
                    'completed_at' => now(),
                    'progress_percentage' => 100,
                    'watch_time_seconds' => 0
                ]);

                if (!$completion->wasRecentlyCreated) {
                    $completion->update([
                        'is_completed' => true,
                        'completed_at' => now(),
                        'progress_percentage' => 100,
                        'course_id' => $course->id
                    ]);
                }

                $completedCount++;
            }
        }

        // Update course enrollment progress
        $totalLectures = $course->sections->sum(function($section) {
            return $section->lectures->count();
        });

        $completedLectures = LectureCompletion::where('user_id', $user->id)
            ->whereIn('lecture_id',
                $course->sections->flatMap(function($section) {
                    return $section->lectures->pluck('id');
                })
            )
            ->where('is_completed', true)
            ->count();

        $progress = $totalLectures > 0 ? round(($completedLectures / $totalLectures) * 100) : 0;

        $enrollment->update([
            'progress_percentage' => $progress,
            'lessons_completed' => $completedLectures,
            'total_lessons' => $totalLectures,
            'last_accessed_at' => now()
        ]);

        // Check if course is completed
        $courseCompleted = false;
        if ($progress >= 100) {
            $courseCompleted = true;
            $enrollment->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        }

        $response = [
            'success' => true,
            'message' => "{$completedCount} lecture(s) marked as complete",
            'progress' => $progress,
            'completed_lectures' => $completedLectures,
            'total_lectures' => $totalLectures,
            'course_completed' => $courseCompleted,
        ];
        
        // Add certificate info if course is completed and certificate is enabled
        if ($courseCompleted && $course->enable_certificate) {
            $response['certificate_available'] = true;
            $response['certificate_request_url'] = route('certificate.request', $course->id);
            if ($enrollment->certificate_path) {
                $response['certificate_download_url'] = route('certificate.download', $enrollment->id);
            }
        }

        return response()->json($response);
    }

    /**
     * Mark multiple lectures as incomplete for a course
     */
    public function incompleteLectures(Request $request, Course $course)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if user is enrolled in the course
        $enrollment = CourseEnrollment::where('user_id', $user->id)->where('course_id', $course->id)->first();
        if (!$enrollment) {
            return response()->json(['error' => 'Not enrolled in this course'], 403);
        }

        $request->validate([
            'lecture_ids' => 'required|array',
            'lecture_ids.*' => 'integer|exists:course_lectures,id'
        ]);

        $lectureIds = $request->lecture_ids;
        $incompletedCount = 0;

        foreach ($lectureIds as $lectureId) {
            // Check if lecture belongs to this course
            $lecture = $course->sections->flatMap(function($section) {
                return $section->lectures;
            })->where('id', $lectureId)->first();

            if ($lecture) {
                // Mark lecture as incomplete
                $completion = LectureCompletion::where('user_id', $user->id)
                    ->where('lecture_id', $lectureId)
                    ->first();

                if ($completion) {
                    $completion->update([
                        'is_completed' => false,
                        'completed_at' => null,
                        'progress_percentage' => 0
                    ]);
                    $incompletedCount++;
                }
            }
        }

        // Update course enrollment progress
        $totalLectures = $course->sections->sum(function($section) {
            return $section->lectures->count();
        });

        $completedLectures = LectureCompletion::where('user_id', $user->id)
            ->whereIn('lecture_id',
                $course->sections->flatMap(function($section) {
                    return $section->lectures->pluck('id');
                })
            )
            ->where('is_completed', true)
            ->count();

        $progress = $totalLectures > 0 ? round(($completedLectures / $totalLectures) * 100) : 0;

        $enrollment->update([
            'progress_percentage' => $progress,
            'lessons_completed' => $completedLectures,
            'total_lessons' => $totalLectures,
            'last_accessed_at' => now()
        ]);

        // Update course status if no longer completed
        if ($progress < 100) {
            $enrollment->update([
                'status' => 'enrolled',
                'completed_at' => null
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "{$incompletedCount} lecture(s) marked as incomplete",
            'progress' => $progress,
            'completed_lectures' => $completedLectures,
            'total_lectures' => $totalLectures
        ]);
    }

    /**
     * Get learning activity timeline
     */
    public function activityTimeline(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);

        $activities = collect();

        // Lecture completions
        $lectureCompletions = LectureCompletion::where('user_id', $user->id)
            ->where('completed_at', '>=', $startDate)
            ->with(['lecture.section.course'])
            ->get()
            ->map(function($completion) {
                return [
                    'type' => 'lecture_completed',
                    'title' => 'Completed: ' . $completion->lecture->title,
                    'course' => $completion->lecture->section->course->name,
                    'date' => $completion->completed_at,
                    'details' => [
                        'lecture_id' => $completion->lecture->id,
                        'watch_time' => $completion->watch_time
                    ]
                ];
            });

        // Quiz attempts
        $quizAttempts = QuizAttempt::where('user_id', $user->id)
            ->where('completed_at', '>=', $startDate)
            ->where('status', 'completed')
            ->with(['quiz.course'])
            ->get()
            ->map(function($attempt) {
                return [
                    'type' => 'quiz_completed',
                    'title' => 'Quiz: ' . $attempt->quiz->name,
                    'course' => $attempt->quiz->course->name,
                    'date' => $attempt->completed_at,
                    'details' => [
                        'score' => $attempt->percentage_score,
                        'passed' => $attempt->is_passed,
                        'quiz_id' => $attempt->quiz->id
                    ]
                ];
            });

        // Homework submissions
        $homeworkSubmissions = HomeworkSubmission::where('user_id', $user->id)
            ->where('submitted_at', '>=', $startDate)
            ->with(['homework.course'])
            ->get()
            ->map(function($submission) {
                return [
                    'type' => 'homework_submitted',
                    'title' => 'Submitted: ' . $submission->homework->title,
                    'course' => $submission->homework->course->name,
                    'date' => $submission->submitted_at,
                    'details' => [
                        'homework_id' => $submission->homework->id,
                        'status' => $submission->status,
                        'score' => $submission->score
                    ]
                ];
            });

        // Live class attendance
        $liveClassAttendance = LiveClassRegistration::where('user_id', $user->id)
            ->where('joined_at', '>=', $startDate)
            ->where('status', 'attended')
            ->with(['liveClass.course'])
            ->get()
            ->map(function($registration) {
                return [
                    'type' => 'live_class_attended',
                    'title' => 'Attended: ' . $registration->liveClass->localized_name,
                    'course' => $registration->liveClass->course->name,
                    'date' => $registration->joined_at,
                    'details' => [
                        'live_class_id' => $registration->liveClass->id
                    ]
                ];
            });

        // Merge and sort all activities
        $activities = $activities
            ->concat($lectureCompletions)
            ->concat($quizAttempts)
            ->concat($homeworkSubmissions)
            ->concat($liveClassAttendance)
            ->sortByDesc('date')
            ->values();

        return response()->json($activities);
    }

    /**
     * Get learning streaks and achievements
     */
    public function achievements()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Calculate learning streak
        $streak = $this->calculateLearningStreak($user);

        // Get achievements
        $achievements = [
            'learning_streak' => $streak,
            'total_lectures_completed' => LectureCompletion::where('user_id', $user->id)
                ->where('is_completed', true)->count(),
            'total_quizzes_passed' => QuizAttempt::where('user_id', $user->id)
                ->where('status', 'completed')
                ->where('passed', true)
                ->distinct('quiz_id')
                ->count(),
            'total_homework_submitted' => HomeworkSubmission::where('user_id', $user->id)->count(),
            'perfect_quiz_scores' => QuizAttempt::where('user_id', $user->id)
                ->where('status', 'completed')
                ->where('score', 100)
                ->count(),
            'courses_completed' => $this->getCompletedCoursesCount($user),
            'live_classes_attended' => LiveClassRegistration::where('user_id', $user->id)
                ->where('status', 'attended')->count()
        ];

        // Badge calculations
        $badges = $this->calculateBadges($achievements);

        return response()->json([
            'achievements' => $achievements,
            'badges' => $badges
        ]);
    }

    /**
     * Calculate learning streak (consecutive days with activity)
     */
    private function calculateLearningStreak($user)
    {
        $activities = collect();

        // Get all activity dates
        $lectureCompletions = LectureCompletion::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->pluck('completed_at')
            ->map(function($date) { return $date->format('Y-m-d'); });

        $quizAttempts = QuizAttempt::where('user_id', $user->id)
            ->whereNotNull('completed_at')
            ->pluck('completed_at')
            ->map(function($date) { return $date->format('Y-m-d'); });

        $homeworkSubmissions = HomeworkSubmission::where('user_id', $user->id)
            ->pluck('submitted_at')
            ->map(function($date) { return $date->format('Y-m-d'); });

        $activities = $activities
            ->concat($lectureCompletions)
            ->concat($quizAttempts)
            ->concat($homeworkSubmissions)
            ->unique()
            ->sort()
            ->values();

        if ($activities->isEmpty()) {
            return 0;
        }

        // Calculate consecutive days from today backwards
        $today = Carbon::today();
        $streak = 0;
        $currentDate = $today;

        while ($activities->contains($currentDate->format('Y-m-d'))) {
            $streak++;
            $currentDate = $currentDate->subDay();
        }

        return $streak;
    }

    /**
     * Get count of completed courses
     */
    private function getCompletedCoursesCount($user)
    {
        $courseIds = LectureCompletion::where('user_id', $user->id)
            ->with('lecture.section.course')
            ->get()
            ->pluck('lecture.section.course.id')
            ->unique();

        $completedCourses = 0;

        foreach ($courseIds as $courseId) {
            $course = Course::with(['sections.lectures'])->find($courseId);
            if (!$course) continue;

            $totalLectures = $course->sections->sum(function($section) {
                return $section->lectures->count();
            });

            $completedLectures = LectureCompletion::where('user_id', $user->id)
                ->whereIn('lecture_id',
                    $course->sections->flatMap(function($section) {
                        return $section->lectures->pluck('id');
                    })
                )
                ->where('is_completed', true)
                ->count();

            if ($totalLectures > 0 && $completedLectures >= $totalLectures) {
                $completedCourses++;
            }
        }

        return $completedCourses;
    }

    /**
     * Calculate badges based on achievements
     */
    private function calculateBadges($achievements)
    {
        $badges = [];

        // Learning streak badges
        if ($achievements['learning_streak'] >= 7) {
            $badges[] = ['name' => 'Week Warrior', 'description' => '7-day learning streak'];
        }
        if ($achievements['learning_streak'] >= 30) {
            $badges[] = ['name' => 'Month Master', 'description' => '30-day learning streak'];
        }

        // Lecture completion badges
        if ($achievements['total_lectures_completed'] >= 10) {
            $badges[] = ['name' => 'Lecture Lover', 'description' => 'Completed 10 lectures'];
        }
        if ($achievements['total_lectures_completed'] >= 100) {
            $badges[] = ['name' => 'Knowledge Seeker', 'description' => 'Completed 100 lectures'];
        }

        // Quiz mastery badges
        if ($achievements['perfect_quiz_scores'] >= 5) {
            $badges[] = ['name' => 'Quiz Master', 'description' => '5 perfect quiz scores'];
        }

        // Course completion badges
        if ($achievements['courses_completed'] >= 1) {
            $badges[] = ['name' => 'Course Crusher', 'description' => 'Completed first course'];
        }
        if ($achievements['courses_completed'] >= 5) {
            $badges[] = ['name' => 'Learning Champion', 'description' => 'Completed 5 courses'];
        }

        return $badges;
    }
}
