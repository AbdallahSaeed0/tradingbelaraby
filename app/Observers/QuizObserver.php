<?php

namespace App\Observers;

use App\Models\Quiz;
use App\Notifications\CourseNewContentNotification;

class QuizObserver
{
    public function updated(Quiz $quiz): void
    {
        if (!$quiz->wasChanged('is_published') || !$quiz->is_published) {
            return;
        }
        $course = $quiz->course;
        if (!$course) {
            return;
        }
        $title = $quiz->name ?? 'New quiz';
        $titleAr = $quiz->name_ar ?? $title;
        $notification = new CourseNewContentNotification(
            CourseNewContentNotification::KEY_NEW_QUIZ,
            $course->name ?? '',
            $course->name_ar ?? '',
            $course->id,
            $title,
            $titleAr,
            $quiz->id,
            '/courses/' . $course->id
        );
        foreach ($course->enrollments()->with('user')->get() as $enrollment) {
            if ($enrollment->user) {
                $enrollment->user->notify($notification);
            }
        }
    }
}
