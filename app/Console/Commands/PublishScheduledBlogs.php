<?php

namespace App\Console\Commands;

use App\Jobs\SendBlogToTelegram;
use App\Models\Blog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class PublishScheduledBlogs extends Command
{
    protected $signature = 'blogs:publish-scheduled';

    protected $description = 'Publish scheduled blogs whose publish_at is due, and optionally dispatch Telegram posting jobs';

    public function handle(): int
    {
        $now = now();

        $dueBlogIds = Blog::query()
            ->where('status', 'scheduled')
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', $now)
            ->pluck('id');

        if ($dueBlogIds->isEmpty()) {
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

            $publishedCount = 0;
            $queuedCount = 0;

            Blog::whereIn('id', $dueBlogIds)->chunkById(100, function ($blogs) use (&$publishedCount, &$queuedCount): void {
                foreach ($blogs as $blog) {
                    $publishedCount++;
                    if ($blog->post_to_telegram) {
                        SendBlogToTelegram::dispatch($blog->id);
                        $queuedCount++;
                    }
                }
            });

            $this->info("Published {$publishedCount} scheduled blog(s).");
            $this->info("Queued {$queuedCount} Telegram publish job(s).");

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Failed to publish scheduled blogs: ' . $e->getMessage());
            report($e);

            return self::FAILURE;
        }
    }
}
