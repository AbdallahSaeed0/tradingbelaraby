<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\AndroidConfig;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class FcmService
{
    /**
     * Send push notification to user's devices.
     * Uses kreait/laravel-firebase (FCM v1) when FIREBASE_CREDENTIALS is set,
     * otherwise falls back to legacy FCM HTTP when FCM_SERVER_KEY is set.
     */
    public static function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $tokens = $user->fcmTokens()->pluck('token')->toArray();
        if (empty($tokens)) {
            return;
        }

        if (self::useFirebaseSdk()) {
            try {
                $messaging = app(Messaging::class);
                $dataStrings = self::dataToStrings($data);
                $androidHigh = AndroidConfig::new()->withHighPriority();
                foreach ($tokens as $token) {
                    $message = CloudMessage::withTarget('token', $token)
                        ->withNotification(FcmNotification::create($title, $body))
                        ->withData($dataStrings)
                        ->withAndroidConfig($androidHigh);
                    $messaging->send($message);
                }
            } catch (\Throwable $e) {
                Log::warning('FCM (Firebase SDK) send failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
            return;
        }

        $key = config('services.fcm.server_key');
        if (empty($key)) {
            Log::debug('FCM: No credentials or server key configured. Skip push.');
            return;
        }
        foreach ($tokens as $token) {
            self::sendToTokenLegacy($token, $title, $body, $data, $key);
        }
    }

    /**
     * Send to a single token (e.g. for testing). Prefers Firebase SDK if configured.
     */
    public static function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        if (self::useFirebaseSdk()) {
            try {
                $messaging = app(Messaging::class);
                $message = CloudMessage::withTarget('token', $token)
                    ->withNotification(FcmNotification::create($title, $body))
                    ->withData(self::dataToStrings($data))
                    ->withAndroidConfig(AndroidConfig::new()->withHighPriority());
                $messaging->send($message);
                return true;
            } catch (\Throwable $e) {
                Log::warning('FCM (Firebase SDK) send failed', ['error' => $e->getMessage()]);
                return false;
            }
        }

        $key = config('services.fcm.server_key');
        return $key ? self::sendToTokenLegacy($token, $title, $body, $data, $key) : false;
    }

    private static function useFirebaseSdk(): bool
    {
        $credentials = config('firebase.projects.app.credentials') ?? env('FIREBASE_CREDENTIALS');
        return !empty($credentials);
    }

    /** FCM data payload accepts only string values. */
    private static function dataToStrings(array $data): array
    {
        $out = [];
        foreach ($data as $k => $v) {
            $out[(string) $k] = (string) $v;
        }
        return $out;
    }

    /**
     * Legacy FCM HTTP API (Server key).
     */
    private static function sendToTokenLegacy(string $token, string $title, string $body, array $data = [], ?string $serverKey = null): bool
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
            Log::warning('FCM (legacy) send failed', ['token' => substr($token, 0, 20) . '...', 'response' => $response->body()]);
            return false;
        }
        return true;
    }
}
