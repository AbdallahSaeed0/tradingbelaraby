<?php

namespace App\Observers;

use App\Jobs\SyncGooglePlayProductJob;
use App\Models\Course;

class CourseObserver
{
    public function created(Course $course): void
    {
        $this->syncGooglePlayProductIfNeeded($course);
    }

    public function updated(Course $course): void
    {
        if (! $course->wasChanged(['name', 'description', 'price', 'is_free', 'status'])) {
            return;
        }

        $this->syncGooglePlayProductIfNeeded($course);
    }

    private function syncGooglePlayProductIfNeeded(Course $course): void
    {
        if ($course->status !== 'published' || $course->is_free || (float) $course->price <= 0) {
            return;
        }

        SyncGooglePlayProductJob::dispatch($course->id);
    }
}
