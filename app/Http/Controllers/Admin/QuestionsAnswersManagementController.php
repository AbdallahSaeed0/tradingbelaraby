<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionsAnswer;
use App\Models\Course;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuestionsAnswersManagementController extends Controller
{
    /**
     * Display a listing of questions and answers
     */
    public function index(Request $request)
    {
        $query = QuestionsAnswer::with(['user', 'course', 'instructor', 'lecture', 'section']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by course
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by question type
        if ($request->filled('question_type')) {
            $query->where('question_type', $request->question_type);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by instructor
        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->instructor_id);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('question_title', 'like', "%{$search}%")
                  ->orWhere('question_content', 'like', "%{$search}%")
                  ->orWhere('answer_content', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('course', function($courseQuery) use ($search) {
                      $courseQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sort options
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at');
                break;
            case 'priority':
                $query->orderByRaw("FIELD(priority, 'urgent', 'high', 'normal', 'low')")
                      ->orderByDesc('created_at');
                break;
            case 'views':
                $query->orderByDesc('views_count');
                break;
            case 'votes':
                $query->orderByDesc('helpful_votes');
                break;
            case 'latest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $questions = $query->paginate(15);

        // Get filter options
        $courses = Course::orderBy('name')->get();
        $questionTypes = [
            'general' => 'General',
            'lecture_specific' => 'Lecture Specific',
            'technical' => 'Technical',
            'clarification' => 'Clarification'
        ];
        $priorities = [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent'
        ];
        $statuses = [
            'pending' => 'Pending',
            'answered' => 'Answered',
            'closed' => 'Closed',
            'flagged' => 'Flagged'
        ];

        // Get statistics
        $stats = [
            'total' => QuestionsAnswer::count(),
            'pending' => QuestionsAnswer::where('status', 'pending')->count(),
            'answered' => QuestionsAnswer::where('status', 'answered')->count(),
            'urgent' => QuestionsAnswer::where('priority', 'urgent')->count(),
            'flagged' => QuestionsAnswer::where('status', 'flagged')->count(),
        ];

        return view('admin.questions-answers.index', compact(
            'questions',
            'courses',
            'questionTypes',
            'priorities',
            'statuses',
            'stats'
        ));
    }

    /**
     * Display the specified question with details
     */
    public function show(QuestionsAnswer $questionsAnswer)
    {
        $questionsAnswer->load(['user', 'course', 'instructor', 'lecture', 'section', 'moderator']);

        // Increment view count
        $questionsAnswer->incrementViews();

        // Get related questions
        $relatedQuestions = QuestionsAnswer::where('course_id', $questionsAnswer->course_id)
            ->where('id', '!=', $questionsAnswer->id)
            ->where('status', 'answered')
            ->orderByDesc('helpful_votes')
            ->limit(5)
            ->get();

        return view('admin.questions-answers.show', compact('questionsAnswer', 'relatedQuestions'));
    }

    /**
     * Show form to reply to a question
     */
    public function reply(QuestionsAnswer $questionsAnswer)
    {
        $questionsAnswer->load(['user', 'course', 'lecture', 'section']);

        return view('admin.questions-answers.reply', compact('questionsAnswer'));
    }

    /**
     * Store reply to a question
     */
    public function storeReply(Request $request, QuestionsAnswer $questionsAnswer)
    {
        $request->validate([
            'answer_content' => 'required|string|min:10|max:5000',
            'moderation_notes' => 'nullable|string|max:1000',
            'audio_data' => 'nullable|string',
        ]);

        $admin = Auth::guard('admin')->user();

        $updateData = [
            'answer_content' => $request->answer_content,
            'instructor_id' => $admin->id,
            'answered_at' => now(),
            'status' => 'answered',
            'is_public' => true,
            'moderation_notes' => $request->moderation_notes,
        ];

        // Handle audio upload if provided
        if ($request->audio_data) {
            try {
                $audioPath = $this->saveAudioFile($request->audio_data, $questionsAnswer->id);
                $updateData['answer_audio'] = $audioPath;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Error saving audio file: ' . $e->getMessage())
                    ->withInput();
            }
        }

        $questionsAnswer->update($updateData);

        return redirect()->route('admin.questions-answers.show', $questionsAnswer)
            ->with('success', 'Reply added successfully!');
    }

    /**
     * Update reply to a question
     */
    public function updateReply(Request $request, QuestionsAnswer $questionsAnswer)
    {
        $request->validate([
            'answer_content' => 'required|string|min:10|max:5000',
            'moderation_notes' => 'nullable|string|max:1000',
            'audio_data' => 'nullable|string',
        ]);

        $admin = Auth::guard('admin')->user();

        $updateData = [
            'answer_content' => $request->answer_content,
            'moderation_notes' => $request->moderation_notes,
        ];

        // Handle audio upload if provided
        if ($request->audio_data) {
            try {
                // Delete old audio file if exists
                if ($questionsAnswer->answer_audio) {
                    $this->deleteAudioFile($questionsAnswer->answer_audio);
                }

                $audioPath = $this->saveAudioFile($request->audio_data, $questionsAnswer->id);
                $updateData['answer_audio'] = $audioPath;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Error saving audio file: ' . $e->getMessage())
                    ->withInput();
            }
        }

        $questionsAnswer->update($updateData);

        return redirect()->route('admin.questions-answers.show', $questionsAnswer)
            ->with('success', 'Reply updated successfully!');
    }

    /**
     * Delete reply to a question
     */
    public function deleteReply(QuestionsAnswer $questionsAnswer)
    {
        // Delete audio file if exists
        if ($questionsAnswer->answer_audio) {
            $this->deleteAudioFile($questionsAnswer->answer_audio);
        }

        $questionsAnswer->update([
            'answer_content' => null,
            'answer_audio' => null,
            'instructor_id' => null,
            'answered_at' => null,
            'status' => 'pending',
        ]);

        return redirect()->route('admin.questions-answers.show', $questionsAnswer)
            ->with('success', 'Reply deleted successfully!');
    }

    /**
     * Approve a question (make it public)
     */
    public function approve(QuestionsAnswer $questionsAnswer)
    {
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            Log::error('Admin authentication failed for approval');
            return redirect()->back()->with('error', 'Admin authentication required!');
        }

        try {
            Log::info('Approving question', [
                'question_id' => $questionsAnswer->id,
                'admin_id' => $admin->id,
                'current_status' => $questionsAnswer->status,
                'current_public' => $questionsAnswer->is_public
            ]);

            $questionsAnswer->update([
                'is_public' => true,
                'status' => $questionsAnswer->answer_content ? 'answered' : 'pending',
                'moderated_by' => $admin->id,
                'moderated_at' => now(),
            ]);

            Log::info('Question approved successfully', [
                'question_id' => $questionsAnswer->id,
                'new_status' => $questionsAnswer->fresh()->status,
                'new_public' => $questionsAnswer->fresh()->is_public
            ]);

            return redirect()->back()->with('success', 'Question approved successfully!');
        } catch (\Exception $e) {
            Log::error('Error approving question', [
                'question_id' => $questionsAnswer->id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Error approving question: ' . $e->getMessage());
        }
    }

    /**
     * Reject/flag a question
     */
    public function reject(Request $request, QuestionsAnswer $questionsAnswer)
    {
        $request->validate([
            'moderation_notes' => 'required|string|max:1000',
        ]);

        $admin = Auth::guard('admin')->user();

        $questionsAnswer->update([
            'is_public' => false,
            'status' => 'flagged',
            'moderated_by' => $admin->id,
            'moderated_at' => now(),
            'moderation_notes' => $request->moderation_notes,
        ]);

        return redirect()->back()->with('success', 'Question rejected successfully!');
    }

    /**
     * Close a question
     */
    public function close(QuestionsAnswer $questionsAnswer)
    {
        $questionsAnswer->update(['status' => 'closed']);

        return redirect()->back()->with('success', 'Question closed successfully!');
    }

    /**
     * Reopen a question
     */
    public function reopen(QuestionsAnswer $questionsAnswer)
    {
        $questionsAnswer->update(['status' => 'pending']);

        return redirect()->back()->with('success', 'Question reopened successfully!');
    }

    /**
     * Update question priority
     */
    public function updatePriority(Request $request, QuestionsAnswer $questionsAnswer)
    {
        $request->validate([
            'priority' => 'required|in:low,normal,high,urgent',
        ]);

        $questionsAnswer->update(['priority' => $request->priority]);

        return redirect()->back()->with('success', 'Priority updated successfully!');
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions_answers,id',
            'status' => 'required|in:pending,answered,closed,flagged',
        ]);

        $admin = Auth::guard('admin')->user();

        QuestionsAnswer::whereIn('id', $request->question_ids)->update([
            'status' => $request->status,
            'moderated_by' => $admin->id,
            'moderated_at' => now(),
        ]);

        return redirect()->back()->with('success', count($request->question_ids) . ' questions updated successfully!');
    }

    /**
     * Bulk delete questions
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions_answers,id',
        ]);

        QuestionsAnswer::whereIn('id', $request->question_ids)->delete();

        return redirect()->back()->with('success', count($request->question_ids) . ' questions deleted successfully!');
    }

    /**
     * Show analytics for Q&A
     */
    public function analytics()
    {
        // Get overall statistics
        $stats = [
            'total_questions' => QuestionsAnswer::count(),
            'answered_questions' => QuestionsAnswer::where('status', 'answered')->count(),
            'pending_questions' => QuestionsAnswer::where('status', 'pending')->count(),
            'urgent_questions' => QuestionsAnswer::where('priority', 'urgent')->count(),
            'avg_response_time' => $this->getAverageResponseTime(),
            'total_views' => QuestionsAnswer::sum('views_count'),
            'total_votes' => QuestionsAnswer::sum('helpful_votes'),
        ];

        // Get questions by status
        $questionsByStatus = QuestionsAnswer::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Get questions by priority
        $questionsByPriority = QuestionsAnswer::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')
            ->pluck('count', 'priority')
            ->toArray();

        // Get questions by type
        $questionsByType = QuestionsAnswer::select('question_type', DB::raw('count(*) as count'))
            ->groupBy('question_type')
            ->pluck('count', 'question_type')
            ->toArray();

        // Get top courses by questions
        $topCourses = QuestionsAnswer::with('course')
            ->select('course_id', DB::raw('count(*) as count'))
            ->groupBy('course_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Get recent activity
        $recentActivity = QuestionsAnswer::with(['user', 'course', 'instructor'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Get monthly trends
        $monthlyTrends = QuestionsAnswer::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.questions-answers.analytics', compact(
            'stats',
            'questionsByStatus',
            'questionsByPriority',
            'questionsByType',
            'topCourses',
            'recentActivity',
            'monthlyTrends'
        ));
    }

    /**
     * Get average response time in hours
     */
    private function getAverageResponseTime()
    {
        $answeredQuestions = QuestionsAnswer::whereNotNull('answered_at')
            ->whereNotNull('created_at')
            ->get();

        if ($answeredQuestions->isEmpty()) {
            return 0;
        }

        $totalHours = $answeredQuestions->sum(function ($question) {
            return $question->created_at->diffInHours($question->answered_at);
        });

        return round($totalHours / $answeredQuestions->count(), 1);
    }

    /**
     * Export questions data
     */
    public function export(Request $request)
    {
        $query = QuestionsAnswer::with(['user', 'course', 'instructor']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        $questions = $query->get();

        $filename = 'questions_answers_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($questions) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, [
                'ID', 'Question Title', 'Question Content', 'Answer Content',
                'Status', 'Priority', 'Question Type', 'Student', 'Course',
                'Instructor', 'Created At', 'Answered At', 'Views', 'Votes'
            ]);

            // Add data
            foreach ($questions as $question) {
                fputcsv($file, [
                    $question->id,
                    $question->question_title,
                    strip_tags($question->question_content),
                    strip_tags($question->answer_content ?? ''),
                    $question->status,
                    $question->priority,
                    $question->question_type,
                    $question->user->name ?? 'Anonymous',
                    $question->course->name ?? '',
                    $question->instructor->name ?? '',
                    $question->created_at->format('Y-m-d H:i:s'),
                    $question->answered_at ? $question->answered_at->format('Y-m-d H:i:s') : '',
                    $question->views_count,
                    $question->helpful_votes,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Delete a single question
     */
    public function destroy(QuestionsAnswer $questionsAnswer)
    {
        $questionsAnswer->delete();

        return redirect()->back()
            ->with('success', 'Question deleted successfully.');
    }

    /**
     * Save audio file from base64 data
     */
    private function saveAudioFile(string $audioData, int $questionId): string
    {
        // Validate audio data format
        if (empty($audioData)) {
            throw new \Exception('No audio data provided');
        }

        // Extract base64 data
        if (strpos($audioData, 'data:audio/webm;base64,') === 0) {
            $audioData = substr($audioData, 22); // Remove data:audio/webm;base64, prefix
        } elseif (strpos($audioData, 'data:audio/mp3;base64,') === 0) {
            $audioData = substr($audioData, 20); // Remove data:audio/mp3;base64, prefix
        }

        $audioBinary = base64_decode($audioData);

        if ($audioBinary === false) {
            throw new \Exception('Invalid audio data format');
        }

        // Validate file size (10MB max)
        if (strlen($audioBinary) > 10 * 1024 * 1024) {
            throw new \Exception('Audio file too large. Maximum size is 10MB.');
        }

        // Validate minimum file size (1KB)
        if (strlen($audioBinary) < 1024) {
            throw new \Exception('Audio file too small. Please record for at least a few seconds.');
        }

        // Generate filename with proper extension
        $extension = 'webm'; // Default to webm for MediaRecorder
        $filename = 'question_' . $questionId . '_' . time() . '.' . $extension;
        $path = 'question-answers/audio/' . $filename;

        // Save file
        $fullPath = storage_path('app/public/' . $path);
        $directory = dirname($fullPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_put_contents($fullPath, $audioBinary) === false) {
            throw new \Exception('Failed to save audio file to storage');
        }

        return $path;
    }

    /**
     * Delete audio file
     */
    private function deleteAudioFile(string $audioPath): void
    {
        $fullPath = storage_path('app/public/' . $audioPath);

        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

}
