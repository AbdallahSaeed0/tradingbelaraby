<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\Course;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class HomeworkManagementController extends Controller
{
    /**
     * Display homework management dashboard
     */
    public function index(Request $request)
    {
        $query = Homework::with(['course', 'instructor', 'submissions']);

        // Filter by instructor permissions
        $admin = auth('admin')->user();
        if ($admin->hasPermission('manage_own_homework') && !$admin->hasPermission('manage_homework')) {
            // Instructor can only see their own homework
            $query->where('instructor_id', $admin->id);
        }

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('course', function ($courseQuery) use ($search) {
                      $courseQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('instructor', function ($instructorQuery) use ($search) {
                      $instructorQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('course')) {
            $query->where('course_id', $request->course);
        }

        if ($request->filled('instructor')) {
            $query->where('instructor_id', $request->instructor);
        }

        if ($request->filled('status')) {
            $query->where('is_published', $request->status === 'published');
        }

        $homework = $query->orderByDesc('created_at')->paginate(15);

        // Get stats based on permissions
        if ($admin->hasPermission('manage_own_homework') && !$admin->hasPermission('manage_homework')) {
            // Instructor stats - only their own homework
            $stats = [
                'total_homework' => Homework::where('instructor_id', $admin->id)->count(),
                'published_homework' => Homework::where('instructor_id', $admin->id)->where('is_published', true)->count(),
                'draft_homework' => Homework::where('instructor_id', $admin->id)->where('is_published', false)->count(),
                'total_submissions' => HomeworkSubmission::whereHas('homework', function($q) use ($admin) {
                    $q->where('instructor_id', $admin->id);
                })->count(),
                'pending_grading' => HomeworkSubmission::whereHas('homework', function($q) use ($admin) {
                    $q->where('instructor_id', $admin->id);
                })->where('status', 'submitted')->count(),
                'overdue_homework' => Homework::where('instructor_id', $admin->id)->where('due_date', '<', now())->where('is_published', true)->count(),
            ];
        } else {
            // Admin stats - all homework
            $stats = [
                'total_homework' => Homework::count(),
                'published_homework' => Homework::where('is_published', true)->count(),
                'draft_homework' => Homework::where('is_published', false)->count(),
                'total_submissions' => HomeworkSubmission::count(),
                'pending_grading' => HomeworkSubmission::where('status', 'submitted')->count(),
                'overdue_homework' => Homework::where('due_date', '<', now())->where('is_published', true)->count(),
            ];
        }

        // Get filter data based on permissions
        if ($admin->hasPermission('manage_own_courses') && !$admin->hasPermission('manage_courses')) {
            // Instructor can only see their own courses
            $courses = Course::published()->where('instructor_id', $admin->id)->get();
            $instructors = collect([$admin]); // Only show themselves
        } else {
            // Admin can see all courses and instructors
            $courses = Course::published()->get();
            $instructors = Admin::whereHas('adminType', function($query) {
                $query->where('name', 'instructor');
            })->where('is_active', true)->get();
        }

        return view('admin.homework.index', compact('homework', 'stats', 'courses', 'instructors'));
    }

    /**
     * Show homework creation form
     */
    public function create()
    {
        $courses = Course::published()->get();
        $instructors = Admin::whereHas('adminType', function($query) {
            $query->where('name', 'instructor');
        })->where('is_active', true)->get();

        return view('admin.homework.create', compact('courses', 'instructors'));
    }

    /**
     * Store new homework
     */
    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:admins,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'instructions' => 'nullable|string',
            'instructions_ar' => 'nullable|string',
            'due_date' => 'required|date|after:now',
            'max_score' => 'required|integer|min:1|max:1000',
            'weight_percentage' => 'required|integer|min:1|max:100',
            'allow_late_submission' => 'boolean',
            'late_submission_until' => 'nullable|date|after:due_date',
            'require_file_upload' => 'boolean',
            'allow_text_submission' => 'boolean',
            'attachment_file' => 'nullable|file|max:10240', // 10MB max
            'additional_files.*' => 'nullable|file|max:10240',
        ]);

        try {
            // Handle file uploads
            $attachmentFile = null;
            $additionalFiles = [];

            if ($request->hasFile('attachment_file')) {
                $attachmentFile = $request->file('attachment_file')->store('homework-files', 'public');
            }

            if ($request->hasFile('additional_files')) {
                foreach ($request->file('additional_files') as $file) {
                    if ($file && $file->isValid()) {
                        $additionalFiles[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $file->store('homework-files', 'public'),
                            'size' => $file->getSize()
                        ];
                    }
                }
            }

            Homework::create([
                'course_id' => $request->course_id,
                'instructor_id' => $request->instructor_id,
                'name' => $request->name,
                'name_ar' => $request->name_ar,
                'description' => $request->description,
                'description_ar' => $request->description_ar,
                'instructions' => $request->instructions,
                'instructions_ar' => $request->instructions_ar,
                'due_date' => $request->due_date,
                'max_score' => $request->max_score,
                'weight_percentage' => $request->weight_percentage,
                'allow_late_submission' => $request->boolean('allow_late_submission'),
                'late_submission_until' => $request->late_submission_until,
                'require_file_upload' => $request->boolean('require_file_upload'),
                'allow_text_submission' => $request->boolean('allow_text_submission'),
                'attachment_file' => $attachmentFile,
                'additional_files' => $additionalFiles,
                'is_published' => true,
            ]);

            return redirect()->route('admin.homework.index')
                ->with('success', 'Homework created successfully');
        } catch (\Exception $e) {
            Log::error('Error creating homework: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error creating homework. Please try again.');
        }
    }

    /**
     * Show homework editing form
     */
    public function edit(Homework $homework)
    {
        $courses = Course::published()->get();
        $instructors = Admin::whereHas('adminType', function($query) {
            $query->where('name', 'instructor');
        })->where('is_active', true)->get();

        return view('admin.homework.edit', compact('homework', 'courses', 'instructors'));
    }

    /**
     * Update homework
     */
    public function update(Request $request, Homework $homework)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'instructor_id' => 'required|exists:admins,id',
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'instructions' => 'nullable|string',
            'instructions_ar' => 'nullable|string',
            'due_date' => 'required|date',
            'max_score' => 'required|integer|min:1|max:1000',
            'weight_percentage' => 'required|integer|min:1|max:100',
            'allow_late_submission' => 'boolean',
            'late_submission_until' => 'nullable|date|after:due_date',
            'require_file_upload' => 'boolean',
            'allow_text_submission' => 'boolean',
            'attachment_file' => 'nullable|file|max:10240',
            'additional_files.*' => 'nullable|file|max:10240',
        ]);

        try {
            $data = [
                'course_id' => $request->course_id,
                'instructor_id' => $request->instructor_id,
                'name' => $request->name,
                'name_ar' => $request->name_ar,
                'description' => $request->description,
                'description_ar' => $request->description_ar,
                'instructions' => $request->instructions,
                'instructions_ar' => $request->instructions_ar,
                'due_date' => $request->due_date,
                'max_score' => $request->max_score,
                'weight_percentage' => $request->weight_percentage,
                'allow_late_submission' => $request->boolean('allow_late_submission'),
                'late_submission_until' => $request->late_submission_until,
                'require_file_upload' => $request->boolean('require_file_upload'),
                'allow_text_submission' => $request->boolean('allow_text_submission'),
            ];

            // Handle file uploads
            if ($request->hasFile('attachment_file')) {
                // Delete old file if exists
                if ($homework->attachment_file) {
                    Storage::disk('public')->delete($homework->attachment_file);
                }
                $data['attachment_file'] = $request->file('attachment_file')->store('homework-files', 'public');
            }

            if ($request->hasFile('additional_files')) {
                $additionalFiles = $homework->additional_files ?? [];
                foreach ($request->file('additional_files') as $file) {
                    if ($file && $file->isValid()) {
                        $additionalFiles[] = [
                            'name' => $file->getClientOriginalName(),
                            'path' => $file->store('homework-files', 'public'),
                            'size' => $file->getSize()
                        ];
                    }
                }
                $data['additional_files'] = $additionalFiles;
            }

            $homework->update($data);

            return redirect()->route('admin.homework.index')
                ->with('success', 'Homework updated successfully');
        } catch (\Exception $e) {
            Log::error('Error updating homework: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error updating homework. Please try again.');
        }
    }

    /**
     * Delete homework
     */
    public function destroy(Homework $homework)
    {
        try {
            // Delete associated files
            if ($homework->attachment_file) {
                Storage::disk('public')->delete($homework->attachment_file);
            }

            if ($homework->additional_files) {
                foreach ($homework->additional_files as $file) {
                    Storage::disk('public')->delete($file['path']);
                }
            }

            // Delete submission files
            foreach ($homework->submissions as $submission) {
                if ($submission->submission_file) {
                    Storage::disk('public')->delete($submission->submission_file);
                }
                if ($submission->additional_files) {
                    foreach ($submission->additional_files as $file) {
                        Storage::disk('public')->delete($file['path']);
                    }
                }
            }

            $homework->delete();

            return redirect()->route('admin.homework.index')
                ->with('success', 'Homework deleted successfully');
        } catch (\Exception $e) {
            Log::error('Error deleting homework: ' . $e->getMessage());
            return back()->with('error', 'Error deleting homework. Please try again.');
        }
    }

    /**
     * Toggle homework published status
     */
    public function toggleStatus(Homework $homework)
    {
        try {
            $homework->update(['is_published' => !$homework->is_published]);

            return response()->json([
                'success' => true,
                'message' => 'Homework status updated successfully',
                'new_status' => $homework->is_published ? 'published' : 'draft'
            ]);
        } catch (\Exception $e) {
            Log::error('Error toggling homework status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating homework status'
            ], 500);
        }
    }

    /**
     * Update homework status
     */
    public function updateStatus(Request $request, Homework $homework)
    {
        $request->validate([
            'status' => 'required|in:published,draft'
        ]);

        $homework->update(['is_published' => $request->status === 'published']);

        return response()->json([
            'success' => true,
            'message' => 'Homework status updated successfully'
        ]);
    }

    /**
     * Duplicate homework
     */
    public function duplicate(Homework $homework)
    {
        try {
            $newHomework = $homework->replicate();
            $newHomework->name = $homework->name . ' (Copy)';
            $newHomework->due_date = now()->addDays(7);
            $newHomework->is_published = false;
            $newHomework->total_assignments = 0;
            $newHomework->submitted_assignments = 0;
            $newHomework->graded_assignments = 0;
            $newHomework->average_score = 0;
            $newHomework->on_time_submissions = 0;
            $newHomework->late_submissions = 0;
            $newHomework->save();

            return redirect()->route('admin.homework.index')
                ->with('success', 'Homework duplicated successfully');
        } catch (\Exception $e) {
            Log::error('Error duplicating homework: ' . $e->getMessage());
            return back()->with('error', 'Error duplicating homework. Please try again.');
        }
    }

    /**
     * Bulk delete homework
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'selected_homework' => 'required|array|min:1',
            'selected_homework.*' => 'exists:homework,id'
        ]);

        try {
            $homeworkToDelete = Homework::whereIn('id', $request->selected_homework)->get();

            foreach ($homeworkToDelete as $homework) {
                // Delete associated files
                if ($homework->attachment_file) {
                    Storage::disk('public')->delete($homework->attachment_file);
                }
                if ($homework->additional_files) {
                    foreach ($homework->additional_files as $file) {
                        Storage::disk('public')->delete($file['path']);
                    }
                }
                $homework->delete();
            }

            return response()->json([
                'success' => true,
                'message' => count($request->selected_homework) . ' homework deleted successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error bulk deleting homework: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting homework'
            ], 500);
        }
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'selected_homework' => 'required|array|min:1',
            'selected_homework.*' => 'exists:homework,id',
            'status' => 'required|boolean'
        ]);

        try {
            Homework::whereIn('id', $request->selected_homework)
                ->update(['is_published' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => count($request->selected_homework) . ' homework status updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error bulk updating homework status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating homework status'
            ], 500);
        }
    }

    /**
     * View submissions for homework
     */
    public function submissions(Homework $homework, Request $request)
    {
        $query = $homework->submissions()->with(['user']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('late_only') && $request->late_only) {
            $query->where('is_late', true);
        }

        if ($request->filled('graded_only') && $request->graded_only) {
            $query->where('is_graded', true);
        }

        $submissions = $query->orderByDesc('submitted_at')->paginate(20);

        return view('admin.homework.submissions', compact('homework', 'submissions'));
    }

    /**
     * Grade a homework submission
     */
    public function gradeSubmission(Request $request, HomeworkSubmission $submission)
    {
        $request->validate([
            'score_earned' => 'required|numeric|min:0|max:' . $submission->max_score,
            'feedback' => 'nullable|string',
            'instructor_notes' => 'nullable|string',
        ]);

        try {
            $percentageScore = ($request->score_earned / $submission->max_score) * 100;

            $submission->update([
                'score_earned' => $request->score_earned,
                'percentage_score' => $percentageScore,
                'feedback' => $request->feedback,
                'instructor_notes' => $request->instructor_notes,
                'graded_at' => now(),
                'graded_by' => auth('admin')->id(),
                'status' => 'graded',
                'is_graded' => true
            ]);

            // Update homework statistics
            $this->updateHomeworkStats($submission->homework);

            return response()->json([
                'success' => true,
                'message' => 'Submission graded successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error grading submission: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error grading submission'
            ], 500);
        }
    }

    /**
     * View individual submission details
     */
    public function viewSubmission(HomeworkSubmission $submission)
    {
        $submission->load(['homework.course', 'user']);

        // Check if submission is late
        $isLate = $submission->submitted_at > $submission->homework->due_date;
        $lateDays = $isLate ?
            $submission->homework->due_date->diffInDays($submission->submitted_at) : 0;

        return view('admin.homework.submission-details', compact('submission', 'isLate', 'lateDays'));
    }

    /**
     * Bulk grade submissions
     */
    public function bulkGrade(Request $request, Homework $homework)
    {
        $request->validate([
            'submissions' => 'required|array',
            'submissions.*.id' => 'required|exists:homework_submissions,id',
            'submissions.*.score' => 'required|numeric|min:0|max:' . $homework->max_score,
            'submissions.*.feedback' => 'nullable|string'
        ]);

        foreach ($request->submissions as $submissionData) {
            $submission = HomeworkSubmission::findOrFail($submissionData['id']);
            $submission->update([
                'score' => $submissionData['score'],
                'feedback' => $submissionData['feedback'] ?? null,
                'graded_at' => now(),
                'graded_by' => auth()->id(),
                'status' => 'graded'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Bulk grading completed successfully'
        ]);
    }

    /**
     * Export homework results to CSV
     */
    public function exportResults(Homework $homework)
    {
        $submissions = HomeworkSubmission::where('homework_id', $homework->id)
            ->with(['user'])
            ->get();

        $filename = 'homework_' . $homework->id . '_results_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($submissions, $homework) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Student Name',
                'Email',
                'Score',
                'Max Score',
                'Percentage',
                'Status',
                'Submitted At',
                'Days Late',
                'Graded At',
                'Feedback'
            ]);

            foreach ($submissions as $submission) {
                $isLate = $submission->submitted_at > $homework->due_date;
                $lateDays = $isLate ?
                    $homework->due_date->diffInDays($submission->submitted_at) : 0;
                $percentage = $submission->score ?
                    ($submission->score / $homework->max_score) * 100 : 0;

                fputcsv($file, [
                    $submission->user->name,
                    $submission->user->email,
                    $submission->score ?? 'Not graded',
                    $homework->max_score,
                    round($percentage, 2) . '%',
                    ucfirst($submission->status),
                    $submission->submitted_at ? $submission->submitted_at->format('Y-m-d H:i:s') : '',
                    $lateDays,
                    $submission->graded_at ? $submission->graded_at->format('Y-m-d H:i:s') : '',
                    $submission->feedback ?? 'No feedback'
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get homework statistics
     */
    public function statistics(Homework $homework)
    {
        $submissions = $homework->submissions;
        $gradedSubmissions = $submissions->where('status', 'graded');

        $stats = [
            'total_submissions' => $submissions->count(),
            'graded_submissions' => $gradedSubmissions->count(),
            'pending_submissions' => $submissions->where('status', 'submitted')->count(),
            'average_score' => $gradedSubmissions->avg('score'),
            'highest_score' => $gradedSubmissions->max('score'),
            'lowest_score' => $gradedSubmissions->min('score'),
            'late_submissions' => $submissions->where('submitted_at', '>', $homework->due_date)->count(),
            'on_time_rate' => $submissions->count() > 0 ?
                ($submissions->where('submitted_at', '<=', $homework->due_date)->count() / $submissions->count()) * 100 : 0,
            'score_distribution' => [
                '90-100%' => $gradedSubmissions->filter(function($s) use ($homework) {
                    return ($s->score / $homework->max_score) * 100 >= 90;
                })->count(),
                '80-89%' => $gradedSubmissions->filter(function($s) use ($homework) {
                    $percentage = ($s->score / $homework->max_score) * 100;
                    return $percentage >= 80 && $percentage < 90;
                })->count(),
                '70-79%' => $gradedSubmissions->filter(function($s) use ($homework) {
                    $percentage = ($s->score / $homework->max_score) * 100;
                    return $percentage >= 70 && $percentage < 80;
                })->count(),
                '60-69%' => $gradedSubmissions->filter(function($s) use ($homework) {
                    $percentage = ($s->score / $homework->max_score) * 100;
                    return $percentage >= 60 && $percentage < 70;
                })->count(),
                'Below 60%' => $gradedSubmissions->filter(function($s) use ($homework) {
                    return ($s->score / $homework->max_score) * 100 < 60;
                })->count(),
            ]
        ];

        return response()->json($stats);
    }

    /**
     * Get pending submissions for dashboard
     */
    public function pendingSubmissions()
    {
        $pendingSubmissions = HomeworkSubmission::where('status', 'submitted')
            ->with(['homework.course', 'user'])
            ->orderBy('submitted_at')
            ->limit(10)
            ->get();

        return response()->json($pendingSubmissions);
    }

    /**
     * Auto-apply late penalties
     */
    public function applyLatePenalties(Homework $homework)
    {
        if (!$homework->late_penalty_per_day) {
            return response()->json(['error' => 'No late penalty configured'], 400);
        }

        $lateSubmissions = $homework->submissions()
            ->where('submitted_at', '>', $homework->due_date)
            ->where('status', 'graded')
            ->get();

        $updatedCount = 0;

        foreach ($lateSubmissions as $submission) {
            $lateDays = $homework->due_date->diffInDays($submission->submitted_at);
            $penalty = min($lateDays * $homework->late_penalty_per_day, 100);
            $adjustedScore = max(0, $submission->score - ($submission->score * $penalty / 100));

            $submission->update([
                'score' => $adjustedScore,
                'feedback' => ($submission->feedback ?? '') . "\n\nLate penalty applied: -{$penalty}% ({$lateDays} days late)"
            ]);

            $updatedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Late penalties applied to {$updatedCount} submissions"
        ]);
    }

    /**
     * View homework analytics
     */
    public function analytics(Homework $homework)
    {
        $submissions = $homework->submissions()->with('user')->get();

        $stats = [
            'total_submissions' => $submissions->count(),
            'submitted' => $submissions->where('status', 'submitted')->count(),
            'graded' => $submissions->where('status', 'graded')->count(),
            'returned' => $submissions->where('status', 'returned')->count(),
            'late_submissions' => $submissions->where('is_late', true)->count(),
            'on_time_submissions' => $submissions->where('is_late', false)->count(),
            'average_score' => $submissions->where('status', 'graded')->avg('percentage_score') ?? 0,
            'highest_score' => $submissions->where('status', 'graded')->max('percentage_score') ?? 0,
            'lowest_score' => $submissions->where('status', 'graded')->min('percentage_score') ?? 0,
            'completion_rate' => $homework->total_assignments > 0
                ? round(($submissions->count() / $homework->total_assignments) * 100, 2)
                : 0,
        ];

        // Score distribution
        $scoreDistribution = [
            '90-100' => $submissions->where('status', 'graded')->whereBetween('percentage_score', [90, 100])->count(),
            '80-89' => $submissions->where('status', 'graded')->whereBetween('percentage_score', [80, 89.99])->count(),
            '70-79' => $submissions->where('status', 'graded')->whereBetween('percentage_score', [70, 79.99])->count(),
            '60-69' => $submissions->where('status', 'graded')->whereBetween('percentage_score', [60, 69.99])->count(),
            '50-59' => $submissions->where('status', 'graded')->whereBetween('percentage_score', [50, 59.99])->count(),
            '0-49' => $submissions->where('status', 'graded')->whereBetween('percentage_score', [0, 49.99])->count(),
        ];

        // Submission timeline
        $submissionTimeline = $submissions->groupBy(function ($submission) {
            return $submission->submitted_at->format('Y-m-d');
        })->map->count();

        return view('admin.homework.analytics', compact('homework', 'stats', 'scoreDistribution', 'submissionTimeline'));
    }

    /**
     * Import homework from Excel/CSV
     */
    public function import(Request $request)
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
                // For Excel files, you'll need to install and use a package like PhpSpreadsheet
                // For now, we'll return an error
                return redirect()->back()->with('error', 'Excel import not implemented yet. Please use CSV format.');
            }

            $imported = 0;
            $errors = [];

            foreach ($data as $row) {
                try {
                    $rowData = array_combine($headers, $row);

                    // Validate required fields
                    if (empty($rowData['name']) || empty($rowData['course_id']) || empty($rowData['instructor_id'])) {
                        $errors[] = "Row " . ($imported + 1) . ": Missing required fields";
                        continue;
                    }

                    // Create homework
                    Homework::create([
                        'name' => $rowData['name'],
                        'description' => $rowData['description'] ?? '',
                        'course_id' => $rowData['course_id'],
                        'instructor_id' => $rowData['instructor_id'],
                        'due_date' => $rowData['due_date'] ?? now()->addDays(7),
                        'max_score' => $rowData['max_score'] ?? 100,
                        'weight_percentage' => $rowData['weight_percentage'] ?? 10,
                        'is_published' => $rowData['is_published'] ?? false,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($imported + 1) . ": " . $e->getMessage();
                }
            }

            $message = "Successfully imported {$imported} homework assignments.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    /**
     * Export homework list to Excel/CSV
     */
    public function exportList(Request $request)
    {
        $format = $request->get('format', 'csv');

        $homework = Homework::with(['course', 'instructor', 'submissions'])
            ->orderByDesc('created_at')
            ->get();

        $filename = 'homework_' . date('Y-m-d_H-i-s') . '.' . $format;

        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($homework) {
                $file = fopen('php://output', 'w');

                // Add headers
                fputcsv($file, [
                    'ID', 'Name', 'Description', 'Course', 'Instructor',
                    'Due Date', 'Max Score', 'Weight (%)', 'Status', 'Submissions', 'Created At'
                ]);

                // Add data
                foreach ($homework as $hw) {
                    fputcsv($file, [
                        $hw->id,
                        $hw->name,
                        $hw->description,
                        $hw->course->name ?? 'N/A',
                        $hw->instructor->name ?? 'N/A',
                        $hw->due_date->format('Y-m-d H:i:s'),
                        $hw->max_score,
                        $hw->weight_percentage,
                        $hw->is_published ? 'Published' : 'Draft',
                        $hw->submissions->count(),
                        $hw->created_at->format('Y-m-d H:i:s'),
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Export format not supported');
    }

    /**
     * Export homework results
     */
    public function export(Homework $homework)
    {
        $submissions = $homework->submissions()->with(['user'])->get();

        $data = [];
        foreach ($submissions as $submission) {
            $data[] = [
                'Student Name' => $submission->user->name,
                'Email' => $submission->user->email,
                'Submitted At' => $submission->submitted_at->format('Y-m-d H:i:s'),
                'Is Late' => $submission->is_late ? 'Yes' : 'No',
                'Days Late' => $submission->days_late,
                'Score' => $submission->score_earned ?? 'Not graded',
                'Percentage' => $submission->percentage_score ?? 'Not graded',
                'Status' => ucfirst($submission->status),
                'Graded At' => $submission->graded_at ? $submission->graded_at->format('Y-m-d H:i:s') : 'Not graded',
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'filename' => 'homework_' . $homework->id . '_results.csv'
        ]);
    }

    /**
     * Update homework statistics
     */
    private function updateHomeworkStats(Homework $homework)
    {
        $submissions = $homework->submissions;

        $homework->update([
            'total_assignments' => $homework->course->students()->count(),
            'submitted_assignments' => $submissions->count(),
            'graded_assignments' => $submissions->where('is_graded', true)->count(),
            'average_score' => $submissions->where('is_graded', true)->avg('percentage_score') ?? 0,
            'on_time_submissions' => $submissions->where('is_late', false)->count(),
            'late_submissions' => $submissions->where('is_late', true)->count(),
        ]);
    }
}
