<?php

namespace App\Console\Commands;

use App\Services\TelegramService;
use Illuminate\Console\Command;
use Throwable;

class TelegramTest extends Command
{
    protected $signature = 'telegram:test';

    protected $description = 'Send a Telegram test message using configured bot token and chat ID';

    public function handle(TelegramService $telegramService): int
    {
        $this->info('Testing Telegram integration...');
        $this->line('Using chat_id: ' . (string) config('services.telegram.chat_id'));

        $message = '<b>Telegram Test</b>' . PHP_EOL
            . 'This is a test message from Laravel at ' . now()->toDateTimeString() . '.';

        try {
            $response = $telegramService->sendMessage($message);
            $messageId = data_get($response, 'result.message_id');

            $this->info('Telegram test passed.');
            $this->line('Message ID: ' . ($messageId ?: 'N/A'));

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Telegram test failed.');
            $this->error('Reason: ' . $e->getMessage());

            // Best effort diagnostic hints for common setup mistakes.
            $reason = strtolower($e->getMessage());
            if (str_contains($reason, 'invalid bot token') || str_contains($reason, 'authentication failed')) {
                $this->warn('Hint: TELEGRAM_BOT_TOKEN may be invalid.');
            } elseif (str_contains($reason, 'chat/channel not found')) {
                $this->warn('Hint: TELEGRAM_CHAT_ID may be wrong. Use channel numeric id or @channelusername.');
            } elseif (str_contains($reason, 'permission') || str_contains($reason, 'admin')) {
                $this->warn('Hint: Bot may not be admin in the channel.');
            } else {
                $this->warn('Hint: Check logs for Telegram API response details.');
            }

            return self::FAILURE;
        }
    }
}
