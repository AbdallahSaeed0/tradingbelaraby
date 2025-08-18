<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        $questionType = $request->question_type ?? 'multiple_choice';

        // Base validation
        $validationRules = [
            'question_text' => 'required|string|max:1000',
            'question_type' => 'required|string|in:multiple_choice,true_false,fill_blank,essay',
            'points' => 'required|integer|min:1',
        ];

        // Type-specific validation
        switch ($questionType) {
            case 'multiple_choice':
                $validationRules['options'] = 'required|array|min:2';
                $validationRules['options.*'] = 'required|string|max:500';
                $validationRules['correct_answers'] = 'required|array|min:1';
                $validationRules['correct_answers.*'] = 'integer|min:0';
                break;

            case 'true_false':
                $validationRules['correct_answer_boolean'] = 'required|boolean';
                break;

            case 'fill_blank':
                $validationRules['correct_answers_text'] = 'required|array|min:1';
                $validationRules['correct_answers_text.*'] = 'required|string|max:500';
                break;

            case 'essay':
                $validationRules['sample_answer'] = 'nullable|string|max:1000';
                $validationRules['word_limit'] = 'nullable|integer|min:1';
                break;
        }

        $request->validate($validationRules);

        // Prepare question data
        $questionData = [
            'question_text' => $request->question_text,
            'question_type' => $questionType,
            'points' => $request->points,
            'order' => $request->order ?? ($quiz->questions()->max('order') + 1),
            'explanation' => $request->explanation,
        ];

        // Add type-specific data
        switch ($questionType) {
            case 'multiple_choice':
                $questionData['options'] = $request->options;
                $questionData['correct_answers'] = $request->correct_answers;
                break;

            case 'true_false':
                $questionData['correct_answer_boolean'] = $request->correct_answer_boolean;
                break;

            case 'fill_blank':
                $questionData['correct_answers_text'] = $request->correct_answers_text;
                break;

            case 'essay':
                $questionData['sample_answer'] = $request->sample_answer;
                $questionData['word_limit'] = $request->word_limit;
                break;
        }

        $question = $quiz->questions()->create($questionData);

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
        // Debug logging
        Log::info('QuizQuestion show method called', [
            'quiz_id' => $quiz->id,
            'question_id' => $question->id,
            'expects_json' => request()->expectsJson(),
            'ajax' => request()->ajax(),
            'x_requested_with' => request()->header('X-Requested-With')
        ]);

        if (request()->expectsJson() || request()->ajax() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
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
        $questionType = $request->question_type ?? $question->question_type;

        // Base validation
        $validationRules = [
            'question_text' => 'required|string|max:1000',
            'question_type' => 'required|string|in:multiple_choice,true_false,fill_blank,essay',
            'points' => 'required|integer|min:1',
        ];

        // Type-specific validation
        switch ($questionType) {
            case 'multiple_choice':
                $validationRules['options'] = 'required|array|min:2';
                $validationRules['options.*'] = 'required|string|max:500';
                $validationRules['correct_answers'] = 'required|array|min:1';
                $validationRules['correct_answers.*'] = 'integer|min:0';
                break;

            case 'true_false':
                $validationRules['correct_answer_boolean'] = 'required|boolean';
                break;

            case 'fill_blank':
                $validationRules['correct_answers_text'] = 'required|array|min:1';
                $validationRules['correct_answers_text.*'] = 'required|string|max:500';
                break;

            case 'essay':
                $validationRules['sample_answer'] = 'nullable|string|max:1000';
                $validationRules['word_limit'] = 'nullable|integer|min:1';
                break;
        }

        $request->validate($validationRules);

        // Prepare question data
        $questionData = [
            'question_text' => $request->question_text,
            'question_type' => $questionType,
            'points' => $request->points,
            'order' => $request->order ?? $question->order,
            'explanation' => $request->explanation,
        ];

        // Add type-specific data
        switch ($questionType) {
            case 'multiple_choice':
                $questionData['options'] = $request->options;
                $questionData['correct_answers'] = $request->correct_answers;
                break;

            case 'true_false':
                $questionData['correct_answer_boolean'] = $request->correct_answer_boolean;
                break;

            case 'fill_blank':
                $questionData['correct_answers_text'] = $request->correct_answers_text;
                break;

            case 'essay':
                $questionData['sample_answer'] = $request->sample_answer;
                $questionData['word_limit'] = $request->word_limit;
                break;
        }

        $question->update($questionData);

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
