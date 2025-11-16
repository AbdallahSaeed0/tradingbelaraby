<?php

namespace App\Http\Controllers\Admin;

use App\Models\Course;
use Illuminate\Support\Str;

class CourseDuplicateService
{
    /**
     * Duplicate a course with basic fields and return the new instance.
     */
    public function duplicate(Course $course): Course
    {
        // Replicate course without primary key and timestamps
        $newCourse = $course->replicate();

        // Reset fields that must be unique or specific
        $newCourse->name = $course->name . ' (' . __('Copy') . ')';
        $newCourse->name_ar = $course->name_ar ? $course->name_ar . ' (' . __('Copy') . ')' : $course->name_ar;

        // Generate unique slug
        $baseSlug = \Illuminate\Support\Str::slug($newCourse->name);
        $slug = $baseSlug;
        $counter = 1;
        while (\App\Models\Course::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $newCourse->slug = $slug;

        // Set status to draft for the duplicated course
        $newCourse->status = 'draft';

        $newCourse->save();

        // Duplicate instructors (many-to-many)
        if (method_exists($course, 'instructors')) {
            $newCourse->instructors()->sync($course->instructors->pluck('id')->toArray());
        }

        // Optionally, duplicate sections (shallow copy)
        if (method_exists($course, 'sections')) {
            foreach ($course->sections as $section) {
                $newSection = $section->replicate();
                $newSection->course_id = $newCourse->id;
                $newSection->save();
            }
        }

        return $newCourse;
    }
}


