<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Services\Payment\GooglePlayService;
use Illuminate\Console\Command;

class SyncGooglePlayProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'google-play:sync-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill Google Play in-app products for all published, paid courses';

    public function handle(GooglePlayService $googlePlayService): int
    {
        if (! $googlePlayService->isConfigured()) {
            $this->error('Google Play service account is not configured (see GOOGLE_PLAY_SERVICE_ACCOUNT_PATH).');
            return self::FAILURE;
        }

        $courses = Course::where('status', 'published')
            ->where('is_free', false)
            ->where('price', '>', 0)
            ->get();

        $this->info("Syncing {$courses->count()} course(s) to Google Play...");

        $failures = 0;
        foreach ($courses as $course) {
            try {
                $googlePlayService->syncProduct($course);
                $this->line("  ✓ Course #{$course->id}: {$course->name}");
            } catch (\Throwable $e) {
                $failures++;
                $this->error("  ✗ Course #{$course->id}: {$e->getMessage()}");
            }
        }

        $this->info('Done.' . ($failures ? " {$failures} failure(s)." : ''));

        return $failures ? self::FAILURE : self::SUCCESS;
    }
}
