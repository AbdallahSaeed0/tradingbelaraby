<?php

namespace App\Jobs;

use App\Models\Blog;
use App\Services\TelegramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SendBlogToTelegram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private readonly int $blogId)
    {
    }

    public function handle(TelegramService $telegramService): void
    {
        $blog = Blog::find($this->blogId);

        if (!$blog) {
            Log::warning('SendBlogToTelegram skipped: blog not found.', ['blog_id' => $this->blogId]);
            return;
        }

        if ($blog->telegram_post_status === 'sent' || $blog->telegram_posted_at) {
            Log::info('SendBlogToTelegram skipped: already sent.', ['blog_id' => $blog->id]);
            return;
        }

        $textMessage = $this->buildTelegramMessage($blog);
        $response = null;

        try {
            $blog->forceFill([
                'telegram_post_status' => 'pending',
                'telegram_error' => null,
            ])->save();

            $photoUrl = $blog->getLocalizedImageUrl();
            $photoAttempted = false;

            if ($photoUrl && $this->isPublicUrlReachable($photoUrl)) {
                $photoAttempted = true;
                try {
                    $response = $telegramService->sendPhoto($photoUrl, $textMessage);
                } catch (Throwable $photoException) {
                    Log::warning('Telegram photo publish failed; falling back to text.', [
                        'blog_id' => $blog->id,
                        'error' => $photoException->getMessage(),
                    ]);
                    $response = $telegramService->sendMessage($textMessage);
                }
            } else {
                $response = $telegramService->sendMessage($textMessage);
            }

            $blog->forceFill([
                'telegram_posted_at' => now(),
                'telegram_message_id' => (string) data_get($response, 'result.message_id', ''),
                'telegram_post_status' => 'sent',
                'telegram_error' => null,
            ])->save();

            Log::info('Blog posted to Telegram successfully.', [
                'blog_id' => $blog->id,
                'method' => $photoAttempted ? 'photo_or_text_fallback' : 'text',
                'telegram_message_id' => data_get($response, 'result.message_id'),
            ]);
        } catch (Throwable $e) {
            $blog->forceFill([
                'telegram_post_status' => 'failed',
                'telegram_error' => Str::limit($e->getMessage(), 5000),
            ])->save();

            Log::error('Failed to post blog to Telegram.', [
                'blog_id' => $blog->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function buildTelegramMessage(Blog $blog): string
    {
        $title = e($blog->getLocalizedTitle());
        $excerpt = trim(strip_tags($blog->getLocalizedExcerpt() ?: ''));
        $excerptLine = $excerpt !== '' ? PHP_EOL . PHP_EOL . e(Str::limit($excerpt, 350)) : '';
        $url = $this->buildPublicBlogUrl($blog);

        return "<b>{$title}</b>{$excerptLine}" . PHP_EOL . PHP_EOL . $url;
    }

    private function buildPublicBlogUrl(Blog $blog): string
    {
        return url('/blog/' . $blog->slug);
    }

    private function isPublicUrlReachable(string $url): bool
    {
        try {
            $response = Http::timeout(8)->head($url);
            return $response->successful();
        } catch (Throwable $e) {
            Log::warning('Could not validate public image URL for Telegram.', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
