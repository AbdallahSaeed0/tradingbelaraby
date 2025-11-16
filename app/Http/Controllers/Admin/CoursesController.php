<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use App\Models\User;
use App\Models\Admin;
use App\Http\Controllers\Admin\CourseDuplicateService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class CoursesController extends Controller
{
    protected CourseDuplicateService $duplicateService;

    public function __construct(CourseDuplicateService $duplicateService)
    {
        $this->duplicateService = $duplicateService;
    }

    public function index(Request $request)
    {
        // Get courses with filters
        $query = Course::with(['category', 'instructor', 'instructors', 'enrollments'])
                       ->withCount('enrollments');

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

        // Pagination size (default 10, user-controllable)
        $perPage = (int) $request->get('per_page', 10);
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $courses = $query->paginate($perPage)->appends($request->query());

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

    /**
     * Duplicate a course and redirect to the edit page for the new copy.
     */
    public function duplicate(Course $course)
    {
        $newCourse = $this->duplicateService->duplicate($course);

        return redirect()
            ->route('admin.courses.edit', $newCourse)
            ->with('success', 'Course duplicated successfully. You are now editing the copy.');
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
            // Basic fields
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'requirements' => 'nullable|string',
            'requirements_ar' => 'nullable|string',
            'category_id' => 'required|exists:course_categories,id',
            'instructor_id' => 'nullable|exists:admins,id',
            'instructor_ids' => 'required|array|min:1',
            'instructor_ids.*' => 'required|exists:admins,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'preview_video_url' => 'nullable|string|url',
            'book' => 'nullable|file|mimes:pdf|max:10240',
            'remove_book' => 'nullable|boolean',

            // Multilingual learning objectives
            'what_to_learn' => 'nullable|array',
            'what_to_learn.*' => 'string|max:500',
            'what_to_learn_ar' => 'nullable|array',
            'what_to_learn_ar.*' => 'nullable|string|max:500',

            // Multilingual FAQ
            'faq_course' => 'nullable|array',
            'faq_course.*.question' => 'nullable|string|max:500',
            'faq_course.*.answer' => 'nullable|string',
            'faq_course_ar' => 'nullable|array',
            'faq_course_ar.*.question' => 'nullable|string|max:500',
            'faq_course_ar.*.answer' => 'nullable|string',

            // SEO fields
            'meta_title' => 'nullable|string|max:60',
            'meta_title_ar' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_description_ar' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_keywords_ar' => 'nullable|string|max:255',
            'default_language' => 'nullable|string|in:en,ar',


            // Course settings
            'is_featured' => 'boolean',
            'show_in_top_discounted' => 'boolean',
            'show_in_subscription_bundles' => 'boolean',
            'show_in_live_meeting' => 'boolean',
            'show_in_recent_courses' => 'boolean',
            'is_free' => 'boolean',
            'sections' => 'nullable|string', // JSON string
            'learn_items' => 'nullable|string', // JSON string
        ]);

        // Set the first instructor as the main instructor_id for legacy compatibility
        if (!empty($request->instructor_ids)) {
            $data['instructor_id'] = $request->instructor_ids[0];
        }

        // Ensure is_free is always set explicitly (unchecked checkbox won't be present in the request)
        if (!$request->has('is_free')) {
            $data['is_free'] = false;
        }

        // If course is free, set price to 0
        if ($request->has('is_free') && $request->is_free) {
            $data['price'] = 0;
        }

        // Handle file uploads
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        if ($request->hasFile('book')) {
            $data['book'] = $request->file('book')->store('courses/books', 'public');
        }


        // Set supported languages
        $supportedLanguages = ['en'];
        if (!empty($data['name_ar']) || !empty($data['description_ar'])) {
            $supportedLanguages[] = 'ar';
        }
        $data['supported_languages'] = $supportedLanguages;

        // Set default language
        $data['default_language'] = $data['default_language'] ?? 'en';

        // Clean up learning objectives arrays - remove null values
        if (isset($data['what_to_learn_ar']) && is_array($data['what_to_learn_ar'])) {
            $data['what_to_learn_ar'] = array_filter($data['what_to_learn_ar'], function($value) {
                return $value !== null && $value !== '';
            });
        }

        if (isset($data['what_to_learn']) && is_array($data['what_to_learn'])) {
            $data['what_to_learn'] = array_filter($data['what_to_learn'], function($value) {
                return $value !== null && $value !== '';
            });
        }

        // Create course
        $course = Course::create($data);

        // Sync instructors (many-to-many relationship)
        if (!empty($request->instructor_ids)) {
            $course->instructors()->sync($request->instructor_ids);
        }

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


        // Handle sections and lectures
        if ($request->filled('sections')) {
            $sections = json_decode($request->sections, true);
            if (is_array($sections)) {
                foreach ($sections as $sectionData) {
                    $section = $course->sections()->create([
                        'title' => $sectionData['title'],
                        'title_ar' => $sectionData['title_ar'] ?? null,
                        'description' => $sectionData['description'] ?? '',
                        'description_ar' => $sectionData['description_ar'] ?? null,
                        'order' => $sectionData['order'] ?? 1,
                    ]);

                    // Handle lectures in this section
                    if (isset($sectionData['lectures']) && is_array($sectionData['lectures'])) {
                        foreach ($sectionData['lectures'] as $lectureData) {
                            $lecture = $section->lectures()->create([
                                'course_id' => $course->id,
                                'title' => $lectureData['title'],
                                'title_ar' => $lectureData['title_ar'] ?? null,
                                'description' => $lectureData['description'] ?? '',
                                'description_ar' => $lectureData['description_ar'] ?? null,
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
        $course->load(['category', 'instructor', 'sections.lectures', 'enrollments.user']);

        // Get course statistics
        $stats = [
            'total_sections' => $course->sections()->count(),
            'total_lectures' => $course->sections()->withCount('lectures')->get()->sum('lectures_count'),
            'total_quizzes' => $course->quizzes()->count(),
            'total_homework' => $course->homework()->count(),
            'total_live_classes' => $course->liveClasses()->count(),
            'total_enrollments' => $course->enrollments()->count(),
        ];

        // Get recent activity
        $recentEnrollments = $course->enrollments()
            ->with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.courses.show', compact('course', 'stats', 'recentEnrollments'));
    }

    public function edit(Course $course)
    {
        $categories = CourseCategory::all();
        $instructors = Admin::whereHas('adminType', function($query) {
            $query->where('name', 'instructor');
        })->get();
        $course->load(['category', 'instructor', 'instructors', 'sections.lectures']);
        return view('admin.courses.edit', compact('course', 'categories', 'instructors'));
    }

    public function update(Request $request, Course $course)
    {
        try {
            $data = $request->validate([
            // Basic fields
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'requirements' => 'nullable|string',
            'requirements_ar' => 'nullable|string',
            'category_id' => 'required|exists:course_categories,id',
            'instructor_id' => 'nullable|exists:admins,id',
            'instructor_ids' => 'required|array|min:1',
            'instructor_ids.*' => 'required|exists:admins,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string',
            'status' => 'required|in:draft,published,archived',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'preview_video_url' => 'nullable|string|url',
            'book' => 'nullable|file|mimes:pdf|max:10240',

            // Multilingual learning objectives
            'what_to_learn' => 'nullable|array',
            'what_to_learn.*' => 'string|max:500',
            'what_to_learn_ar' => 'nullable|array',
            'what_to_learn_ar.*' => 'nullable|string|max:500',

            // Multilingual FAQ
            'faq_course' => 'nullable|array',
            'faq_course.*.question' => 'nullable|string|max:500',
            'faq_course.*.answer' => 'nullable|string',
            'faq_course_ar' => 'nullable|array',
            'faq_course_ar.*.question' => 'nullable|string|max:500',
            'faq_course_ar.*.answer' => 'nullable|string',

            // SEO fields
            'meta_title' => 'nullable|string|max:60',
            'meta_title_ar' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_description_ar' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_keywords_ar' => 'nullable|string|max:255',
            'default_language' => 'nullable|string|in:en,ar',


            // Course settings
            'is_featured' => 'boolean',
            'show_in_top_discounted' => 'boolean',
            'show_in_subscription_bundles' => 'boolean',
            'show_in_live_meeting' => 'boolean',
            'show_in_recent_courses' => 'boolean',
            'is_free' => 'boolean',
            'learning_objectives' => 'nullable|array',
            'learning_objectives.*' => 'string',
            'sections' => 'nullable|array',
            'sections.*.title' => 'required|string',
            'sections.*.title_ar' => 'nullable|string',
            'sections.*.description' => 'nullable|string',
            'sections.*.description_ar' => 'nullable|string',
            'lectures' => 'nullable|array',
            'lectures.*.title' => 'required|string',
            'lectures.*.title_ar' => 'nullable|string',
            'lectures.*.description' => 'nullable|string',
            'lectures.*.description_ar' => 'nullable|string',
            'lectures.*.video_url' => 'nullable|url',
            'lectures.*.file' => 'nullable|file',
            'lectures.*.book' => 'nullable|file|mimes:pdf',
            'deleted_sections' => 'nullable|array',
            'deleted_sections.*' => 'integer|exists:course_sections,id',
            'deleted_lectures' => 'nullable|array',
            'deleted_lectures.*' => 'integer|exists:course_lectures,id',
        ]);

        // Set the first instructor as the main instructor_id for legacy compatibility
        if (!empty($request->instructor_ids)) {
            $data['instructor_id'] = $request->instructor_ids[0];
        }

        // Ensure is_free is always set explicitly on create as well
        if (!$request->has('is_free')) {
            $data['is_free'] = false;
        }

        // If course is free, set price to 0
        if ($request->has('is_free') && $request->is_free) {
            $data['price'] = 0;
        }

        $removeExistingBook = $request->boolean('remove_book');
        unset($data['remove_book']);

        // Handle file uploads
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($course->image && Storage::disk('public')->exists($course->image)) {
                Storage::disk('public')->delete($course->image);
            }
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        if ($request->hasFile('book')) {
            // Delete old book if exists
            if ($course->book && Storage::disk('public')->exists($course->book)) {
                Storage::disk('public')->delete($course->book);
            }
            $data['book'] = $request->file('book')->store('courses/books', 'public');
        } elseif ($removeExistingBook && $course->book) {
            if (Storage::disk('public')->exists($course->book)) {
                Storage::disk('public')->delete($course->book);
            }
            $data['book'] = null;
        }


        // Set supported languages
        $supportedLanguages = ['en'];
        if (!empty($data['name_ar']) || !empty($data['description_ar'])) {
            $supportedLanguages[] = 'ar';
        }
        $data['supported_languages'] = $supportedLanguages;

        // Set default language
        $data['default_language'] = $data['default_language'] ?? 'en';


        // Clean up learning objectives arrays - remove null values
        if (isset($data['what_to_learn_ar']) && is_array($data['what_to_learn_ar'])) {
            $data['what_to_learn_ar'] = array_filter($data['what_to_learn_ar'], function($value) {
                return $value !== null && $value !== '';
            });
        }

        if (isset($data['what_to_learn']) && is_array($data['what_to_learn'])) {
            $data['what_to_learn'] = array_filter($data['what_to_learn'], function($value) {
                return $value !== null && $value !== '';
            });
        }


        // Update course
        $course->update($data);

        // Sync instructors (many-to-many relationship)
        if (!empty($request->instructor_ids)) {
            $course->instructors()->sync($request->instructor_ids);
        }

        // Handle learning objectives - they are already processed in the main update above
        // The what_to_learn and what_to_learn_ar arrays are already included in $data and updated

        // Handle deletions first
        if ($request->has('deleted_sections')) {
            Log::info('Deleting sections:', $request->deleted_sections);
            foreach ($request->deleted_sections as $sectionId) {
                $section = \App\Models\CourseSection::find($sectionId);
                if ($section && $section->course_id === $course->id) {
                    // Delete all lectures in this section first
                    $section->lectures()->delete();
                    // Then delete the section
                    $section->delete();
                    Log::info("Section {$sectionId} and its lectures deleted successfully");
                }
            }
        }

        if ($request->has('deleted_lectures')) {
            Log::info('Deleting lectures:', $request->deleted_lectures);
            foreach ($request->deleted_lectures as $lectureId) {
                $lecture = \App\Models\CourseLecture::find($lectureId);
                if ($lecture && $lecture->section->course_id === $course->id) {
                    // Delete associated files
                    if ($lecture->document_file && Storage::disk('public')->exists($lecture->document_file)) {
                        Storage::disk('public')->delete($lecture->document_file);
                    }
                    if ($lecture->book && Storage::disk('public')->exists($lecture->book)) {
                        Storage::disk('public')->delete($lecture->book);
                    }
                    $lecture->delete();
                    Log::info("Lecture {$lectureId} deleted successfully");
                }
            }
        }

        // Handle section updates
        Log::info('Form submitted successfully');
        Log::info('Request has sections:', ['has_sections' => $request->has('sections')]);
        Log::info('Request has lectures:', ['has_lectures' => $request->has('lectures')]);

        // Initialize array to track new section IDs
        $newSectionIds = [];

        if ($request->has('sections')) {
            Log::info('Section data received:', $request->sections);

            foreach ($request->sections as $sectionId => $sectionData) {
                Log::info("Processing section: {$sectionId}", $sectionData);

                if (strpos($sectionId, 'new_') === 0) {
                    // Handle new sections
                    Log::info("Creating new section with data:", $sectionData);
                    $newSection = $course->sections()->create([
                        'title' => $sectionData['title'],
                        'title_ar' => $sectionData['title_ar'] ?? null,
                        'description' => $sectionData['description'] ?? '',
                        'description_ar' => $sectionData['description_ar'] ?? null,
                        'order' => $course->sections()->count() + 1
                    ]);
                    Log::info("New section created with ID: {$newSection->id}");

                    // Store the new section ID for lecture processing
                    $newSectionIds[$sectionId] = $newSection->id;
                } else {
                    // Handle existing sections
                    $section = \App\Models\CourseSection::find($sectionId);
                    if ($section && $section->course_id === $course->id) {
                        Log::info("Updating existing section {$sectionId} with data:", $sectionData);
                        $section->update([
                            'title' => $sectionData['title'],
                            'title_ar' => $sectionData['title_ar'] ?? null,
                            'description' => $sectionData['description'] ?? '',
                            'description_ar' => $sectionData['description_ar'] ?? null,
                        ]);
                        Log::info("Section {$sectionId} updated successfully");
                    }
                }
            }
        }

        if ($request->has('lectures')) {
            Log::info('Lecture data received:', $request->lectures);
            Log::info('All request data:', $request->all());

            // Debug: Check if we have any lectures data
            if (empty($request->lectures)) {
                Log::warning('No lectures data found in request');
                return redirect()->back()->with('error', 'No lecture data received');
            }

            // First, update the order of all lectures based on their position in the form
            $lectureOrder = 1;
            foreach ($request->lectures as $lectureId => $lectureData) {
                if (strpos($lectureId, 'new_') === 0) {
                    // For new lectures, we'll handle order when creating them
                    continue;
                } else {
                    // Update existing lecture order
                    $lecture = CourseLecture::find($lectureId);
                    if ($lecture && $lecture->section->course_id === $course->id) {
                        $lecture->update(['order' => $lectureOrder]);
                        $lectureOrder++;
                    }
                }
            }

            // Then handle the actual lecture data updates
            foreach ($request->lectures as $lectureId => $lectureData) {
                Log::info("Processing lecture: {$lectureId}", $lectureData);

                if (strpos($lectureId, 'new_') === 0) {
                    // Handle new lectures - we need to create them
                    $section = null;

                    // Check if lecture has a section_id
                    if (isset($lectureData['section_id'])) {
                        $sectionId = $lectureData['section_id'];

                        // Check if it's a new section ID
                        if (strpos($sectionId, 'new_') === 0 && isset($newSectionIds[$sectionId])) {
                            $section = \App\Models\CourseSection::find($newSectionIds[$sectionId]);
                        } else {
                            // It's an existing section ID
                            $section = \App\Models\CourseSection::find($sectionId);
                        }
                    }

                    // If no section found, use the first available section or create one
                    if (!$section) {
                        $section = $course->sections()->first();
                        if (!$section) {
                            $section = $course->sections()->create([
                                'title' => 'General',
                                'title_ar' => null,
                                'description' => 'Course content',
                                'description_ar' => null,
                                'order' => 1
                            ]);
                        }
                    }

                    // Create new lecture
                    Log::info("Creating new lecture with data:", $lectureData);
                    $newLecture = $section->lectures()->create([
                        'course_id' => $course->id,
                        'title' => $lectureData['title'],
                        'title_ar' => $lectureData['title_ar'] ?? null,
                        'description' => $lectureData['description'] ?? '',
                        'description_ar' => $lectureData['description_ar'] ?? null,
                        'video_url' => $lectureData['video_url'] ?? null,
                        'content_type' => isset($lectureData['video_url']) && $lectureData['video_url'] ? 'video' : 'document',
                        'order' => isset($lectureData['order']) ? $lectureData['order'] : ($section->lectures()->count() + 1)
                    ]);
                    Log::info("New lecture created with ID: {$newLecture->id}");

                    // Handle lecture file upload for new lecture
                    if (isset($lectureData['file']) && $lectureData['file']) {
                        $filePath = $lectureData['file']->store('lectures', 'public');
                        $newLecture->update(['document_file' => $filePath]);
                    }

                    // Handle lecture book upload for new lecture
                    if (isset($lectureData['book']) && $lectureData['book']) {
                        $bookPath = $lectureData['book']->store('lectures/books', 'public');
                        $newLecture->update(['book' => $bookPath]);
                    }
                } else {
                    // Handle existing lectures
                    $lecture = CourseLecture::find($lectureId);
                    if ($lecture && $lecture->section->course_id === $course->id) {
                        Log::info("Updating existing lecture {$lectureId} with data:", $lectureData);
                        $lecture->update([
                            'title' => $lectureData['title'],
                            'title_ar' => $lectureData['title_ar'] ?? null,
                            'description' => $lectureData['description'] ?? '',
                            'description_ar' => $lectureData['description_ar'] ?? null,
                            'video_url' => $lectureData['video_url'] ?? null,
                            'content_type' => isset($lectureData['video_url']) && $lectureData['video_url'] ? 'video' : 'document',
                        ]);
                        Log::info("Lecture {$lectureId} updated successfully");

                        // Handle lecture file upload
                        if (isset($lectureData['file']) && $lectureData['file']) {
                            // Delete old file if exists
                            if ($lecture->document_file && Storage::disk('public')->exists($lecture->document_file)) {
                                Storage::disk('public')->delete($lecture->document_file);
                            }

                            $filePath = $lectureData['file']->store('lectures', 'public');
                            $lecture->update(['document_file' => $filePath]);
                        }
                        // Note: If no new file is uploaded, the existing file is preserved

                        // Handle lecture book upload
                        if (isset($lectureData['book']) && $lectureData['book']) {
                            // Delete old book if exists
                            if ($lecture->book && Storage::disk('public')->exists($lecture->book)) {
                                Storage::disk('public')->delete($lecture->book);
                            }

                            $bookPath = $lectureData['book']->store('lectures/books', 'public');
                            $lecture->update(['book' => $bookPath]);
                        }
                        // Note: If no new book is uploaded, the existing book is preserved
                    }
                }
            }
        }


        return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed:', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating course:', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Error updating course: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully.');
    }

    public function analytics(Course $course)
    {
        // Get comprehensive analytics data
        $totalEnrollments = $course->enrollments()->count();

        $analytics = [
            // Basic metrics
            'total_enrollments' => $totalEnrollments,
            'active' => $course->enrollments()->where('status', 'active')->count(),
            'completed' => $course->enrollments()->where('status', 'completed')->count(),
            'dropped' => $course->enrollments()->where('status', 'dropped')->count(),
            'not_started' => $course->enrollments()->where('progress_percentage', 0)->count(),
            'in_progress' => $course->enrollments()->where('progress_percentage', '>', 0)->where('status', '!=', 'completed')->count(),
            'avg_progress' => round($course->enrollments()->avg('progress_percentage') ?? 0, 1),
            'avg_rating' => round($course->ratings()->avg('rating') ?? 0, 1),
            'total_ratings' => $course->ratings()->count(),
            'completion_rate' => $totalEnrollments > 0
                ? round(($course->enrollments()->where('status', 'completed')->count() / $totalEnrollments) * 100, 1)
                : 0,

            // Recent activity
            'recent_enrollments' => $course->enrollments()->with('user')->latest()->take(5)->get(),
            'recent_completions' => $course->enrollments()->with('user')->where('status', 'completed')->latest()->take(5)->get(),
            'recent_ratings' => $course->ratings()->with('user')->latest()->take(5)->get(),

            // Top lectures (mock data for now - requires lecture completion tracking)
            'top_lectures' => collect(),

            // Student engagement
            'active_students' => $course->enrollments()->where('last_accessed_at', '>=', now()->subWeek())->count(),
            'avg_time_spent' => 0, // Would need tracking implementation
            'quiz_attempts' => $course->quizzes()->withCount('attempts')->get()->sum('attempts_count') ?? 0,
            'quiz_pass_rate' => 0, // Would need pass/fail tracking
            'homework_submissions' => $course->homework()->withCount('submissions')->get()->sum('submissions_count') ?? 0,
            'homework_pass_rate' => 0, // Would need pass/fail tracking
            'avg_session_duration' => 0, // Would need session tracking
            'total_watch_time' => 0, // Would need video tracking

            // Course content
            'total_sections' => $course->sections()->count(),
            'total_lectures' => $course->sections()->withCount('lectures')->get()->sum('lectures_count'),
            'total_quizzes' => $course->quizzes()->count(),
            'total_homework' => $course->homework()->count(),
            'avg_lecture_completion_rate' => 0, // Would need lecture completion tracking

            // Revenue analytics
            'total_revenue' => $totalEnrollments * $course->price,
            'monthly_revenue' => $course->enrollments()->where('created_at', '>=', now()->startOfMonth())->count() * $course->price,
            'avg_revenue_per_student' => $course->price,

            // Enrollment trends (last 12 months)
            'enrollment_trends' => $this->getEnrollmentTrends($course),
        ];

        return view('admin.courses.analytics', compact('course', 'analytics'));
    }

    /**
     * Get enrollment trends for the last 12 months
     */
    private function getEnrollmentTrends($course)
    {
        $trends = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = $course->enrollments()
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $trends[] = $count;
        }
        return $trends;
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
