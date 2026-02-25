<?php

namespace App\Observers;

use App\Models\CourseSection;
use App\Notifications\CourseNewContentNotification;

class CourseSectionObserver
{
    public function updated(CourseSection $section): void
    {
        if (!$section->wasChanged('is_published') || !$section->is_published) {
            return;
        }
        $course = $section->course;
        if (!$course) {
            return;
        }
        $title = $section->title ?? 'New section';
        $titleAr = $section->title_ar ?? $title;
        $notification = new CourseNewContentNotification(
            CourseNewContentNotification::KEY_NEW_LESSON,
            $course->name ?? '',
            $course->name_ar ?? '',
            $course->id,
            $title,
            $titleAr,
            $section->id,
            '/courses/' . $course->id
        );
        foreach ($course->enrollments()->with('user')->get() as $enrollment) {
            if ($enrollment->user) {
                $enrollment->user->notify($notification);
            }
        }
    }
}
