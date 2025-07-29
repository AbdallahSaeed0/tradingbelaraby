<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function show(Quiz $quiz)
    {
        $course = $quiz->course;
        $enrollment = Auth::user()->enrollments()->where('course_id', $course->id)->first();
        if (!$enrollment) {
            return redirect()->route('courses.show', $course)->with('error', 'You must be enrolled in this course to take the quiz.');
        }
        $attempts = $quiz->attempts()->where('user_id', Auth::id())->orderBy('created_at', 'desc')->get();
        $canTakeResult = $this->canTakeQuiz($quiz, $enrollment);
        $canTakeQuiz = $canTakeResult['allowed'];
        return view('quizzes.show', compact('quiz', 'course', 'attempts', 'canTakeQuiz'));
    }

    public function attempts(Quiz $quiz)
    {
        $course = $quiz->course;
        $enrollment = Auth::user()->enrollments()->where('course_id', $course->id)->first();
        if (!$enrollment) {
            return redirect()->route('courses.show', $course)->with('error', 'You must be enrolled in this course to view quiz attempts.');
        }
        $attempts = $quiz->attempts()->where('user_id', Auth::id())->with(['answers'])->orderBy('created_at', 'desc')->paginate(10);
        return view('courses.quiz-attempts', compact('quiz', 'course', 'attempts'));
    }

    public function results(Quiz $quiz, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }
        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }
        $course = $quiz->course;
        $questions = $quiz->questions()->with('options')->get();
        $totalQuestions = $questions->count();

        // Count correct answers from the JSON answers field
        $correctAnswers = 0;
        if ($attempt->answers) {
            foreach ($attempt->answers as $questionId => $answerData) {
                if (isset($answerData['is_correct']) && $answerData['is_correct']) {
                    $correctAnswers++;
                }
            }
        }

        $score = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
        return view('courses.quiz-results', compact('quiz', 'course', 'attempt', 'questions', 'totalQuestions', 'correctAnswers', 'score'));
    }

    public function start(Request $request, Quiz $quiz)
    {
        $course = $quiz->course;
        $enrollment = Auth::user()->enrollments()->where('course_id', $course->id)->first();
        if (!$enrollment) {
            return response()->json(['error' => 'You must be enrolled in this course to take the quiz.'], 403);
        }
        $canTakeResult = $this->canTakeQuiz($quiz, $enrollment);
        if (!$canTakeResult['allowed']) {
            return response()->json(['error' => $canTakeResult['reason']], 403);
        }
        $attempt = QuizAttempt::create([
            'quiz_id' => $quiz->id,
            'user_id' => Auth::id(),
            'started_at' => now(),
            'status' => 'in_progress'
        ]);
        return response()->json([
            'success' => true,
            'attempt_id' => $attempt->id,
            'redirect_url' => route('quizzes.take', ['quiz' => $quiz, 'attempt' => $attempt])
        ]);
    }

    public function take(Quiz $quiz, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403);
        }
        if ($attempt->quiz_id !== $quiz->id) {
            abort(404);
        }
        if ($attempt->status !== 'in_progress') {
            return redirect()->route('quizzes.show', $quiz)->with('error', 'This attempt has already been completed.');
        }

        $course = $quiz->course;
        $questions = $quiz->questions()->get();

        // Get saved answers from JSON field
        $savedAnswers = [];
        if ($attempt->answers) {
            foreach ($attempt->answers as $questionId => $answerData) {
                if (isset($answerData['answer'])) {
                    $savedAnswers[$questionId] = $answerData['answer'];
                }
            }
        }

        return view('quizzes.take', compact('quiz', 'course', 'attempt', 'questions', 'savedAnswers'));
    }

    public function saveAnswer(Request $request, Quiz $quiz)
    {
        $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
            'question_id' => 'required|exists:quiz_questions,id',
            'option_id' => 'required|integer|min:0'
        ]);

        $attempt = QuizAttempt::where('id', $request->attempt_id)
            ->where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->first();

        if (!$attempt || $attempt->status !== 'in_progress') {
            return response()->json(['error' => 'Invalid attempt.'], 400);
        }

        // Get current answers or initialize empty array
        $answers = $attempt->answers ?? [];

        // Save or update the answer in JSON format
        $answers[$request->question_id] = [
            'answer' => $request->option_id,
            'saved_at' => now()->toISOString()
        ];

        // Update the attempt with new answers
        $attempt->update(['answers' => $answers]);

        return response()->json(['success' => true]);
    }

    public function submit(Request $request, Quiz $quiz)
    {
        $request->validate([
            'attempt_id' => 'required|exists:quiz_attempts,id',
            'answers' => 'required|array',
            'answers.*' => 'required|integer|min:0'
        ]);
        $attempt = QuizAttempt::where('id', $request->attempt_id)
            ->where('user_id', Auth::id())
            ->where('quiz_id', $quiz->id)
            ->first();
        if (!$attempt) {
            return response()->json(['error' => 'Invalid attempt.'], 404);
        }
        if ($attempt->status !== 'in_progress') {
            return response()->json(['error' => 'This attempt has already been submitted.'], 400);
        }

        $answers = $request->answers;
        $correctCount = 0;
        $totalQuestions = $quiz->questions()->count();
        $processedAnswers = [];

        foreach ($answers as $questionId => $optionId) {
            $question = QuizQuestion::find($questionId);
            $isCorrect = in_array($optionId, $question->correct_answers ?? []);
            if ($isCorrect) {
                $correctCount++;
            }

            // Store answer in JSON format
            $processedAnswers[$questionId] = [
                'answer' => $optionId,
                'is_correct' => $isCorrect,
                'submitted_at' => now()->toISOString()
            ];
        }

        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;
        $attempt->update([
            'completed_at' => now(),
            'status' => 'completed',
            'score_earned' => $correctCount,
            'total_possible_score' => $totalQuestions,
            'percentage_score' => $score,
            'is_passed' => $score >= $quiz->passing_score,
            'answers' => $processedAnswers
        ]);
        return response()->json([
            'success' => true,
            'redirect_url' => route('quizzes.results', ['quiz' => $quiz, 'attempt' => $attempt])
        ]);
    }

    private function canTakeQuiz(Quiz $quiz, $enrollment)
    {
        if (!$quiz->is_published) {
            return ['allowed' => false, 'reason' => 'Quiz is not published'];
        }

        if ($quiz->required_lectures) {
            $completedLectures = Auth::user()->lectureCompletions()
                ->where('course_id', $quiz->course_id)
                ->where('is_completed', true)
                ->count();
            if ($completedLectures < $quiz->required_lectures) {
                return ['allowed' => false, 'reason' => "You need to complete {$quiz->required_lectures} lectures first. You have completed {$completedLectures}."];
            }
        }

        $attemptCount = $quiz->attempts()->where('user_id', Auth::id())->count();
        if ($quiz->max_attempts && $attemptCount >= $quiz->max_attempts) {
            return ['allowed' => false, 'reason' => "You have reached the maximum number of attempts ({$quiz->max_attempts})"];
        }

        $ongoingAttempt = $quiz->attempts()
            ->where('user_id', Auth::id())
            ->where('status', 'in_progress')
            ->first();
        if ($ongoingAttempt) {
            return ['allowed' => false, 'reason' => 'You have an ongoing attempt. Please complete it first.'];
        }

        return ['allowed' => true, 'reason' => 'OK'];
    }
}
