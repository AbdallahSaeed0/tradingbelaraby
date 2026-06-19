<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\QuestionsAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseQuestionController extends Controller
{
    /**
     * List Q&A for a course (same visibility as website: public + user's own).
     */
    public function index(Request $request, $courseId)
    {
        $course = Course::published()->findOrFail($courseId);
        $user = Auth::user();

        $query = QuestionsAnswer::where('course_id', $course->id)
            ->with(['user', 'instructor'])
            ->where(function ($q) use ($user) {
                $q->where('is_public', true)->whereIn('status', ['pending', 'answered']);
                if ($user) {
                    $q->orWhere('user_id', $user->id);
                }
            })
            ->orderByDesc('created_at');

        $page = max(1, (int) $request->get('page', 1));
        $pageSize = min(50, max(1, (int) $request->get('page_size', 20)));
        $questions = $query->paginate($pageSize, ['*'], 'page', $page);

        $items = $questions->getCollection()->map(function ($q) use ($user) {
            $authorName = $q->is_anonymous ? 'Anonymous' : ($q->user ? $q->user->name : 'User');
            return [
                'id' => (string) $q->id,
                'question_title' => $q->question_title,
                'question_content' => $q->question_content,
                'question_type' => $q->question_type,
                'is_anonymous' => (bool) $q->is_anonymous,
                'author_name' => $authorName,
                'answer_content' => $q->answer_content,
                'answered_at' => $q->answered_at?->toIso8601String(),
                'instructor_name' => $q->instructor ? $q->instructor->name : null,
                'status' => $q->status,
                'created_at' => $q->created_at?->toIso8601String(),
                'is_mine' => $user && (int) $q->user_id === (int) $user->id,
            ];
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $questions->currentPage(),
                'last_page' => $questions->lastPage(),
                'per_page' => $questions->perPage(),
                'total' => $questions->total(),
            ],
        ]);
    }

    /**
     * Submit a question (enrolled students only; same fields as website).
     */
    public function store(Request $request, $courseId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $course = Course::published()->findOrFail($courseId);

        if (!$course->isEnrolledBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You must be enrolled in this course to ask a question.',
            ], 403);
        }

        $validated = $request->validate([
            'question_title' => 'required|string|min:3|max:500',
            'question_content' => 'nullable|string|max:2000',
            'question_type' => 'required|in:general,technical,assignment,schedule,content,other',
            'is_anonymous' => 'boolean',
        ], [
            'question_title.required' => 'Question title is required.',
            'question_title.min' => 'Question title must be at least 3 characters.',
        ]);

        $question = QuestionsAnswer::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'question_title' => $validated['question_title'],
            'question_content' => $validated['question_content'] ?? '',
            'question_type' => $validated['question_type'],
            'is_anonymous' => $request->boolean('is_anonymous'),
            'status' => 'pending',
            'is_public' => true,
            'views_count' => 0,
            'helpful_votes' => 0,
            'total_votes' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Question submitted successfully.',
            'data' => [
                'id' => (string) $question->id,
                'question_title' => $question->question_title,
                'question_content' => $question->question_content,
                'question_type' => $question->question_type,
                'status' => $question->status,
                'created_at' => $question->created_at?->toIso8601String(),
            ],
        ], 201);
    }
}
