<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseCategory;
use App\Models\Admin;
use App\Helpers\MultilingualHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MultilingualCourseController extends Controller
{
    /**
     * Show the form for creating a new course with multilingual support
     */
    public function create()
    {
        $categories = CourseCategory::active()->ordered()->get();
        $instructors = Admin::whereHas('adminType', function($q) {
            $q->where('name', 'instructor');
        })->active()->get();

        return view('admin.courses.create-multilingual-example', compact('categories', 'instructors'));
    }

    /**
     * Store a newly created course with multilingual support
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            // Basic fields
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'category_id' => 'required|exists:course_categories,id',
            'instructor_id' => 'required|exists:admins,id',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|string|max:255',

            // Multilingual learning objectives
            'what_to_learn' => 'nullable|array',
            'what_to_learn.*' => 'string|max:500',
            'what_to_learn_ar' => 'nullable|array',
            'what_to_learn_ar.*' => 'string|max:500',

            // Multilingual FAQ
            'faq_course' => 'nullable|array',
            'faq_course.*.question' => 'string|max:500',
            'faq_course.*.answer' => 'string|max:2000',
            'faq_course_ar' => 'nullable|array',
            'faq_course_ar.*.question' => 'string|max:500',
            'faq_course_ar.*.answer' => 'string|max:2000',

            // SEO fields
            'meta_title' => 'nullable|string|max:60',
            'meta_title_ar' => 'nullable|string|max:60',
            'meta_description' => 'nullable|string|max:160',
            'meta_description_ar' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'meta_keywords_ar' => 'nullable|string|max:255',
            'canonical_url' => 'nullable|url',
            'robots' => 'nullable|string|in:index,follow,index,nofollow,noindex,follow,noindex,nofollow',
            'default_language' => 'nullable|string|in:en,ar',

            // Open Graph fields
            'og_title' => 'nullable|string|max:60',
            'og_title_ar' => 'nullable|string|max:60',
            'og_description' => 'nullable|string|max:160',
            'og_description_ar' => 'nullable|string|max:160',
            'og_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Twitter Card fields
            'twitter_title' => 'nullable|string|max:60',
            'twitter_title_ar' => 'nullable|string|max:60',
            'twitter_description' => 'nullable|string|max:160',
            'twitter_description_ar' => 'nullable|string|max:160',
            'twitter_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Course settings
            'is_featured' => 'boolean',
            'is_free' => 'boolean',
            'status' => 'required|in:draft,published',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Generate slug based on default language
        $defaultLang = $validated['default_language'] ?? 'en';
        $validated['slug'] = MultilingualHelper::generateSlug(
            $validated['name'],
            $validated['name_ar'] ?? null,
            $defaultLang
        );

        // Handle file uploads
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('courses', 'public');
        }

        if ($request->hasFile('og_image')) {
            $validated['og_image'] = $request->file('og_image')->store('seo/og', 'public');
        }

        if ($request->hasFile('twitter_image')) {
            $validated['twitter_image'] = $request->file('twitter_image')->store('seo/twitter', 'public');
        }

        // Set supported languages
        $supportedLanguages = ['en'];
        if (!empty($validated['name_ar']) || !empty($validated['description_ar'])) {
            $supportedLanguages[] = 'ar';
        }
        $validated['supported_languages'] = $supportedLanguages;

        // Create the course
        $course = Course::create($validated);

        // Generate structured data
        $this->generateStructuredData($course);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully with multilingual support!');
    }

    /**
     * Show the form for editing a course with multilingual support
     */
    public function edit(Course $course)
    {
        $categories = CourseCategory::active()->ordered()->get();
        $instructors = Admin::whereHas('adminType', function($q) {
            $q->where('name', 'instructor');
        })->active()->get();

        return view('admin.courses.edit-multilingual', compact('course', 'categories', 'instructors'));
    }

    /**
     * Update a course with multilingual support
     */
    public function update(Request $request, Course $course)
    {
        // Similar validation as store method
        $validated = $request->validate([
            // ... same validation rules as store method
        ]);

        // Handle file uploads
        if ($request->hasFile('image')) {
            // Delete old image
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
            $validated['image'] = $request->file('image')->store('courses', 'public');
        }

        if ($request->hasFile('og_image')) {
            if ($course->og_image) {
                Storage::disk('public')->delete($course->og_image);
            }
            $validated['og_image'] = $request->file('og_image')->store('seo/og', 'public');
        }

        if ($request->hasFile('twitter_image')) {
            if ($course->twitter_image) {
                Storage::disk('public')->delete($course->twitter_image);
            }
            $validated['twitter_image'] = $request->file('twitter_image')->store('seo/twitter', 'public');
        }

        // Update supported languages
        $supportedLanguages = ['en'];
        if (!empty($validated['name_ar']) || !empty($validated['description_ar'])) {
            $supportedLanguages[] = 'ar';
        }
        $validated['supported_languages'] = $supportedLanguages;

        // Update the course
        $course->update($validated);

        // Regenerate structured data
        $this->generateStructuredData($course);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully!');
    }

    /**
     * Generate structured data for SEO
     */
    private function generateStructuredData(Course $course)
    {
        $structuredData = [
            '@context' => 'https://schema.org',
            '@type' => 'Course',
            'name' => $course->name,
            'description' => $course->description,
            'provider' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'url' => config('app.url'),
            ],
            'instructor' => [
                '@type' => 'Person',
                'name' => $course->instructor->name,
            ],
            'courseMode' => 'online',
            'educationalLevel' => 'beginner',
            'isAccessibleForFree' => $course->is_free,
        ];

        if ($course->price > 0) {
            $structuredData['offers'] = [
                '@type' => 'Offer',
                'price' => $course->price,
                'priceCurrency' => 'USD',
            ];
        }

        if ($course->image) {
            $structuredData['image'] = asset('storage/' . $course->image);
        }

        // Arabic structured data
        if ($course->name_ar && $course->description_ar) {
            $structuredDataAr = $structuredData;
            $structuredDataAr['name'] = $course->name_ar;
            $structuredDataAr['description'] = $course->description_ar;
            $structuredDataAr['inLanguage'] = 'ar';

            $course->update(['structured_data_ar' => $structuredDataAr]);
        }

        $course->update(['structured_data' => $structuredData]);
    }

    /**
     * Display course content in specific language
     */
    public function showInLanguage(Course $course, string $locale = 'en')
    {
        // Validate locale
        if (!in_array($locale, $course->available_languages)) {
            $locale = $course->default_language;
        }

        // Get localized content
        $localizedCourse = [
            'name' => $course->getFieldInLanguage('name', $locale),
            'description' => $course->getFieldInLanguage('description', $locale),
            'what_to_learn' => $course->getArrayFieldInLanguage('what_to_learn', $locale),
            'faq_course' => $course->getArrayFieldInLanguage('faq_course', $locale),
            'meta_title' => $course->getFieldInLanguage('meta_title', $locale),
            'meta_description' => $course->getFieldInLanguage('meta_description', $locale),
        ];

        // Get SEO meta
        $seoMeta = MultilingualHelper::getSeoMeta($course, $locale);
        $hreflangs = MultilingualHelper::generateHreflangTags($course, route('courses.show', $course->slug));

        return view('courses.show-multilingual', compact('course', 'localizedCourse', 'seoMeta', 'hreflangs', 'locale'));
    }
}
