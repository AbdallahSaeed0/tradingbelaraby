<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    /**
     * Send push notification to user's devices (FCM legacy HTTP).
     * Configure FCM_SERVER_KEY in .env (from Firebase Console -> Project Settings -> Cloud Messaging -> Server key).
     */
    public static function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $tokens = $user->fcmTokens()->pluck('token')->toArray();
        if (empty($tokens)) {
            return;
        }
        $key = config('services.fcm.server_key');
        if (empty($key)) {
            Log::debug('FCM: No server key configured. Skip push.');
            return;
        }
        foreach ($tokens as $token) {
            self::sendToToken($token, $title, $body, $data, $key);
        }
    }

    public static function sendToToken(string $token, string $title, string $body, array $data = [], ?string $serverKey = null): bool
    {
        $serverKey = $serverKey ?? config('services.fcm.server_key');
        if (empty($serverKey)) {
            return false;
        }
        $response = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [
            'to' => $token,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $data,
            'priority' => 'high',
        ]);
        if (!$response->successful()) {
            Log::warning('FCM send failed', ['token' => substr($token, 0, 20) . '...', 'response' => $response->body()]);
            return false;
        }
        return true;
    }
}
