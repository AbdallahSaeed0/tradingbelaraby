<?php

namespace App\Console\Commands;

use App\Jobs\SendBlogToTelegram;
use App\Models\Blog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PublishScheduledBlogs extends Command
{
    protected $signature = 'blogs:publish-scheduled';

    protected $description = 'Publish scheduled blogs whose publish_at is due, and optionally dispatch Telegram posting jobs';

    public function handle(): int
    {
        $now = now();
        $startedAt = microtime(true);

        Log::info('Scheduled blog publisher started.', [
            'command' => 'blogs:publish-scheduled',
            'started_at' => $now->toDateTimeString(),
        ]);
        $this->info('[blogs:publish-scheduled] Start at ' . $now->toDateTimeString());

        $dueBlogIds = Blog::query()
            ->where('status', 'scheduled')
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', $now)
            ->pluck('id');

        Log::info('Scheduled blog publisher due blogs resolved.', [
            'due_count' => $dueBlogIds->count(),
            'due_blog_ids' => $dueBlogIds->values()->all(),
        ]);
        $this->line('[blogs:publish-scheduled] Due blogs: ' . $dueBlogIds->count());

        if ($dueBlogIds->isEmpty()) {
            $elapsedMs = (int) ((microtime(true) - $startedAt) * 1000);
            Log::info('Scheduled blog publisher finished (nothing due).', [
                'elapsed_ms' => $elapsedMs,
            ]);
            $this->info('No scheduled blogs due for publishing.');
            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use ($dueBlogIds, $now): void {
                Blog::whereIn('id', $dueBlogIds)->update([
                    'status' => 'published',
                    'published_at' => $now,
                    'updated_at' => $now,
                ]);
            });
            Log::info('Scheduled blog publisher updated blog statuses to published.', [
                'published_blog_ids' => $dueBlogIds->values()->all(),
                'published_at' => $now->toDateTimeString(),
            ]);
            $this->line('[blogs:publish-scheduled] Published blog IDs: ' . $dueBlogIds->implode(', '));

            $publishedCount = 0;
            $queuedCount = 0;
            $telegramQueuedBlogIds = [];

            Blog::whereIn('id', $dueBlogIds)->chunkById(100, function ($blogs) use (&$publishedCount, &$queuedCount, &$telegramQueuedBlogIds): void {
                foreach ($blogs as $blog) {
                    $publishedCount++;
                    if ($blog->shouldSendToTelegram()) {
                        SendBlogToTelegram::dispatch($blog->id);
                        $queuedCount++;
                        $telegramQueuedBlogIds[] = $blog->id;
                    }
                }
            });

            Log::info('Scheduled blog publisher Telegram dispatch summary.', [
                'published_count' => $publishedCount,
                'telegram_queued_count' => $queuedCount,
                'telegram_queued_blog_ids' => $telegramQueuedBlogIds,
            ]);
            if (!empty($telegramQueuedBlogIds)) {
                $this->line('[blogs:publish-scheduled] Telegram queued blog IDs: ' . implode(', ', $telegramQueuedBlogIds));
            } else {
                $this->line('[blogs:publish-scheduled] Telegram queued blog IDs: none');
            }

            $elapsedMs = (int) ((microtime(true) - $startedAt) * 1000);
            Log::info('Scheduled blog publisher finished successfully.', [
                'published_count' => $publishedCount,
                'telegram_queued_count' => $queuedCount,
                'elapsed_ms' => $elapsedMs,
            ]);
            $this->info("Published {$publishedCount} scheduled blog(s).");
            $this->info("Queued {$queuedCount} Telegram publish job(s).");
            $this->info("[blogs:publish-scheduled] Finished successfully in {$elapsedMs} ms.");

            return self::SUCCESS;
        } catch (Throwable $e) {
            Log::error('Scheduled blog publisher failed.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->error('Failed to publish scheduled blogs: ' . $e->getMessage());
            report($e);

            return self::FAILURE;
        }
    }
}
