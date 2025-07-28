<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLecture;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    public function index(Request $request)
    {
        // Get courses with filters
        $query = Course::with(['category', 'instructor']);

        // Filter by instructor permissions
        $admin = auth('admin')->user();
        if ($admin->hasPermission('manage_own_courses') && !$admin->hasPermission('manage_courses')) {
            // Instructor can only see their own courses
            $query->where('instructor_id', $admin->id);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Apply instructor filter
        if ($request->filled('instructor')) {
            $query->where('instructor_id', $request->instructor);
        }

        $courses = $query->paginate(20);

                // Get statistics based on permissions
        if ($admin->hasPermission('manage_own_courses') && !$admin->hasPermission('manage_courses')) {
            // Instructor stats - only their own courses
            $stats = [
                'total_courses' => Course::where('instructor_id', $admin->id)->count(),
                'published_courses' => Course::where('instructor_id', $admin->id)->where('status', 'published')->count(),
                'draft_courses' => Course::where('instructor_id', $admin->id)->where('status', 'draft')->count(),
                'total_students' => User::count(), // All users are students
            ];
        } else {
            // Admin stats - all courses
            $stats = [
                'total_courses' => Course::count(),
                'published_courses' => Course::where('status', 'published')->count(),
                'draft_courses' => Course::where('status', 'draft')->count(),
                'total_students' => User::count(), // All users are students
            ];
        }

        // Get categories and instructors for filters based on permissions
        $categories = CourseCategory::all();
        if ($admin->hasPermission('manage_own_courses') && !$admin->hasPermission('manage_courses')) {
            // Instructor can only see themselves in the filter
            $instructors = collect([$admin]);
        } else {
            // Admin can see all instructors
            $instructors = Admin::whereHas('adminType', function($query) {
                $query->where('name', 'instructor');
            })->get();
        }

        return view('admin.courses.index', compact('courses', 'stats', 'categories', 'instructors'));
    }

    public function create()
    {
        $categories = CourseCategory::all();
        $instructors = Admin::whereHas('adminType', function($query) {
            $query->where('name', 'instructor');
        })->get();
        return view('admin.courses.create', compact('categories', 'instructors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:course_categories,id',
            'instructor_id' => 'required|exists:admins,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'book' => 'nullable|file|mimes:pdf|max:10240',
            'sections' => 'nullable|string', // JSON string
            'learn_items' => 'nullable|string', // JSON string
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses', 'public');
            $data['image'] = $imagePath;
        }

        // Handle book upload
        if ($request->hasFile('book')) {
            $bookPath = $request->file('book')->store('courses/books', 'public');
            $data['book'] = $bookPath;
        }

        // Handle boolean fields
        $data['is_featured'] = $request->has('featured');
        $data['is_free'] = $request->has('free');

        // Create course
        $course = Course::create([
            'name' => $data['title'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'instructor_id' => $data['instructor_id'],
            'price' => $data['price'],
            'duration' => $data['duration'],
            'status' => $data['status'],
            'image' => $data['image'] ?? null,
            'book' => $data['book'] ?? null,
            'is_featured' => $data['is_featured'],
            'is_free' => $data['is_free'],
        ]);

        // Handle learning items (what you'll learn)
        if ($request->filled('learn_items')) {
            $learnItems = json_decode($request->learn_items, true);
            if (is_array($learnItems)) {
                $course->update(['what_to_learn' => $learnItems]);
            }
        } elseif ($request->has('learning_objectives')) {
            // Handle learning objectives as array
            $course->update(['what_to_learn' => $request->learning_objectives]);
        }

        // Debug: Log the learning items
        Log::info('Learning items received:', [
            'learn_items' => $request->learn_items,
            'decoded' => $request->filled('learn_items') ? json_decode($request->learn_items, true) : null
        ]);

        // Handle sections and lectures
        if ($request->filled('sections')) {
            $sections = json_decode($request->sections, true);
            if (is_array($sections)) {
                foreach ($sections as $sectionData) {
                    $section = $course->sections()->create([
                        'title' => $sectionData['title'],
                        'description' => $sectionData['description'] ?? '',
                        'order' => $sectionData['order'] ?? 1,
                    ]);

                    // Handle lectures in this section
                    if (isset($sectionData['lectures']) && is_array($sectionData['lectures'])) {
                        foreach ($sectionData['lectures'] as $lectureData) {
                            $lecture = $section->lectures()->create([
                                'title' => $lectureData['title'],
                                'description' => $lectureData['description'] ?? '',
                                'content_type' => $lectureData['type'] === 'url' ? 'video' : 'document',
                                'video_url' => $lectureData['type'] === 'url' ? $lectureData['link'] : null,
                                'order' => $lectureData['order'] ?? 1,
                            ]);

                            // Handle lecture file upload
                            if (isset($lectureData['fileKey']) && $request->hasFile($lectureData['fileKey'])) {
                                $filePath = $request->file($lectureData['fileKey'])->store('lectures', 'public');
                                $lecture->update(['document_file' => $filePath]);
                            }

                            // Handle lecture book upload
                            if (isset($lectureData['bookKey']) && $request->hasFile($lectureData['bookKey'])) {
                                $bookPath = $request->file($lectureData['bookKey'])->store('lectures/books', 'public');
                                $lecture->update(['book' => $bookPath]);
                            }
                        }
                    }
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Course created successfully',
                'course_id' => $course->id
            ]);
        }

        return redirect()->route('admin.courses.index')->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load(['category', 'instructor', 'sections.lectures']);

        // Get course statistics
        $stats = [
            'total_sections' => $course->sections()->count(),
            'total_lectures' => $course->sections()->withCount('lectures')->get()->sum('lectures_count'),
            'total_quizzes' => $course->quizzes()->count(),
            'total_homework' => $course->homework()->count(),
            'total_live_classes' => $course->liveClasses()->count(),
        ];

        return view('admin.courses.show', compact('course', 'stats'));
    }

    public function edit(Course $course)
    {
        $categories = CourseCategory::all();
        $instructors = Admin::whereHas('adminType', function($query) {
            $query->where('name', 'instructor');
        })->get();
        $course->load(['category', 'instructor', 'sections.lectures']);
        return view('admin.courses.edit', compact('course', 'categories', 'instructors'));
    }

    public function update(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:course_categories,id',
            'instructor_id' => 'required|exists:admins,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'book' => 'nullable|file|mimes:pdf|max:10240',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'string',
            'lectures' => 'nullable|array',
            'lectures.*.title' => 'required|string',
            'lectures.*.description' => 'nullable|string',
            'lectures.*.video_url' => 'nullable|url',
            'lectures.*.file' => 'nullable|file',
            'lectures.*.book' => 'nullable|file|mimes:pdf',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($course->image && Storage::disk('public')->exists($course->image)) {
                Storage::disk('public')->delete($course->image);
            }

            $imagePath = $request->file('image')->store('courses', 'public');
            $data['image'] = $imagePath;
        }

        // Handle book upload
        if ($request->hasFile('book')) {
            // Delete old book if exists
            if ($course->book && Storage::disk('public')->exists($course->book)) {
                Storage::disk('public')->delete($course->book);
            }

            $bookPath = $request->file('book')->store('courses/books', 'public');
            $data['book'] = $bookPath;
        }

        // Handle boolean fields
        $data['is_featured'] = $request->has('featured');
        $data['is_free'] = $request->has('free');

        // Update course basic info
        $course->update([
            'name' => $data['title'],
            'description' => $data['description'],
            'category_id' => $data['category_id'],
            'instructor_id' => $data['instructor_id'],
            'price' => $data['price'],
            'duration' => $data['duration'],
            'status' => $data['status'],
            'image' => $data['image'] ?? $course->image,
            'book' => $data['book'] ?? $course->book,
            'is_featured' => $data['is_featured'],
            'is_free' => $data['is_free'],
        ]);

        // Handle learning objectives
        if ($request->has('learning_objectives')) {
            $course->update(['what_to_learn' => $request->learning_objectives]);
        }

        // Handle lecture updates
        if ($request->has('lectures')) {
            foreach ($request->lectures as $lectureId => $lectureData) {
                $lecture = CourseLecture::find($lectureId);
                if ($lecture && $lecture->section->course_id === $course->id) {
                    $lecture->update([
                        'title' => $lectureData['title'],
                        'description' => $lectureData['description'] ?? '',
                        'video_url' => $lectureData['video_url'] ?? null,
                    ]);

                    // Handle lecture file upload
                    if (isset($lectureData['file']) && $lectureData['file']) {
                        // Delete old file if exists
                        if ($lecture->document_file && Storage::disk('public')->exists($lecture->document_file)) {
                            Storage::disk('public')->delete($lecture->document_file);
                        }

                        $filePath = $lectureData['file']->store('lectures', 'public');
                        $lecture->update(['document_file' => $filePath]);
                    }

                    // Handle lecture book upload
                    if (isset($lectureData['book']) && $lectureData['book']) {
                        // Delete old book if exists
                        if ($lecture->book && Storage::disk('public')->exists($lecture->book)) {
                            Storage::disk('public')->delete($lecture->book);
                        }

                        $bookPath = $lectureData['book']->store('lectures/books', 'public');
                        $lecture->update(['book' => $bookPath]);
                    }
                }
            }
        }

        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }

    public function analytics(Course $course)
    {
        // Analytics logic here
        return view('admin.courses.analytics', compact('course'));
    }

    public function enrollments(Course $course)
    {
        // Get enrollment statistics
        $stats = [
            'total_enrollments' => $course->enrollments()->count(),
            'completed_enrollments' => $course->enrollments()->where('status', 'completed')->count(),
            'in_progress_enrollments' => $course->enrollments()->where('status', 'active')->count(),
            'average_progress' => round($course->enrollments()->avg('progress_percentage') ?? 0, 1),
        ];

        $enrollments = $course->enrollments()->with('user')->paginate(20);
        return view('admin.courses.enrollments', compact('course', 'enrollments', 'stats'));
    }

    public function exportEnrollments(Course $course)
    {
        $enrollments = $course->enrollments()->with('user')->get();

        $filename = 'enrollments_' . $course->slug . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
        ];

        $callback = function() use ($enrollments) {
            $file = fopen('php://output', 'w');

            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Add headers
            fputcsv($file, [
                'Student Name',
                'Email',
                'Status',
                'Progress (%)',
                'Enrolled Date',
                'Completed Date'
            ]);

            // Add data
            foreach ($enrollments as $enrollment) {
                fputcsv($file, [
                    $enrollment->user->name,
                    $enrollment->user->email,
                    $enrollment->status,
                    $enrollment->progress_percentage ?? 0,
                    $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i:s') : $enrollment->created_at->format('Y-m-d H:i:s'),
                    $enrollment->completed_at ? $enrollment->completed_at->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export(Request $request)
    {
        try {
            // Get courses with filters (same as index method)
            $query = Course::with(['category', 'instructor']);

            // Apply search filter
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
            }

            // Apply status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Apply category filter
            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            // Apply instructor filter
            if ($request->filled('instructor')) {
                $query->where('instructor_id', $request->instructor);
            }

            $courses = $query->get();

            // Generate CSV content
            $filename = 'courses_export_' . date('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, must-revalidate',
                'Pragma' => 'no-cache',
            ];

            $callback = function() use ($courses) {
                $file = fopen('php://output', 'w');

                // Add BOM for UTF-8
                fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

                // Add CSV headers
                fputcsv($file, [
                    'Name',
                    'Description',
                    'Category',
                    'Instructor',
                    'Price',
                    'Status',
                    'Featured',
                    'Free',
                    'Duration (minutes)',
                    'Difficulty Level'
                ]);

                // Add course data
                foreach ($courses as $course) {
                    fputcsv($file, [
                        $course->name,
                        strip_tags($course->description),
                        $course->category->name ?? '',
                        $course->instructor->name ?? '',
                        $course->price,
                        $course->status,
                        $course->is_featured ? 'Yes' : 'No',
                        $course->is_free ? 'Yes' : 'No',
                        $course->duration_minutes ?? '',
                        $course->difficulty_level ?? 'beginner'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Export failed: ' . $e->getMessage());
        }
    }

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

                    $courseData = array_combine($headers, $row);

                    // Validate required fields
                    if (empty($courseData['Name'])) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Course name is required";
                        continue;
                    }

                    // Find category by name (case insensitive)
                    $category = CourseCategory::whereRaw('LOWER(name) = ?', [strtolower($courseData['Category'] ?? '')])->first();
                    if (!$category) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Category '{$courseData['Category']}' not found";
                        continue;
                    }

                    // Find instructor by name (case insensitive)
                    $instructor = Admin::whereRaw('LOWER(name) = ?', [strtolower($courseData['Instructor'] ?? '')])
                                     ->whereHas('adminType', function($query) {
                                         $query->where('name', 'instructor');
                                     })
                                     ->first();
                    if (!$instructor) {
                        $errors[] = "Row " . ($rowIndex + 2) . ": Instructor '{$courseData['Instructor']}' not found";
                        continue;
                    }

                    // Create course with proper data handling
                    Course::create([
                        'name' => trim($courseData['Name']),
                        'description' => trim($courseData['Description'] ?? ''),
                        'category_id' => $category->id,
                        'instructor_id' => $instructor->id,
                        'price' => floatval($courseData['Price'] ?? 0),
                        'status' => strtolower($courseData['Status'] ?? 'draft'),
                        'is_featured' => strtolower($courseData['Featured'] ?? 'no') === 'yes',
                        'is_free' => strtolower($courseData['Free'] ?? 'no') === 'yes',
                        'duration_minutes' => !empty($courseData['Duration (minutes)']) ? intval($courseData['Duration (minutes)']) : null,
                        'difficulty_level' => strtolower($courseData['Difficulty Level'] ?? 'beginner'),
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Successfully imported {$imported} courses.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode(', ', $errors);
            }

            return redirect()->route('admin.courses.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()->route('admin.courses.index')
                ->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id'
        ]);

        $deleted = Course::whereIn('id', $request->course_ids)->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', "Successfully deleted {$deleted} courses.");
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
            'status' => 'required|in:published,draft,archived'
        ]);

        $updated = Course::whereIn('id', $request->course_ids)
            ->update(['status' => $request->status]);

        return redirect()->route('admin.courses.index')
            ->with('success', "Successfully updated status for {$updated} courses.");
    }

    public function downloadTemplate()
    {
        $filename = 'courses_import_template.csv';

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
                'Category',
                'Instructor',
                'Price',
                'Status',
                'Featured',
                'Free',
                'Duration (minutes)',
                'Difficulty Level'
            ]);

            // Add sample data
            fputcsv($file, [
                'Sample Course',
                'This is a sample course description',
                'Programming',
                'John Doe',
                '99.99',
                'draft',
                'No',
                'No',
                '120',
                'beginner'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
