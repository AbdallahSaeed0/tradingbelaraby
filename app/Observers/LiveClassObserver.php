<?php

namespace App\Observers;

use App\Models\LiveClass;
use App\Notifications\CourseNewContentNotification;

class LiveClassObserver
{
    public function updated(LiveClass $liveClass): void
    {
        // Notify when a live class is scheduled/announced (status becomes 'scheduled' or similar)
        if (!$liveClass->wasChanged('status') || $liveClass->status !== 'scheduled') {
            return;
        }
        $course = $liveClass->course;
        if (!$course) {
            return;
        }
        $name = $liveClass->localized_name ?? $liveClass->name ?? 'Live class';
        $nameAr = $liveClass->name_ar ?? $name;
        $notification = new CourseNewContentNotification(
            CourseNewContentNotification::KEY_NEW_LIVE_CLASS_ANNOUNCED,
            $course->name ?? '',
            $course->name_ar ?? '',
            $course->id,
            $name,
            $nameAr,
            $liveClass->id,
            '/live-classes/' . $liveClass->id
        );
        foreach ($course->enrollments()->with('user')->get() as $enrollment) {
            if ($enrollment->user) {
                $enrollment->user->notify($notification);
            }
        }
    }
}
