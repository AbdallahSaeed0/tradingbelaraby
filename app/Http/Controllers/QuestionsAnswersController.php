<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\QuestionsAnswer;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class QuestionsAnswersController extends Controller
{
    /**
     * Display Q&A for a specific course or all
     */
    public function index(Request $request, Course $course = null)
    {
        $query = QuestionsAnswer::approved()->with(['user', 'course']);

        if ($course) {
            $query->where('course_id', $course->id);
        }

        // Category filter
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('question', 'like', '%' . $request->search . '%')
                  ->orWhere('answer', 'like', '%' . $request->search . '%');
            });
        }

        // Sort options
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'votes':
                $query->orderByDesc('votes');
                break;
            case 'views':
                $query->orderByDesc('views');
                break;
            case 'oldest':
                $query->orderBy('created_at');
                break;
            case 'latest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $questions = $query->paginate(10);

        $categories = QuestionsAnswer::distinct('category')
            ->whereNotNull('category')
            ->pluck('category');

        return view('qa.index', compact('questions', 'categories', 'course'));
    }

    /**
     * Display specific question with answers
     */
    public function show(QuestionsAnswer $question)
    {
        if ($question->status !== 'approved') {
            abort(404, 'Question not found');
        }

        $question->load(['user', 'course']);

        // Increment view count
        $question->increment('views');

        // Get related questions
        $relatedQuestions = QuestionsAnswer::approved()
            ->where('course_id', $question->course_id)
            ->where('id', '!=', $question->id)
            ->where(function($query) use ($question) {
                $query->where('category', $question->category)
                      ->orWhere('question', 'like', '%' . substr($question->question, 0, 50) . '%');
            })
            ->limit(5)
            ->get();

        return view('qa.show', compact('question', 'relatedQuestions'));
    }

    /**
     * Show form to ask new question
     */
    public function create(Course $course = null)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to ask questions.');
        }

        $courses = Course::published()->get();
        $categories = [
            'general' => 'General',
            'technical' => 'Technical',
            'assignment' => 'Assignment Help',
            'schedule' => 'Schedule & Deadlines',
            'content' => 'Course Content',
            'other' => 'Other'
        ];

        return view('qa.create', compact('course', 'courses', 'categories'));
    }

    /**
     * Store new question
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $course = Course::find($request->course_id);
        if (!$course || !$course->isEnrolledBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You must be enrolled in this course to ask a question.',
            ], 403);
        }

        try {
            $request->validate([
                'question_title' => 'required|string|max:500',
                'question_content' => 'nullable|string|max:2000',
                'course_id' => 'required|exists:courses,id',
                'question_type' => 'required|in:general,technical,assignment,schedule,content,other',
                'is_anonymous' => 'boolean'
            ], [
                'question_title.required' => 'Question title is required',
                'question_title.max' => 'Question title cannot exceed 500 characters',
                'question_type.required' => 'Please select a question type',
                'question_type.in' => 'Please select a valid question type',
                'course_id.required' => 'Course ID is required',
                'course_id.exists' => 'Invalid course selected'
            ]);

            // Manual validation for minimum length
            if (strlen(trim($request->question_title)) < 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Question title must be at least 3 characters',
                    'errors' => ['question_title' => ['Question title must be at least 3 characters']]
                ], 422);
            }
                       } catch (\Illuminate\Validation\ValidationException $e) {
                   return response()->json([
                       'success' => false,
                       'message' => 'Validation failed',
                       'errors' => $e->errors()
                   ], 422);
               }

        $question = QuestionsAnswer::create([
            'user_id' => $user->id,
            'course_id' => $request->course_id,
            'question_title' => $request->question_title,
            'question_content' => $request->question_content,
            'question_type' => $request->question_type,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'status' => 'pending', // Requires moderation
            'is_public' => true, // Make public immediately so students can see their questions
            'views_count' => 0,
            'helpful_votes' => 0,
            'total_votes' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Question submitted successfully!'),
            'redirect_url' => route('qa.index')
        ]);
    }

    /**
     * Add answer to question (instructor only)
     */
    public function answer(Request $request, QuestionsAnswer $question)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Check if user is admin or course instructor
        $isAdmin = $user instanceof \App\Models\Admin;
        $isCourseInstructor = $question->course && $question->course->instructor_id === $user->id;

        // Only allow admins or course instructors to answer
        if (!$isAdmin && !$isCourseInstructor) {
            return response()->json(['error' => 'Only course instructors and administrators can answer questions'], 403);
        }

        $request->validate([
            'answer_content' => 'required|string|min:10|max:2000'
        ]);

        $question->update([
            'answer_content' => $request->answer_content,
            'instructor_id' => $user->id,
            'answered_at' => now(),
            'status' => 'answered' // Make answer visible immediately
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Answer submitted successfully!'
        ]);
    }

    /**
     * Vote on question (helpful/not helpful)
     */
    public function vote(Request $request, QuestionsAnswer $question)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'type' => 'required|in:up,down'
        ]);

        // Check if user already voted (you might want to create a votes table)
        // For now, we'll just increment/decrement

        if ($request->type === 'up') {
            $question->increment('votes');
        } else {
            $question->decrement('votes');
        }

        return response()->json([
            'success' => true,
            'new_votes' => $question->fresh()->votes
        ]);
    }

    /**
     * Get Q&A for specific course (AJAX)
     */
    public function courseQuestions(Course $course)
    {
        $questions = QuestionsAnswer::approved()
            ->where('course_id', $course->id)
            ->with(['user'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()->json($questions);
    }

    /**
     * Search questions (AJAX)
     */
    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:3'
        ]);

        $questions = QuestionsAnswer::approved()
            ->where(function($q) use ($request) {
                $q->where('question', 'like', '%' . $request->query . '%')
                  ->orWhere('answer', 'like', '%' . $request->query . '%');
            })
            ->with(['user', 'course'])
            ->orderByDesc('votes')
            ->limit(20)
            ->get();

        return response()->json($questions);
    }

    /**
     * Get popular questions
     */
    public function popular()
    {
        $questions = QuestionsAnswer::approved()
            ->orderByDesc('votes')
            ->orderByDesc('views')
            ->with(['user', 'course'])
            ->limit(10)
            ->get();

        return response()->json($questions);
    }

    /**
     * Get user's questions
     */
    public function myQuestions()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $questions = QuestionsAnswer::where('user_id', $user->id)
            ->with(['course'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('qa.my-questions', compact('questions'));
    }

    /**
     * Delete user's own question
     */
    public function destroy(QuestionsAnswer $question)
    {
        $user = Auth::user();
        if (!$user || $question->user_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Only allow deletion if not answered yet
        if ($question->status === 'answered') {
            return response()->json(['error' => 'Cannot delete answered questions'], 400);
        }

        $question->delete();

        return response()->json([
            'success' => true,
            'message' => 'Question deleted successfully'
        ]);
    }

    /**
     * Get statistics for dashboard
     */
    public function statistics()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_questions' => QuestionsAnswer::where('user_id', $user->id)->count(),
            'answered_questions' => QuestionsAnswer::where('user_id', $user->id)
                ->where('status', 'answered')->count(),
            'pending_questions' => QuestionsAnswer::where('user_id', $user->id)
                ->where('status', 'pending')->count(),
            'total_votes' => QuestionsAnswer::where('user_id', $user->id)->sum('votes'),
        ];

        return response()->json($stats);
    }
}
