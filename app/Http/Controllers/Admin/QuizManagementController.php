<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizAttempt;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class QuizManagementController extends Controller
{
    /**
     * Display quiz management dashboard
     */
    public function index(Request $request)
    {
        $query = Quiz::with(['course', 'questions', 'attempts']);

        // Filter by instructor permissions
        $admin = auth('admin')->user();
        if ($admin->hasPermission('manage_own_quizzes') && !$admin->hasPermission('manage_quizzes')) {
            // Instructor can only see their own quizzes
            $query->where('instructor_id', $admin->id);
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('course', function($courseQuery) use ($search) {
                      $courseQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $quizzes = $query->orderByDesc('created_at')->paginate(15);

        // Calculate stats based on permissions
        $admin = auth('admin')->user();
        if ($admin->hasPermission('manage_own_quizzes') && !$admin->hasPermission('manage_quizzes')) {
            // Instructor stats - only their own quizzes
            $stats = [
                'total_quizzes' => Quiz::where('instructor_id', $admin->id)->count(),
                'active_quizzes' => Quiz::where('instructor_id', $admin->id)->where('is_published', true)->count(),
                'total_questions' => QuizQuestion::whereHas('quiz', function($q) use ($admin) {
                    $q->where('instructor_id', $admin->id);
                })->count(),
                'total_attempts' => QuizAttempt::whereHas('quiz', function($q) use ($admin) {
                    $q->where('instructor_id', $admin->id);
                })->count(),
            ];
        } else {
            // Admin stats - all quizzes
            $stats = [
                'total_quizzes' => Quiz::count(),
                'active_quizzes' => Quiz::where('is_published', true)->count(),
                'total_questions' => QuizQuestion::count(),
                'total_attempts' => QuizAttempt::count(),
            ];
        }

        // Filter courses based on permissions
        if ($admin->hasPermission('manage_own_courses') && !$admin->hasPermission('manage_courses')) {
            // Instructor can only see their own courses
            $courses = Course::where('instructor_id', $admin->id)->get();
        } else {
            // Admin can see all courses
            $courses = Course::all();
        }

        return view('admin.quizzes.index', compact('quizzes', 'stats', 'courses'));
    }

    /**
     * Show quiz creation form
     */
    public function create()
    {
        $admin = auth('admin')->user();

        // Filter courses based on permissions
        if ($admin->hasPermission('manage_own_courses') && !$admin->hasPermission('manage_courses')) {
            // Instructor can only create quizzes for their own courses
            $courses = Course::published()->where('instructor_id', $admin->id)->get();
        } else {
            // Admin can create quizzes for all courses
            $courses = Course::published()->get();
        }

        return view('admin.quizzes.create', compact('courses'));
    }

    /**
     * Get sections for a course (AJAX)
     */
    public function getSections(Course $course)
    {
        $sections = $course->sections()->orderBy('order')->get(['id', 'title']);
        return response()->json($sections);
    }

    /**
     * Get lectures for a course (AJAX)
     */
    public function getLectures(Course $course)
    {
        $lectures = $course->lectures()
            ->with('section:id,title')
            ->orderBy('order')
            ->get(['id', 'title', 'section_id'])
            ->map(function ($lecture) {
                return [
                    'id' => $lecture->id,
                    'title' => $lecture->title,
                    'section_title' => $lecture->section->title ?? ''
                ];
            });

        return response()->json($lectures);
    }

    /**
     * Store new quiz
     */
    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'instructions' => 'nullable|string',
            'instructions_ar' => 'nullable|string',
            'course_id' => 'required|exists:courses,id',
            'connection_type' => 'required|in:course,section,lecture',
            'time_limit_minutes' => 'nullable|integer|min:1',
            'passing_score' => 'required|numeric|min:0|max:100',
            'available_from' => 'nullable|date',
            'available_until' => 'nullable|date|after:available_from',
            'is_published' => 'boolean',
            'is_randomized' => 'boolean',
            'show_results_immediately' => 'boolean',
        ];

        // Add conditional validation based on connection type
        if ($request->connection_type === 'section') {
            $validationRules['section_id'] = 'required|exists:course_sections,id';
        } elseif ($request->connection_type === 'lecture') {
            $validationRules['lecture_id'] = 'required|exists:course_lectures,id';
        }

        $request->validate($validationRules);

        // Debug logging
        \Log::info('Quiz Create Request Data:', [
            'connection_type' => $request->connection_type,
            'section_id' => $request->section_id,
            'lecture_id' => $request->lecture_id,
        ]);

        $data = [
            'name' => $request->name,
            'name_ar' => $request->name_ar,
            'description' => $request->description,
            'description_ar' => $request->description_ar,
            'instructions' => $request->instructions,
            'instructions_ar' => $request->instructions_ar,
            'course_id' => $request->course_id,
            'instructor_id' => auth('admin')->id(),
            'time_limit_minutes' => $request->time_limit_minutes,
            'passing_score' => $request->passing_score,
            'available_from' => $request->available_from,
            'available_until' => $request->available_until,
            'is_published' => $request->input('is_published') === '1',
            'is_randomized' => $request->has('is_randomized'),
            'show_results_immediately' => $request->has('show_results_immediately'),
        ];

        // Handle connection type
        if ($request->connection_type === 'course') {
            $data['section_id'] = null;
            $data['lecture_id'] = null;
        } elseif ($request->connection_type === 'section') {
            $data['section_id'] = $request->section_id;
            $data['lecture_id'] = null;
        } elseif ($request->connection_type === 'lecture') {
            $data['section_id'] = null;
            $data['lecture_id'] = $request->lecture_id;
        }

        \Log::info('Quiz Data to Save:', $data);

        $quiz = Quiz::create($data);

        // Handle questions data if provided
        if ($request->has('questions_data')) {
            $questionsData = json_decode($request->questions_data, true);

            if (is_array($questionsData)) {
                foreach ($questionsData as $questionData) {
                    $question = new QuizQuestion([
                        'quiz_id' => $quiz->id,
                        'question_text' => $questionData['text'],
                        'question_text_ar' => $questionData['text_ar'] ?? null,
                        'question_type' => $questionData['type'],
                        'points' => $questionData['points'],
                        'order' => $questionData['order'],
                        'explanation' => $questionData['explanation'] ?? null,
                        'explanation_ar' => $questionData['explanation_ar'] ?? null,
                    ]);

                    // Handle different question types
                    switch ($questionData['type']) {
                        case 'multiple_choice':
                            $question->options = $questionData['options'] ?? [];
                            $question->options_ar = $questionData['options_ar'] ?? [];
                            $question->correct_answers = $questionData['correctAnswers'] ?? [];
                            break;
                        case 'true_false':
                            $question->correct_answer_boolean = $questionData['correctAnswerBoolean'] ?? null;
                            break;
                        case 'fill_blank':
                            $question->correct_answers_text = $questionData['correctAnswersText'] ?? [];
                            $question->correct_answers_text_ar = $questionData['correctAnswersTextAr'] ?? [];
                            break;
                        case 'essay':
                            $question->sample_answer = $questionData['sampleAnswer'] ?? null;
                            $question->sample_answer_ar = $questionData['sampleAnswerAr'] ?? null;
                            break;
                    }

                    $question->save();
                }
            }
        }

        return redirect()->route('admin.quizzes.show', $quiz)
            ->with('success', 'Quiz created successfully with ' . ($quiz->questions->count() ?? 0) . ' questions');
    }

    /**
     * Display quiz details and questions
     */
    public function show(Quiz $quiz)
    {
        $quiz->load(['course', 'questions' => function($query) {
            $query->orderBy('order');
        }, 'attempts.user']);

        $stats = [
            'total_questions' => $quiz->questions->count(),
            'total_attempts' => $quiz->attempts->count(),
            'completed_attempts' => $quiz->attempts->where('status', 'completed')->count(),
            'average_score' => $quiz->attempts->where('status', 'completed')->avg('percentage_score') ?? 0,
            'pass_rate' => $quiz->attempts->where('status', 'completed')->where('is_passed', true)->count() / max($quiz->attempts->where('status', 'completed')->count(), 1) * 100,
        ];

        return view('admin.quizzes.show', compact('quiz', 'stats'));
    }

    /**
     * Show quiz editing form
     */
    public function edit(Quiz $quiz)
    {
        $courses = Course::published()->get();

        return view('admin.quizzes.edit', compact('quiz', 'courses'));
    }

    /**
     * Update quiz
     */
        public function update(Request $request, Quiz $quiz)
    {
        try {
            $validationRules = [
                'name' => 'required|string|max:255',
                'name_ar' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'instructions' => 'nullable|string',
                'instructions_ar' => 'nullable|string',
                'course_id' => 'required|exists:courses,id',
                'connection_type' => 'required|in:course,section,lecture',
                'time_limit_minutes' => 'nullable|integer|min:1',
                'passing_score' => 'required|numeric|min:0|max:100',
                'available_from' => 'nullable|date',
                'available_until' => 'nullable|date|after:available_from',
                'is_published' => 'nullable|in:0,1',
                'is_randomized' => 'boolean',
                'show_results_immediately' => 'boolean',
            ];

            // Add conditional validation based on connection type
            if ($request->connection_type === 'section') {
                $validationRules['section_id'] = 'required|exists:course_sections,id';
            } elseif ($request->connection_type === 'lecture') {
                $validationRules['lecture_id'] = 'required|exists:course_lectures,id';
            }

            $request->validate($validationRules);

            $data = [
                'name' => $request->name,
                'name_ar' => $request->name_ar,
                'description' => $request->description,
                'description_ar' => $request->description_ar,
                'instructions' => $request->instructions,
                'instructions_ar' => $request->instructions_ar,
                'course_id' => $request->course_id,
                'time_limit_minutes' => $request->time_limit_minutes,
                'passing_score' => $request->passing_score,
                'available_from' => $request->available_from,
                'available_until' => $request->available_until,
                'is_published' => $request->has('is_published') && $request->input('is_published') === '1',
                'is_randomized' => $request->has('is_randomized'),
                'show_results_immediately' => $request->has('show_results_immediately'),
            ];

            // Handle connection type
            if ($request->connection_type === 'course') {
                $data['section_id'] = null;
                $data['lecture_id'] = null;
            } elseif ($request->connection_type === 'section') {
                $data['section_id'] = $request->section_id;
                $data['lecture_id'] = null;
            } elseif ($request->connection_type === 'lecture') {
                $data['section_id'] = null;
                $data['lecture_id'] = $request->lecture_id;
            }

            $quiz->update($data);

            return redirect()->route('admin.quizzes.show', $quiz)
                ->with('success', 'Quiz updated successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating quiz: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete quiz
     */
    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', 'Quiz deleted successfully');
    }

    /**
     * Add question to quiz
     */
    public function storeQuestion(Request $request, Quiz $quiz)
    {
        try {
            $request->validate([
                'question_type' => 'required|in:multiple_choice,true_false,fill_blank,essay',
                'question_text' => 'required|string',
                'points' => 'required|integer|min:1',
                'order' => 'nullable|integer|min:1',
                'explanation' => 'nullable|string',
                'options' => 'required_if:question_type,multiple_choice|array|min:2',
                'correct_answer' => 'required_if:question_type,multiple_choice|integer',
                'correct_answer_boolean' => 'required_if:question_type,true_false|boolean',
                'correct_answers_text' => 'required_if:question_type,fill_blank|array|min:1',
            ]);

            $questionData = [
                'quiz_id' => $quiz->id,
                'question_text' => $request->question_text,
                'question_type' => $request->question_type,
                'points' => $request->points,
                'order' => $request->order ?? ($quiz->questions()->max('order') + 1),
                'explanation' => $request->explanation,
            ];

            // Handle different question types
            switch ($request->question_type) {
                case 'multiple_choice':
                    $questionData['options'] = $request->options;
                    $questionData['correct_answers'] = [$request->correct_answer];
                    break;

                case 'true_false':
                    $questionData['correct_answer_boolean'] = $request->correct_answer_boolean;
                    break;

                case 'fill_blank':
                    $questionData['correct_answers_text'] = $request->correct_answers_text;
                    break;

                case 'essay':
                    // Essay questions don't need additional data
                    break;
            }

            $question = QuizQuestion::create($questionData);

            return response()->json([
                'success' => true,
                'message' => 'Question added successfully',
                'question' => $question->load('quiz')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show question for editing
     */
    public function showQuestion(QuizQuestion $question)
    {
        try {
            return response()->json([
                'success' => true,
                'question' => $question
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update question
     */
    public function updateQuestion(Request $request, QuizQuestion $question)
    {
        try {
            $request->validate([
                'question_type' => 'required|in:multiple_choice,true_false,fill_blank,essay',
                'question_text' => 'required|string',
                'points' => 'required|integer|min:1',
                'order' => 'nullable|integer|min:1',
                'explanation' => 'nullable|string',
                'options' => 'required_if:question_type,multiple_choice|array|min:2',
                'correct_answer' => 'required_if:question_type,multiple_choice|integer',
                'correct_answer_boolean' => 'required_if:question_type,true_false|boolean',
                'correct_answers_text' => 'required_if:question_type,fill_blank|array|min:1',
            ]);

            $questionData = [
                'question_text' => $request->question_text,
                'question_type' => $request->question_type,
                'points' => $request->points,
                'order' => $request->order ?? $question->order,
                'explanation' => $request->explanation,
            ];

            // Handle different question types
            switch ($request->question_type) {
                case 'multiple_choice':
                    $questionData['options'] = $request->options;
                    $questionData['correct_answers'] = [$request->correct_answer];
                    break;

                case 'true_false':
                    $questionData['correct_answer_boolean'] = $request->correct_answer_boolean;
                    break;

                case 'fill_blank':
                    $questionData['correct_answers_text'] = $request->correct_answers_text;
                    break;

                case 'essay':
                    // Essay questions don't need additional data
                    break;
            }

            $question->update($questionData);

            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully',
                'question' => $question->fresh()->load('quiz')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating question: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete question
     */
    public function destroyQuestion(QuizQuestion $question)
    {
        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully'
        ]);
    }

    /**
     * View quiz attempts and results
     */
    public function attempts(Quiz $quiz)
    {
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'total_attempts' => $attempts->count(),
            'completed_attempts' => $attempts->where('status', 'completed')->count(),
            'average_score' => $attempts->where('status', 'completed')->avg('percentage_score') ?? 0,
            'pass_rate' => $attempts->where('status', 'completed')->where('is_passed', true)->count() / max($attempts->where('status', 'completed')->count(), 1) * 100,
        ];

        return view('admin.quizzes.attempts', compact('quiz', 'attempts', 'stats'));
    }

    /**
     * View individual attempt details
     */
    public function viewAttempt(QuizAttempt $attempt)
    {
        $attempt->load(['quiz.questions', 'user']);

        // Prepare detailed results
        $results = [];
        $userAnswers = $attempt->answers ?? [];

        foreach ($attempt->quiz->questions as $question) {
            $userAnswer = $userAnswers[$question->id]['answer'] ?? 'No answer';

            $isCorrect = false;
            $correctAnswers = [];

            if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
                $correctOptions = collect($question->options)->where('is_correct', true);
                $correctAnswers = $correctOptions->pluck('text')->toArray();
                $isCorrect = $correctOptions->pluck('text')->contains($userAnswer);
            } elseif ($question->type === 'fill_blank') {
                $correctAnswers = collect($question->options)->pluck('text')->toArray();
                $isCorrect = collect($correctAnswers)->contains(function($answer) use ($userAnswer) {
                    return strtolower(trim($answer)) === strtolower(trim($userAnswer));
                });
            } elseif ($question->type === 'essay') {
                $correctAnswers = ['Manual grading required'];
                $isCorrect = null; // Requires manual review
            }

            $results[] = [
                'question' => $question,
                'user_answer' => $userAnswer,
                'correct_answers' => $correctAnswers,
                'is_correct' => $isCorrect,
                'points_earned' => $isCorrect ? $question->points : 0
            ];
        }

        return view('admin.quizzes.attempt-details', compact('attempt', 'results'));
    }

    /**
     * Export quiz results to CSV
     */
    public function exportResults(Quiz $quiz)
    {
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('status', 'completed')
            ->with(['user'])
            ->get();

        $filename = 'quiz_' . $quiz->id . '_results_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attempts) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Student Name',
                'Email',
                'Score (%)',
                'Points Earned',
                'Total Points',
                'Correct Answers',
                'Total Questions',
                'Status',
                'Started At',
                'Completed At',
                'Duration (minutes)'
            ]);

            foreach ($attempts as $attempt) {
                $duration = $attempt->started_at && $attempt->completed_at
                    ? $attempt->started_at->diffInMinutes($attempt->completed_at)
                    : 0;

                fputcsv($file, [
                    $attempt->user->name,
                    $attempt->user->email,
                    round($attempt->percentage_score, 2),
                    $attempt->score_earned,
                    $attempt->total_possible_score,
                    $attempt->getCorrectAnswersCount(),
                    $attempt->quiz->questions()->count(),
                    $attempt->is_passed ? 'Passed' : 'Failed',
                    $attempt->started_at?->format('Y-m-d H:i:s'),
                    $attempt->completed_at?->format('Y-m-d H:i:s'),
                    $duration
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get quiz statistics
     */
    public function statistics(Quiz $quiz)
    {
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)->where('status', 'completed');

        $stats = [
            'total_attempts' => $attempts->count(),
            'average_score' => $attempts->avg('percentage_score'),
            'pass_rate' => $attempts->where('is_passed', true)->count() / max($attempts->count(), 1) * 100,
            'score_distribution' => [
                '90-100' => $attempts->whereBetween('percentage_score', [90, 100])->count(),
                '80-89' => $attempts->whereBetween('percentage_score', [80, 89])->count(),
                '70-79' => $attempts->whereBetween('percentage_score', [70, 79])->count(),
                '60-69' => $attempts->whereBetween('percentage_score', [60, 69])->count(),
                'below-60' => $attempts->where('percentage_score', '<', 60)->count(),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Display quiz analytics
     */
    public function analytics(Quiz $quiz)
    {
        $attempts = QuizAttempt::where('quiz_id', $quiz->id)->where('status', 'completed');
        $allAttempts = QuizAttempt::where('quiz_id', $quiz->id);

        // Basic stats
        $analytics = [
            'total_attempts' => $allAttempts->count(),
            'completed_attempts' => $attempts->count(),
            'completion_rate' => $allAttempts->count() > 0 ? ($attempts->count() / $allAttempts->count()) * 100 : 0,
            'average_score' => $attempts->avg('percentage_score') ?? 0,
            'pass_rate' => $attempts->count() > 0 ? ($attempts->where('is_passed', true)->count() / $attempts->count()) * 100 : 0,
        ];

        // Time analysis
        $completedAttempts = $attempts->get();
        $durations = $completedAttempts->map(function($attempt) {
            if ($attempt->started_at && $attempt->completed_at) {
                return $attempt->started_at->diffInMinutes($attempt->completed_at);
            }
            return 0;
        })->filter(function($duration) {
            return $duration > 0;
        });

        $analytics['average_time_minutes'] = $durations->count() > 0 ? round($durations->avg(), 1) : 0;
        $analytics['fastest_time_minutes'] = $durations->count() > 0 ? $durations->min() : 0;
        $analytics['slowest_time_minutes'] = $durations->count() > 0 ? $durations->max() : 0;
        $analytics['median_time_minutes'] = $durations->count() > 0 ? round($durations->median(), 1) : 0;

        // Score distribution
        $analytics['score_distribution'] = [
            $attempts->whereBetween('percentage_score', [0, 10])->count(),
            $attempts->whereBetween('percentage_score', [11, 20])->count(),
            $attempts->whereBetween('percentage_score', [21, 30])->count(),
            $attempts->whereBetween('percentage_score', [31, 40])->count(),
            $attempts->whereBetween('percentage_score', [41, 50])->count(),
            $attempts->whereBetween('percentage_score', [51, 60])->count(),
            $attempts->whereBetween('percentage_score', [61, 70])->count(),
            $attempts->whereBetween('percentage_score', [71, 80])->count(),
            $attempts->whereBetween('percentage_score', [81, 90])->count(),
            $attempts->whereBetween('percentage_score', [91, 100])->count(),
        ];

        // Attempts over time (last 30 days)
        $timeLabels = [];
        $attemptsOverTime = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $timeLabels[] = $date->format('M d');
            $attemptsOverTime[] = $allAttempts->whereDate('created_at', $date)->count();
        }
        $analytics['time_labels'] = $timeLabels;
        $analytics['attempts_over_time'] = $attemptsOverTime;

        // Question analysis
        $questions = $quiz->questions;
        $questionAnalysis = [];
        foreach ($questions as $question) {
            $questionAttempts = $attempts->get();
            $correctAnswers = 0;
            $incorrectAnswers = 0;
            $skippedAnswers = 0;

            foreach ($questionAttempts as $attempt) {
                $userAnswers = $attempt->answers ?? [];
                $userAnswer = $userAnswers[$question->id]['answer'] ?? null;

                if ($userAnswer === null) {
                    $skippedAnswers++;
                } else {
                    // Simple check for correct answer
                    $isCorrect = false;
                    if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
                        $correctOptions = collect($question->options)->where('is_correct', true);
                        $isCorrect = $correctOptions->pluck('text')->contains($userAnswer);
                    } elseif ($question->question_type === 'fill_blank') {
                        $correctAnswers = collect($question->options)->pluck('text')->toArray();
                        $isCorrect = collect($correctAnswers)->contains(function($answer) use ($userAnswer) {
                            return strtolower(trim($answer)) === strtolower(trim($userAnswer));
                        });
                    }

                    if ($isCorrect) {
                        $correctAnswers++;
                    } else {
                        $incorrectAnswers++;
                    }
                }
            }

            $totalAnswers = $correctAnswers + $incorrectAnswers + $skippedAnswers;
            $successRate = $totalAnswers > 0 ? ($correctAnswers / $totalAnswers) * 100 : 0;

            $questionAnalysis[] = [
                'question_text' => $question->question_text,
                'question_type' => $question->question_type,
                'difficulty' => $question->difficulty ?? 'medium',
                'correct_answers' => $correctAnswers,
                'incorrect_answers' => $incorrectAnswers,
                'skipped_answers' => $skippedAnswers,
                'success_rate' => round($successRate, 1),
            ];
        }
        $analytics['question_analysis'] = $questionAnalysis;

        // Top performers
        $topPerformers = $attempts->with('user')
            ->orderByDesc('percentage_score')
            ->limit(5)
            ->get()
            ->map(function($attempt) {
                return [
                    'name' => $attempt->user->name,
                    'email' => $attempt->user->email,
                    'score' => $attempt->percentage_score,
                    'time_taken' => $attempt->started_at && $attempt->completed_at
                        ? $attempt->started_at->diffInMinutes($attempt->completed_at) . ' min'
                        : 'N/A',
                ];
            });
        $analytics['top_performers'] = $topPerformers;

        // Recent activity
        $recentActivity = $allAttempts->with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function($attempt) {
                return [
                    'student_name' => $attempt->user->name,
                    'action' => $attempt->status === 'completed' ? 'Completed quiz' : 'Started quiz',
                    'time_ago' => $attempt->created_at->diffForHumans(),
                ];
            });
        $analytics['recent_activity'] = $recentActivity;

        // Attempt status breakdown
        $analytics['in_progress_attempts'] = $allAttempts->where('status', 'in_progress')->count();
        $analytics['abandoned_attempts'] = $allAttempts->where('status', 'abandoned')->count();

        return view('admin.quizzes.analytics', compact('quiz', 'analytics'));
    }

    /**
     * Duplicate a quiz
     */
    public function duplicate(Quiz $quiz)
    {
        $newQuiz = $quiz->replicate();
        $newQuiz->name = $quiz->name . ' (Copy)';
        $newQuiz->name_ar = $quiz->name_ar ? $quiz->name_ar . ' (نسخة)' : null;
        $newQuiz->is_published = false;
        $newQuiz->created_at = now();
        $newQuiz->updated_at = now();
        $newQuiz->save();

        // Duplicate questions
        foreach ($quiz->questions as $question) {
            $newQuestion = $question->replicate();
            $newQuestion->quiz_id = $newQuiz->id;
            $newQuestion->save();
        }

        return redirect()->route('admin.quizzes.edit', $newQuiz)
            ->with('success', 'Quiz duplicated successfully');
    }

    /**
     * Export quiz results
     */
    public function export(Quiz $quiz)
    {
        return $this->exportResults($quiz);
    }

    /**
     * Bulk delete quizzes
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'quiz_ids' => 'required|array|min:1',
            'quiz_ids.*' => 'exists:quizzes,id'
        ]);

        $deletedCount = Quiz::whereIn('id', $request->quiz_ids)->delete();

        return redirect()->route('admin.quizzes.index')
            ->with('success', "Successfully deleted {$deletedCount} quiz(zes)");
    }

    /**
     * Bulk update quiz status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'quiz_ids' => 'required|array|min:1',
            'quiz_ids.*' => 'exists:quizzes,id',
            'status' => 'required|in:published,draft'
        ]);

        $status = $request->status === 'published';
        $updatedCount = Quiz::whereIn('id', $request->quiz_ids)->update(['is_published' => $status]);

        return redirect()->route('admin.quizzes.index')
            ->with('success', "Successfully updated {$updatedCount} quiz(zes) to " . ucfirst($request->status));
    }

    /**
     * Toggle quiz status
     */
    public function toggleStatus(Quiz $quiz)
    {
        $quiz->update(['is_published' => !$quiz->is_published]);

        return response()->json([
            'success' => true,
            'is_published' => $quiz->is_published,
            'message' => 'Quiz status updated successfully'
        ]);
    }

    /**
     * Import quizzes from Excel file
     */
    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        try {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            $data = array_map('str_getcsv', file($path));
            $headers = array_shift($data); // Remove header row

            $imported = 0;
            $errors = [];

            foreach ($data as $rowIndex => $row) {
                try {
                    if (count($row) !== count($headers)) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Column count mismatch";
                        continue;
                    }

                    $rowData = array_combine($headers, $row);

                    // Validate required fields
                    if (empty($rowData['Name'])) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Quiz name is required";
                        continue;
                    }

                    if (empty($rowData['Course ID'])) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Course ID is required";
                        continue;
                    }

                    // Check if course exists
                    $course = Course::find($rowData['Course ID']);
                    if (!$course) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Course with ID '{$rowData['Course ID']}' not found";
                        continue;
                    }

                    // Create quiz with proper data handling
                    Quiz::create([
                        'name' => trim($rowData['Name']),
                        'description' => trim($rowData['Description'] ?? ''),
                        'course_id' => intval($rowData['Course ID']),
                        'instructor_id' => auth('admin')->id(),
                        'time_limit_minutes' => !empty($rowData['Time Limit (minutes)']) ? intval($rowData['Time Limit (minutes)']) : null,
                        'passing_score' => floatval($rowData['Passing Score'] ?? 70),
                        'is_published' => strtolower($rowData['Status'] ?? 'draft') === 'published',
                        'total_marks' => intval($rowData['Total Marks'] ?? 0),
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Successfully imported {$imported} quizzes.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

        /**
     * Export quizzes list to Excel/CSV
     */
    public function exportList(Request $request)
    {
        try {
            $format = $request->get('format', 'csv');

            $quizzes = Quiz::with(['course', 'questions', 'attempts'])
                ->orderByDesc('created_at')
                ->get();

            $filename = 'quizzes_' . date('Y-m-d_H-i-s') . '.' . $format;

            if ($format === 'csv') {
                $headers = [
                    'Content-Type' => 'text/csv; charset=UTF-8',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Cache-Control' => 'no-cache, must-revalidate',
                    'Pragma' => 'no-cache',
                ];

                $callback = function() use ($quizzes) {
                    $file = fopen('php://output', 'w');

                    // Add BOM for UTF-8
                    fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                    // Add headers for import template
                    fputcsv($file, [
                        'Name',
                        'Description',
                        'Course ID',
                        'Time Limit (minutes)',
                        'Passing Score',
                        'Status',
                        'Total Marks'
                    ]);

                    // Add data
                    foreach ($quizzes as $quiz) {
                        fputcsv($file, [
                            $quiz->name,
                            $quiz->description,
                            $quiz->course_id,
                            $quiz->time_limit_minutes ?? '',
                            $quiz->passing_score,
                            $quiz->is_published ? 'published' : 'draft',
                            $quiz->total_marks
                        ]);
                    }

                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);
            } elseif ($format === 'excel') {
                // For Excel format, we'll create a CSV file with .xlsx extension
                // In a real implementation, you'd use a library like PhpSpreadsheet
                $headers = [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ];

                $callback = function() use ($quizzes) {
                    $file = fopen('php://output', 'w');

                    // Add headers
                    fputcsv($file, [
                        'ID', 'Name', 'Description', 'Course', 'Questions',
                        'Time Limit', 'Passing Score', 'Status', 'Attempts', 'Created At'
                    ]);

                    // Add data
                    foreach ($quizzes as $quiz) {
                        fputcsv($file, [
                            $quiz->id,
                            $quiz->name,
                            $quiz->description,
                            $quiz->course->name ?? 'N/A',
                            $quiz->questions->count(),
                            $quiz->time_limit_minutes ?? 'No limit',
                            $quiz->passing_score . '%',
                            $quiz->is_published ? 'Published' : 'Draft',
                            $quiz->attempts->count(),
                            $quiz->created_at->format('Y-m-d H:i:s'),
                        ]);
                    }

                    fclose($file);
                };

                return response()->stream($callback, 200, $headers);
            }

            return redirect()->back()->with('error', 'Export format not supported');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

    /**
     * Reorder question
     */
    public function reorderQuestion(Request $request, QuizQuestion $question)
    {
        $request->validate([
            'order' => 'required|integer|min:0'
        ]);

        $question->update(['order' => $request->order]);

        return response()->json([
            'success' => true,
            'message' => 'Question reordered successfully'
        ]);
    }

    /**
     * Bulk import questions
     */
    public function bulkImportQuestions(Request $request, Quiz $quiz)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            // Read the file based on extension
            if ($extension === 'csv') {
                $data = array_map('str_getcsv', file($file->getPathname()));
                $headers = array_shift($data);
            } else {
                return redirect()->back()->with('error', 'Excel import not implemented yet. Please use CSV format.');
            }

            $imported = 0;
            $errors = [];

            foreach ($data as $row) {
                try {
                    $rowData = array_combine($headers, $row);

                    // Validate required fields
                    if (empty($rowData['question_text']) || empty($rowData['question_type'])) {
                        $errors[] = "Row " . ($imported + 1) . ": Missing required fields";
                        continue;
                    }

                    // Create question
                    $question = $quiz->questions()->create([
                        'question_text' => $rowData['question_text'],
                        'question_type' => $rowData['question_type'],
                        'options' => isset($rowData['options']) ? json_decode($rowData['options'], true) : [],
                        'correct_answer' => $rowData['correct_answer'] ?? null,
                        'points' => $rowData['points'] ?? 1,
                        'order' => $rowData['order'] ?? $quiz->questions()->count() + 1,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($imported + 1) . ": " . $e->getMessage();
                }
            }

            $message = "Successfully imported {$imported} questions.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $filename = 'quizzes_import_template.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Add headers
            fputcsv($file, [
                'Name',
                'Description',
                'Course ID',
                'Time Limit (minutes)',
                'Passing Score',
                'Status',
                'Total Marks'
            ]);

            // Add sample data
            fputcsv($file, [
                'Sample Quiz',
                'This is a sample quiz description',
                '1',
                '30',
                '70',
                'draft',
                '100'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
