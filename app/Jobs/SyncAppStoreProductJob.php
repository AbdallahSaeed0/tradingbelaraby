<?php

namespace App\Jobs;

use App\Models\Course;
use App\Services\Payment\AppStoreConnectService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SyncAppStoreProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public function __construct(public readonly int $courseId)
    {
    }

    public function handle(AppStoreConnectService $appStoreConnectService): void
    {
        if (! $appStoreConnectService->isConfigured()) {
            return;
        }

        $course = Course::find($this->courseId);
        if (! $course) {
            return;
        }

        try {
            $appStoreConnectService->syncProduct($course);
        } catch (\Throwable $e) {
            // Never break saving a course; `php artisan app-store:sync-products` can retry.
            Log::error('SyncAppStoreProductJob failed: ' . $e->getMessage(), ['course_id' => $this->courseId]);
        }
    }
}
