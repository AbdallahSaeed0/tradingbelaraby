<?php

namespace App\Observers;

use App\Models\CourseLecture;
use App\Notifications\CourseNewContentNotification;

class CourseLectureObserver
{
    public function updated(CourseLecture $lecture): void
    {
        if (!$lecture->wasChanged('is_published') || !$lecture->is_published) {
            return;
        }
        $course = $lecture->course;
        if (!$course) {
            return;
        }
        $title = $lecture->title ?? 'New lecture';
        $titleAr = $lecture->title_ar ?? $title;
        $notification = new CourseNewContentNotification(
            CourseNewContentNotification::KEY_NEW_LECTURE,
            $course->name ?? '',
            $course->name_ar ?? '',
            $course->id,
            $title,
            $titleAr,
            $lecture->id,
            '/courses/' . $course->id
        );
        foreach ($course->enrollments()->with('user')->get() as $enrollment) {
            if ($enrollment->user) {
                $enrollment->user->notify($notification);
            }
        }
    }
}
