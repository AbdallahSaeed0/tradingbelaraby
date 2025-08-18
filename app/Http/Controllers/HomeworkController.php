<?php

namespace App\Http\Controllers;

use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HomeworkController extends Controller
{
    /**
     * Display homework details
     */
    public function show(Homework $homework)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view homework.');
        }

        $homework->load(['course', 'submissions' => function($query) use ($user) {
            $query->where('user_id', $user->id)->orderBy('created_at', 'desc');
        }]);

        $userSubmission = $homework->submissions->first();
        $canSubmit = $homework->isSubmissionAllowed();

        // Check if user can resubmit
        $canResubmit = false;
        if ($userSubmission && $homework->allow_resubmission) {
            $canResubmit = $userSubmission->status !== 'graded' || $homework->due_date > now();
        }

        return view('homework.show', compact('homework', 'userSubmission', 'canSubmit', 'canResubmit'));
    }

    /**
     * Show homework submission form
     */
    public function create(Homework $homework)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to submit homework.');
        }

        if (!$homework->isSubmissionAllowed()) {
            return redirect()->route('homework.show', $homework)
                ->with('error', 'Submission is not allowed for this homework.');
        }

        $existingSubmission = HomeworkSubmission::where('user_id', $user->id)
            ->where('homework_id', $homework->id)
            ->first();

        if ($existingSubmission && !$homework->allow_resubmission) {
            return redirect()->route('homework.show', $homework)
                ->with('error', 'You have already submitted this homework.');
        }

        return view('homework.create', compact('homework', 'existingSubmission'));
    }

    /**
     * Store homework submission
     */
    public function store(Request $request, Homework $homework)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        if (!$homework->isSubmissionAllowed()) {
            return response()->json(['error' => 'Submission not allowed'], 403);
        }

        $request->validate([
            'content' => 'required|string',
            'attachments.*' => 'file|max:10240', // 10MB max per file
        ]);

        // Check if user already submitted and resubmission is not allowed
        $existingSubmission = HomeworkSubmission::where('user_id', $user->id)
            ->where('homework_id', $homework->id)
            ->first();

        if ($existingSubmission && !$homework->allow_resubmission) {
            return response()->json(['error' => 'Resubmission not allowed'], 403);
        }

        // Handle file uploads
        $attachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('homework_submissions', $filename, 'public');

                $attachments[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'filename' => $filename,
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
        }

        // Create or update submission
        $submissionData = [
            'content' => $request->content,
            'attachments' => $attachments,
            'submitted_at' => now(),
            'status' => 'submitted',
        ];

        if ($existingSubmission && $homework->allow_resubmission) {
            // Delete old attachments
            if ($existingSubmission->attachments) {
                foreach ($existingSubmission->attachments as $attachment) {
                    Storage::disk('public')->delete($attachment['path']);
                }
            }

            $existingSubmission->update($submissionData);
            $submission = $existingSubmission;
        } else {
            $submissionData['homework_id'] = $homework->id;
            $submissionData['user_id'] = $user->id;
            $submission = HomeworkSubmission::create($submissionData);
        }

        return response()->json([
            'success' => true,
            'message' => 'Homework submitted successfully',
            'redirect_url' => route('homework.show', $homework)
        ]);
    }

    /**
     * Download attachment
     */
    public function downloadAttachment(HomeworkSubmission $submission, $filename)
    {
        $user = Auth::user();

        // Check if user owns this submission or is an instructor/admin
        if (!$user || $submission->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $attachment = collect($submission->attachments)->firstWhere('filename', $filename);

        if (!$attachment) {
            abort(404, 'File not found');
        }

        $filePath = storage_path('app/public/' . $attachment['path']);

        if (!file_exists($filePath)) {
            abort(404, 'File not found on disk');
        }

        return response()->download($filePath, $attachment['original_name']);
    }

    /**
     * Display list of homework for student
     */
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Get all homework assignments (you might want to filter by enrolled courses)
        $homework = Homework::published()
            ->with(['course', 'submissions' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->orderBy('due_date')
            ->paginate(10);

        // Add submission status to each homework
        $homework->getCollection()->transform(function($hw) {
            $submission = $hw->submissions->first();
            $hw->submission_status = $submission ? $submission->status : 'not_submitted';
            $hw->user_submission = $submission;
            return $hw;
        });

        return view('homework.index', compact('homework'));
    }

    /**
     * View submission details
     */
    public function viewSubmission(HomeworkSubmission $submission)
    {
        $user = Auth::user();

        // Check if user owns this submission
        if (!$user || $submission->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $submission->load(['homework.course', 'user']);

        return view('homework.submission', compact('submission'));
    }

    /**
     * Get upcoming homework assignments (AJAX)
     */
    public function upcoming()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $upcoming = Homework::published()
            ->where('due_date', '>', now())
            ->whereDoesntHave('submissions', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('course')
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return response()->json($upcoming);
    }

    /**
     * Mark homework as viewed (for tracking)
     */
    public function markViewed(Homework $homework)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // You could track homework views in a separate table if needed
        // For now, we'll just return success

        return response()->json(['success' => true]);
    }

    /**
     * Get homework statistics
     */
    public function statistics()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_homework' => Homework::where('course_id', $user->enrolledCourses()->pluck('course_id'))->count(),
            'submitted' => HomeworkSubmission::where('user_id', $user->id)->count(),
            'pending' => Homework::where('course_id', $user->enrolledCourses()->pluck('course_id'))
                ->whereDoesntHave('submissions', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->count(),
            'overdue' => Homework::where('course_id', $user->enrolledCourses()->pluck('course_id'))
                ->where('due_date', '<', now())
                ->whereDoesntHave('submissions', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Submit homework for a specific course
     */
    public function submitCourseHomework(Request $request, Course $course)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login to submit homework'], 401);
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
            'file' => 'nullable|file|max:10240', // 10MB max
        ]);

        try {
            $homework = Homework::create([
                'course_id' => $course->id,
                'title' => $request->title,
                'description' => $request->description,
                'type' => 'assignment',
                'status' => 'active',
                'points' => 0,
                'due_date' => now()->addDays(7), // Default due date
            ]);

            // Create submission if content or file is provided
            if ($request->content || $request->hasFile('file')) {
                $submission = HomeworkSubmission::create([
                    'homework_id' => $homework->id,
                    'user_id' => $user->id,
                    'content' => $request->content,
                    'status' => 'submitted',
                ]);

                // Handle file upload
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                    $path = $file->storeAs('homework-attachments', $filename, 'public');

                    $submission->attachments = json_encode([$path]);
                    $submission->save();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Homework submitted successfully',
                'homework' => $homework
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit homework: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get homework details for a specific course
     */
    public function getCourseHomework(Course $course, $assignment_id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login to view homework'], 401);
        }

        try {
            $homework = Homework::where('id', $assignment_id)
                ->where('course_id', $course->id)
                ->first();

            if (!$homework) {
                return response()->json([
                    'success' => false,
                    'message' => 'Homework assignment not found'
                ], 404);
            }

            $homework->load(['course', 'submissions' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }]);

            // Get user's submission and grade info
            $userSubmission = $homework->submissions->first();
            $gradeInfo = null;

            if ($userSubmission && $userSubmission->is_graded) {
                $gradeInfo = [
                    'score_earned' => $userSubmission->score_earned,
                    'max_score' => $userSubmission->max_score,
                    'percentage_score' => number_format($userSubmission->percentage_score, 1),
                    'feedback' => $userSubmission->feedback,
                    'graded_at' => $userSubmission->graded_at->format('M d, Y h:i A'),
                ];
            }

            return response()->json([
                'success' => true,
                'assignment' => [
                    'id' => $homework->id,
                    'title' => $homework->name,
                    'description' => $homework->description,
                    'due_date' => $homework->due_date,
                    'max_score' => $homework->max_score,
                    'type' => $homework->type,
                    'status' => $userSubmission ? ($userSubmission->is_graded ? 'graded' : 'submitted') : 'not_started',
                    'grade' => $gradeInfo,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load homework details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit assignment for a specific homework
     */
    public function submitAssignment(Request $request, Course $course)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Please login to submit assignment'], 401);
        }

        $request->validate([
            'assignment_id' => 'required|exists:homework,id',
            'content' => 'nullable|string',
            'file' => 'required|file|max:10240', // 10MB max
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $homework = Homework::findOrFail($request->assignment_id);

            // Check if homework belongs to the course
            if ($homework->course_id !== $course->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Homework does not belong to this course'
                ], 400);
            }

            // Check if user already submitted - allow resubmission
            $existingSubmission = HomeworkSubmission::where('user_id', $user->id)
                ->where('homework_id', $homework->id)
                ->first();

            // Handle file upload
            $file = $request->file('file');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('homework-attachments', $filename, 'public');

            if ($existingSubmission) {
                // Update existing submission
                $existingSubmission->update([
                    'submission_text' => $request->content,
                    'submission_file' => $path,
                    'student_notes' => $request->notes,
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ]);

                $submission = $existingSubmission;
                $message = 'Assignment resubmitted successfully';
            } else {
                // Create new submission
                $submission = HomeworkSubmission::create([
                    'homework_id' => $homework->id,
                    'user_id' => $user->id,
                    'submission_text' => $request->content,
                    'submission_file' => $path,
                    'student_notes' => $request->notes,
                    'status' => 'submitted',
                    'submitted_at' => now(),
                ]);

                $message = 'Assignment submitted successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'submission' => $submission
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit assignment: ' . $e->getMessage()
            ], 500);
        }
    }
}
