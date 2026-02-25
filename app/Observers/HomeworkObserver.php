<?php

namespace App\Observers;

use App\Models\Homework;
use App\Notifications\CourseNewContentNotification;

class HomeworkObserver
{
    public function updated(Homework $homework): void
    {
        if (!$homework->wasChanged('is_published') || !$homework->is_published) {
            return;
        }
        $course = $homework->course;
        if (!$course) {
            return;
        }
        $title = $homework->name ?? 'New homework';
        $titleAr = $homework->name_ar ?? $title;
        $notification = new CourseNewContentNotification(
            CourseNewContentNotification::KEY_NEW_HOMEWORK,
            $course->name ?? '',
            $course->name_ar ?? '',
            $course->id,
            $title,
            $titleAr,
            $homework->id,
            '/courses/' . $course->id
        );
        foreach ($course->enrollments()->with('user')->get() as $enrollment) {
            if ($enrollment->user) {
                $enrollment->user->notify($notification);
            }
        }
    }
}
