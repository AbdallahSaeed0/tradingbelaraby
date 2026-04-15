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

        if (!$blog->shouldSendToTelegram()) {
            Log::info('SendBlogToTelegram skipped: Telegram disabled or no language selected.', ['blog_id' => $blog->id]);
            return;
        }

        if ($blog->telegram_send_ar) {
            $this->sendLanguage($blog, 'ar', $telegramService);
        }

        if ($blog->telegram_send_en) {
            $this->sendLanguage($blog, 'en', $telegramService);
        }

        // Backward-compatible aggregate fields.
        $blog->refresh();
        if ($blog->telegram_post_status_ar === 'sent' || $blog->telegram_post_status_en === 'sent') {
            $blog->forceFill([
                'telegram_post_status' => 'sent',
                'telegram_posted_at' => $blog->telegram_posted_at_ar ?? $blog->telegram_posted_at_en,
                'telegram_message_id' => $blog->telegram_message_id_ar ?: $blog->telegram_message_id_en,
                'telegram_error' => null,
            ])->save();
        } elseif (($blog->telegram_send_ar && $blog->telegram_post_status_ar === 'failed')
            || ($blog->telegram_send_en && $blog->telegram_post_status_en === 'failed')
        ) {
            $blog->forceFill([
                'telegram_post_status' => 'failed',
                'telegram_error' => trim(($blog->telegram_error_ar ?? '') . ' ' . ($blog->telegram_error_en ?? '')),
            ])->save();
        }
    }

    private function sendLanguage(Blog $blog, string $lang, TelegramService $telegramService): void
    {
        if ($this->alreadySentForLanguage($blog, $lang)) {
            Log::info('SendBlogToTelegram skipped for language: already sent.', [
                'blog_id' => $blog->id,
                'lang' => $lang,
            ]);
            return;
        }

        $statusField = "telegram_post_status_{$lang}";
        $errorField = "telegram_error_{$lang}";
        $postedAtField = "telegram_posted_at_{$lang}";
        $messageIdField = "telegram_message_id_{$lang}";

        $blog->forceFill([
            $statusField => 'pending',
            $errorField => null,
        ])->save();

        try {
            $payload = $this->buildTelegramMessageForLanguage($blog, $lang);
            $response = $this->sendWithPhotoFallback($telegramService, $payload['photo_url'], $payload['message']);

            $blog->forceFill([
                $postedAtField => now(),
                $messageIdField => (string) data_get($response, 'result.message_id', ''),
                $statusField => 'sent',
                $errorField => null,
            ])->save();
        } catch (Throwable $e) {
            $blog->forceFill([
                $statusField => 'failed',
                $errorField => Str::limit($e->getMessage(), 5000),
            ])->save();

            Log::error('Failed to post blog to Telegram for language.', [
                'blog_id' => $blog->id,
                'lang' => $lang,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function alreadySentForLanguage(Blog $blog, string $lang): bool
    {
        $statusField = "telegram_post_status_{$lang}";
        $postedAtField = "telegram_posted_at_{$lang}";

        return $blog->{$statusField} === 'sent' || !empty($blog->{$postedAtField});
    }

    /**
     * @return array{message: string, photo_url: ?string}
     */
    private function buildTelegramMessageForLanguage(Blog $blog, string $lang): array
    {
        $isArabic = $lang === 'ar';

        $title = $isArabic && !empty($blog->title_ar) ? $blog->title_ar : $blog->title;
        $excerpt = $isArabic ? ($blog->excerpt_ar ?: null) : ($blog->excerpt ?: null);
        $description = $isArabic ? ($blog->description_ar ?: '') : ($blog->description ?: '');

        if (empty($excerpt)) {
            $excerpt = Str::limit(trim(strip_tags($description)), 350);
        }

        $titleSafe = e((string) $title);
        $excerptSafe = trim((string) $excerpt) !== '' ? PHP_EOL . PHP_EOL . e((string) $excerpt) : '';
        $url = $this->buildPublicBlogUrl($blog);

        $photoUrl = $isArabic
            ? ($blog->image_ar_url ?: $blog->image_url)
            : ($blog->image_url ?: $blog->image_ar_url);

        return [
            'message' => "<b>{$titleSafe}</b>{$excerptSafe}" . PHP_EOL . PHP_EOL . $url,
            'photo_url' => $photoUrl,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function sendWithPhotoFallback(TelegramService $telegramService, ?string $photoUrl, string $textMessage): array
    {
        if ($photoUrl && $this->isPublicUrlReachable($photoUrl)) {
            try {
                return $telegramService->sendPhoto($photoUrl, $textMessage);
            } catch (Throwable $photoException) {
                Log::warning('Telegram photo publish failed; falling back to text.', [
                    'error' => $photoException->getMessage(),
                ]);
            }
        }

        return $telegramService->sendMessage($textMessage);
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
