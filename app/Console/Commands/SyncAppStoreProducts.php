<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Services\Payment\AppStoreConnectService;
use Illuminate\Console\Command;

class SyncAppStoreProducts extends Command
{
    protected $signature = 'app-store:sync-products {--course= : Only sync this course ID}';

    protected $description = 'Create missing App Store In-App Purchases (and sync prices) for all published, paid courses';

    public function handle(AppStoreConnectService $appStoreConnectService): int
    {
        if (! $appStoreConnectService->isConfigured()) {
            $this->error('App Store Connect API is not configured (see APPLE_ASC_KEY_ID and the AuthKey .p8 file).');

            return self::FAILURE;
        }

        $courses = Course::where('status', 'published')
            ->where('is_free', false)
            ->where('price', '>', 0)
            ->when($this->option('course'), fn ($q, $id) => $q->whereKey($id))
            ->get();

        $this->info("Syncing {$courses->count()} course(s) to App Store Connect...");

        $failures = 0;
        foreach ($courses as $course) {
            try {
                $result = $appStoreConnectService->syncProduct($course);
                $this->line("  ✓ Course #{$course->id} ({$result}): {$course->name}");
            } catch (\Throwable $e) {
                $failures++;
                $this->error("  ✗ Course #{$course->id}: {$e->getMessage()}");
            }
        }

        $this->info('Done.' . ($failures ? " {$failures} failure(s)." : ''));

        return $failures ? self::FAILURE : self::SUCCESS;
    }
}
