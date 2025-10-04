# Multilingual Courses Implementation

This document describes the implementation of Arabic and English multilingual support for courses, sections, and lectures in the Laravel application.

## Overview

The multilingual system allows you to create courses with content in both Arabic and English languages, including:

-   Course titles and descriptions
-   Section titles and descriptions
-   Lecture titles and descriptions
-   Learning objectives
-   FAQ content
-   SEO metadata
-   Open Graph and Twitter Card data

## Database Changes

### Courses Table

Added the following multilingual fields:

-   `name_ar` - Arabic course title
-   `description_ar` - Arabic course description
-   `what_to_learn_ar` - Arabic learning objectives (JSON)
-   `faq_course_ar` - Arabic FAQ content (JSON)
-   `certificate_text_ar` - Arabic certificate text

### SEO Fields

-   `meta_title_ar` - Arabic meta title
-   `meta_description_ar` - Arabic meta description
-   `meta_keywords_ar` - Arabic meta keywords
-   `canonical_url` - Canonical URL
-   `robots` - Robots directive
-   `og_title`, `og_title_ar` - Open Graph titles
-   `og_description`, `og_description_ar` - Open Graph descriptions
-   `og_image` - Open Graph image
-   `twitter_title`, `twitter_title_ar` - Twitter card titles
-   `twitter_description`, `twitter_description_ar` - Twitter card descriptions
-   `twitter_image` - Twitter card image
-   `structured_data`, `structured_data_ar` - JSON-LD structured data
-   `default_language` - Default language (en/ar)
-   `supported_languages` - Array of supported languages

### Course Sections Table

-   `title_ar` - Arabic section title
-   `description_ar` - Arabic section description
-   `learning_objectives`, `learning_objectives_ar` - Learning objectives (JSON)
-   `resources`, `resources_ar` - Section resources (JSON)
-   `section_type` - Section type (theory, practice, assessment, etc.)
-   `difficulty_level` - Difficulty level (beginner, intermediate, advanced)

### Course Lectures Table

-   `title_ar` - Arabic lecture title
-   `description_ar` - Arabic lecture description
-   `content_text_ar` - Arabic content text
-   `notes_ar` - Arabic instructor notes
-   `learning_objectives`, `learning_objectives_ar` - Learning objectives (JSON)
-   `lecture_resources`, `lecture_resources_ar` - Lecture resources (JSON)
-   `lecture_type` - Lecture type (theory, demo, exercise, quiz, etc.)
-   `difficulty_level` - Difficulty level
-   `prerequisites`, `prerequisites_ar` - Prerequisites (JSON)
-   `tags`, `tags_ar` - Lecture tags (JSON)
-   `transcript`, `transcript_ar` - Video transcripts
-   `subtitles`, `subtitles_ar` - Subtitles data (JSON)

## Model Updates

### Course Model

The Course model now includes:

-   New fillable fields for all multilingual content
-   Helper methods for getting localized content:
    -   `getLocalizedNameAttribute()`
    -   `getLocalizedDescriptionAttribute()`
    -   `getLocalizedWhatToLearnAttribute()`
    -   `getLocalizedFaqCourseAttribute()`
    -   `getLocalizedMetaTitleAttribute()`
    -   `getLocalizedMetaDescriptionAttribute()`
    -   `getLocalizedMetaKeywordsAttribute()`
-   Language support methods:
    -   `supportsLanguage(string $language)`
    -   `getAvailableLanguagesAttribute()`

### CourseSection Model

-   New fillable fields for multilingual content
-   Helper methods for localized content
-   Difficulty level and section type support
-   Badge color helpers for UI display

### CourseLecture Model

-   New fillable fields for multilingual content
-   Helper methods for localized content
-   Lecture type and difficulty level support
-   Content type icons for UI display

## Usage Examples

### Creating a Course with Multilingual Content

```php
use App\Models\Course;

$course = Course::create([
    'name' => 'Web Development Basics',
    'name_ar' => 'أساسيات تطوير الويب',
    'description' => 'Learn the fundamentals of web development...',
    'description_ar' => 'تعلم أساسيات تطوير الويب...',
    'what_to_learn' => [
        'HTML and CSS fundamentals',
        'JavaScript basics',
        'Responsive design'
    ],
    'what_to_learn_ar' => [
        'أساسيات HTML و CSS',
        'أساسيات JavaScript',
        'التصميم المتجاوب'
    ],
    'default_language' => 'en',
    'supported_languages' => ['en', 'ar'],
    // ... other fields
]);
```

### Getting Localized Content

```php
// Get localized name based on current locale
$localizedName = $course->localized_name;

// Get localized description
$localizedDescription = $course->localized_description;

// Get localized learning objectives
$localizedObjectives = $course->localized_what_to_learn;

// Check if course supports Arabic
if ($course->supportsLanguage('ar')) {
    // Show Arabic content
}

// Get available languages
$languages = $course->available_languages; // ['en', 'ar']
```

### Using the Multilingual Helper

```php
use App\Helpers\MultilingualHelper;

// Generate slug for multilingual content
$slug = MultilingualHelper::generateSlug($title, $titleAr, 'en');

// Get SEO meta data
$seoMeta = MultilingualHelper::getSeoMeta($course, 'ar');

// Get language direction for RTL support
$direction = MultilingualHelper::getLanguageDirection('ar'); // 'rtl'

// Format content for display
$formattedContent = MultilingualHelper::formatMultilingualContent($course, 'description', 'ar');
```

## View Components

### Multilingual Fields Partial

Use the `multilingual-fields.blade.php` partial to create forms with language tabs:

```blade
@include('admin.courses.partials.multilingual-fields', [
    'fieldName' => 'name',
    'label' => 'Course Title',
    'type' => 'input',
    'required' => true,
    'placeholder' => 'Enter course title',
    'value' => $course->name ?? '',
    'valueAr' => $course->name_ar ?? ''
])
```

### SEO Fields Partial

Use the `seo-fields.blade.php` partial to add SEO configuration:

```blade
@include('admin.courses.partials.seo-fields', ['course' => $course])
```

## Controller Implementation

### Handling Multilingual Data

```php
public function store(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'name_ar' => 'nullable|string|max:255',
        'description' => 'required|string',
        'description_ar' => 'nullable|string',
        // ... other validation rules
    ]);

    // Generate slug based on default language
    $validated['slug'] = MultilingualHelper::generateSlug(
        $validated['name'],
        $validated['name_ar'] ?? null,
        'en'
    );

    // Set supported languages
    $supportedLanguages = ['en'];
    if (!empty($validated['name_ar']) || !empty($validated['description_ar'])) {
        $supportedLanguages[] = 'ar';
    }
    $validated['supported_languages'] = $supportedLanguages;

    $course = Course::create($validated);

    return redirect()->route('courses.index')
        ->with('success', 'Course created successfully!');
}
```

## Frontend Integration

### Language Switching

```javascript
// Switch language via URL parameter
function switchLanguage(lang) {
    const url = new URL(window.location);
    url.searchParams.set("lang", lang);
    window.location.href = url.toString();
}
```

### RTL Support

```css
/* RTL support for Arabic content */
[dir="rtl"] {
    text-align: right;
}

[dir="rtl"] .multilingual-field textarea,
[dir="rtl"] .multilingual-field input {
    text-align: right;
}
```

## SEO Benefits

The multilingual implementation includes comprehensive SEO features:

1. **Hreflang Tags**: Automatic generation for language versions
2. **Structured Data**: JSON-LD for both languages
3. **Meta Tags**: Language-specific meta titles and descriptions
4. **Open Graph**: Social media sharing optimization
5. **Twitter Cards**: Twitter-specific sharing optimization
6. **Canonical URLs**: Proper canonicalization

## Best Practices

1. **Always provide English content** as the primary language
2. **Use the multilingual helper** for generating slugs and SEO data
3. **Validate both languages** when creating content
4. **Use proper RTL styling** for Arabic content
5. **Implement language switching** in your UI
6. **Test SEO implementation** with Google Search Console

## Migration Commands

To apply the database changes:

```bash
php artisan migrate
```

The migrations will add all the necessary fields to support multilingual content.

## File Structure

```
app/
├── Models/
│   ├── Course.php (updated)
│   ├── CourseSection.php (updated)
│   └── CourseLecture.php (updated)
├── Traits/
│   └── HasMultilingualFields.php (new)
├── Helpers/
│   └── MultilingualHelper.php (new)
└── Http/Controllers/Admin/
    └── MultilingualCourseController.php (example)

resources/views/admin/courses/
├── partials/
│   ├── multilingual-fields.blade.php (new)
│   └── seo-fields.blade.php (new)
└── create-multilingual-example.blade.php (example)
```

This implementation provides a robust foundation for creating and managing multilingual course content with proper SEO optimization and user experience considerations.
