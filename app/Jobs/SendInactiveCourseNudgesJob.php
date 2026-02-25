<?php

namespace App\Jobs;

use App\Models\CourseEnrollment;
use App\Models\NotificationSend;
use App\Notifications\CourseProgressNudgeNotification;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendInactiveCourseNudgesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Enrolled user inactive for 7 days: no progress/lecture completion in last 7 days.
     * Use last_accessed_at on enrollment or latest LectureCompletion for the course.
     */
    public function handle(): void
    {
        $cutoff = Carbon::now()->subDays(7);
        $enrollments = CourseEnrollment::where('status', 'active')
            ->where(function ($q) use ($cutoff) {
                $q->whereNull('last_accessed_at')
                    ->orWhere('last_accessed_at', '<', $cutoff);
            })
            ->where('progress_percentage', '<', 100)
            ->with(['user', 'course'])
            ->get();

        $today = Carbon::now()->startOfDay();
        foreach ($enrollments as $enrollment) {
            if (!$enrollment->user) {
                continue;
            }
            $sent = NotificationSend::recordSend(
                $enrollment->user_id,
                CourseProgressNudgeNotification::KEY,
                $enrollment->id,
                $today
            );
            if ($sent) {
                $enrollment->user->notify(new CourseProgressNudgeNotification($enrollment));
            }
        }
    }
}
