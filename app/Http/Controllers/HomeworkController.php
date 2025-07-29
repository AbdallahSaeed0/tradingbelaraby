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
     * Get homework statistics for student dashboard
     */
    public function statistics()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $stats = [
            'total_assignments' => Homework::published()->count(),
            'submitted' => HomeworkSubmission::where('user_id', $user->id)->count(),
            'graded' => HomeworkSubmission::where('user_id', $user->id)
                ->where('status', 'graded')->count(),
            'pending' => HomeworkSubmission::where('user_id', $user->id)
                ->where('status', 'submitted')->count(),
            'overdue' => Homework::published()
                ->where('due_date', '<', now())
                ->whereDoesntHave('submissions', function($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->count(),
        ];

        $stats['completion_rate'] = $stats['total_assignments'] > 0
            ? ($stats['submitted'] / $stats['total_assignments']) * 100
            : 0;

        return response()->json($stats);
    }
}
