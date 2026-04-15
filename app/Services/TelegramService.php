<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class TelegramService
{
    private string $token;
    private ?string $chatId;

    public function __construct()
    {
        $this->token = (string) config('services.telegram.token');
        $this->chatId = config('services.telegram.chat_id');
    }

    /**
     * @return array<string, mixed>
     */
    public function sendMessage(string $text): array
    {
        return $this->sendRequest('sendMessage', [
            'chat_id' => $this->requireChatId(),
            'text' => $text,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => false,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function sendPhoto(string $photoUrl, string $caption): array
    {
        return $this->sendRequest('sendPhoto', [
            'chat_id' => $this->requireChatId(),
            'photo' => $photoUrl,
            'caption' => $caption,
            'parse_mode' => 'HTML',
        ]);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function sendRequest(string $method, array $payload): array
    {
        $token = $this->requireToken();
        $baseUrl = "https://api.telegram.org/bot{$token}";
        $url = "{$baseUrl}/{$method}";

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->asForm()
                ->post($url, $payload);

            $data = $response->json();

            if (!is_array($data)) {
                Log::error('Telegram API returned non-JSON or invalid JSON.', [
                    'method' => $method,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new RuntimeException('Telegram API returned an invalid response body.');
            }

            if ($response->failed() || !($data['ok'] ?? false)) {
                $diagnosis = $this->diagnoseError($data, $response->status());

                Log::error('Telegram API request failed.', [
                    'method' => $method,
                    'status' => $response->status(),
                    'payload' => $payload,
                    'response' => $data,
                    'diagnosis' => $diagnosis,
                ]);

                throw new RuntimeException($diagnosis . ' Telegram response: ' . json_encode($data, JSON_UNESCAPED_UNICODE));
            }

            return $data;
        } catch (ConnectionException $e) {
            Log::error('Telegram API connection error.', [
                'method' => $method,
                'message' => $e->getMessage(),
            ]);
            throw new RuntimeException('Could not connect to Telegram API. Check network connectivity.');
        } catch (RequestException $e) {
            Log::error('Telegram API request exception.', [
                'method' => $method,
                'message' => $e->getMessage(),
            ]);
            throw new RuntimeException('Telegram API request failed: ' . $e->getMessage());
        }
    }

    private function requireChatId(): string
    {
        $chatId = (string) $this->chatId;

        if ($chatId === '') {
            throw new RuntimeException('Telegram chat_id is missing. Set TELEGRAM_CHAT_ID in .env.');
        }

        return $chatId;
    }

    private function requireToken(): string
    {
        if ($this->token === '') {
            throw new RuntimeException('Telegram bot token is missing. Set TELEGRAM_BOT_TOKEN in .env.');
        }

        return $this->token;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function diagnoseError(array $data, int $httpStatus): string
    {
        $description = (string) ($data['description'] ?? 'Unknown Telegram error');
        $lowerDescription = strtolower($description);

        if ($httpStatus === 401 || str_contains($lowerDescription, 'unauthorized')) {
            return 'Telegram authentication failed (invalid bot token).';
        }

        if (str_contains($lowerDescription, 'chat not found')) {
            return 'Telegram chat/channel not found. Verify TELEGRAM_CHAT_ID (numeric ID or @channelusername).';
        }

        if (str_contains($lowerDescription, 'bot is not a member')
            || str_contains($lowerDescription, 'not enough rights')
            || str_contains($lowerDescription, 'need administrator rights')
            || str_contains($lowerDescription, 'forbidden')
        ) {
            return 'Telegram bot lacks permission. Add the bot to the channel/group and grant admin posting rights.';
        }

        if (str_contains($lowerDescription, 'have no rights to send a message')) {
            return 'Bot cannot send to this chat. Ensure bot is admin and can post messages.';
        }

        return "Telegram API error: {$description}";
    }
}
