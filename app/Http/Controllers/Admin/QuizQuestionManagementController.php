<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

class QuizQuestionManagementController extends Controller
{
    public function index(Quiz $quiz)
    {
        $questions = $quiz->questions()->paginate(10);
        return view('admin.quizzes.questions.index', compact('quiz', 'questions'));
    }

    public function create(Quiz $quiz)
    {
        return view('admin.quizzes.questions.create', compact('quiz'));
    }

    public function store(Request $request, Quiz $quiz)
    {
        $request->validate([
            'question_text' => 'required|string|max:1000',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:500',
            'correct_answers' => 'required|array|min:1',
            'correct_answers.*' => 'integer|min:0'
        ]);

        $question = $quiz->questions()->create([
            'question_text' => $request->question_text,
            'question_type' => 'multiple_choice',
            'points' => $request->points ?? 1,
            'options' => $request->options,
            'correct_answers' => $request->correct_answers
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Question created successfully.',
                'question' => $question
            ]);
        }

        return redirect()->route('admin.quizzes.questions.index', $quiz)
            ->with('success', 'Question created successfully.');
    }

    public function show(Quiz $quiz, QuizQuestion $question)
    {
        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'question' => $question
            ]);
        }

        return view('admin.quizzes.questions.show', compact('quiz', 'question'));
    }

    public function edit(Quiz $quiz, QuizQuestion $question)
    {
        return view('admin.quizzes.questions.edit', compact('quiz', 'question'));
    }

    public function update(Request $request, Quiz $quiz, QuizQuestion $question)
    {
        $request->validate([
            'question_text' => 'required|string|max:1000',
            'options' => 'required|array|min:2',
            'options.*' => 'required|string|max:500',
            'correct_answers' => 'required|array|min:1',
            'correct_answers.*' => 'integer|min:0'
        ]);

        $question->update([
            'question_text' => $request->question_text,
            'points' => $request->points ?? 1,
            'options' => $request->options,
            'correct_answers' => $request->correct_answers
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Question updated successfully.',
                'question' => $question->fresh()
            ]);
        }

        return redirect()->route('admin.quizzes.questions.index', $quiz)
            ->with('success', 'Question updated successfully.');
    }

    public function destroy(Quiz $quiz, QuizQuestion $question)
    {
        $question->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Question deleted successfully.'
            ]);
        }

        return redirect()->route('admin.quizzes.questions.index', $quiz)
            ->with('success', 'Question deleted successfully.');
    }
}
