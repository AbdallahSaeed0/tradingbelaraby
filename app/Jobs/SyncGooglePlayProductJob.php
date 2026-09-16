<?php

namespace App\Jobs;

use App\Models\Course;
use App\Services\Payment\GooglePlayService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncGooglePlayProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $courseId)
    {
    }

    public function handle(GooglePlayService $googlePlayService): void
    {
        if (! $googlePlayService->isConfigured()) {
            return;
        }

        $course = Course::find($this->courseId);
        if (! $course) {
            return;
        }

        try {
            $googlePlayService->syncProduct($course);
        } catch (\Throwable $e) {
            Log::error('SyncGooglePlayProductJob failed: ' . $e->getMessage(), ['course_id' => $this->courseId]);
            throw $e;
        }
    }
}
