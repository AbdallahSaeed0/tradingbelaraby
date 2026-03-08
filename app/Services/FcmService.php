<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserFcmToken;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    /**
     * Send push notification to user's devices.
     * Uses Firebase SDK (Kreait) when available and FIREBASE_CREDENTIALS is set,
     * otherwise uses legacy FCM HTTP when FCM_SERVER_KEY is set.
     */
    public static function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $tokens = array_values(array_unique($user->fcmTokens()->pluck('token')->toArray()));
        if (empty($tokens)) {
            Log::info('FCM: No device tokens for user. Push will not show in system bar. User must open the app while logged in to register device.', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
            return;
        }

        if (self::useFirebaseSdk()) {
            try {
                self::sendViaFirebaseSdk($tokens, $title, $body, $data);
            } catch (\Throwable $e) {
                $msg = $e->getMessage();
                Log::warning('FCM (Firebase SDK) send failed', ['user_id' => $user->id, 'error' => $msg]);
                if (self::isTokenNotFoundError($msg)) {
                    self::removeUserFcmTokens($user);
                    Log::info('FCM: Removed invalid/old tokens for user. User must open the app (same Firebase project as server) to re-register.', ['user_id' => $user->id]);
                }
                self::sendViaLegacy($tokens, $title, $body, $data, $user->id);
            }
            return;
        }

        Log::debug('FCM: Using legacy API', [
            'reason' => self::getWhyNoFirebaseSdk(),
            'user_id' => $user->id,
        ]);
        self::sendViaLegacy($tokens, $title, $body, $data, $user->id);
    }

    /**
     * Send to a single token (e.g. for testing). Prefers Firebase SDK if configured.
     */
    public static function sendToToken(string $token, string $title, string $body, array $data = []): bool
    {
        if (self::useFirebaseSdk()) {
            try {
                self::sendViaFirebaseSdk([$token], $title, $body, $data);
                return true;
            } catch (\Throwable $e) {
                Log::warning('FCM (Firebase SDK) send failed', ['error' => $e->getMessage()]);
            }
        }
        $key = config('services.fcm.server_key');
        return $key ? self::sendToTokenLegacy($token, $title, $body, $data, $key) : false;
    }

    private static function useFirebaseSdk(): bool
    {
        $credentials = config('firebase.projects.app.credentials') ?? env('FIREBASE_CREDENTIALS');
        if (empty($credentials)) {
            return false;
        }
        return class_exists('Kreait\Firebase\Factory');
    }

    private static function getWhyNoFirebaseSdk(): string
    {
        $credentials = config('firebase.projects.app.credentials') ?? env('FIREBASE_CREDENTIALS');
        if (empty($credentials)) {
            return 'FIREBASE_CREDENTIALS not set or empty in .env';
        }
        if (!class_exists('Kreait\Firebase\Factory')) {
            return 'Kreait Firebase SDK not loaded (run composer install on server and restart queue)';
        }
        return 'unknown';
    }

    private static function isTokenNotFoundError(string $message): bool
    {
        return stripos($message, 'Requested entity was not found') !== false
            || stripos($message, 'NOT_FOUND') !== false
            || stripos($message, 'invalid registration') !== false;
    }

    private static function removeUserFcmTokens(User $user): void
    {
        UserFcmToken::where('user_id', $user->id)->delete();
    }

    /**
     * Send via Kreait Firebase SDK. Only call when useFirebaseSdk() is true.
     */
    private static function sendViaFirebaseSdk(array $tokens, string $title, string $body, array $data): void
    {
        $messaging = self::createMessaging();
        $dataStrings = self::dataToStrings($data);
        $androidConfig = \Kreait\Firebase\Messaging\AndroidConfig::fromArray([
            'priority' => 'high',
            'notification' => [
                'channel_id' => 'courses_app_notifications',
                'notification_priority' => 'PRIORITY_HIGH',
                'visibility' => 'PUBLIC',
            ],
        ]);
        $notification = \Kreait\Firebase\Messaging\Notification::create($title, $body);
        foreach ($tokens as $token) {
            $message = \Kreait\Firebase\Messaging\CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($dataStrings)
                ->withAndroidConfig($androidConfig);
            $messaging->send($message);
        }
    }

    /**
     * Fallback: send via legacy FCM HTTP API (no Kreait dependency).
     */
    private static function sendViaLegacy(array $tokens, string $title, string $body, array $data, ?int $userId = null): void
    {
        $key = config('services.fcm.server_key');
        if (empty($key)) {
            if ($userId !== null) {
                Log::warning('FCM: No push sent (legacy fallback has no FCM_SERVER_KEY). Set FCM_SERVER_KEY in .env for fallback, or fix Firebase SDK error above.', ['user_id' => $userId]);
            }
            return;
        }
        $sent = 0;
        foreach ($tokens as $token) {
            if (self::sendToTokenLegacy($token, $title, $body, $data, $key)) {
                $sent++;
            }
        }
    }

    /**
     * Create Messaging instance via Kreait\Firebase\Factory (only when class exists).
     */
    private static function createMessaging(): \Kreait\Firebase\Contract\Messaging
    {
        $credentials = config('firebase.projects.app.credentials') ?? env('FIREBASE_CREDENTIALS');
        if (empty($credentials)) {
            throw new \InvalidArgumentException('FIREBASE_CREDENTIALS is not set.');
        }
        $isJson = str_starts_with(trim($credentials), '{');
        if ($isJson) {
            $pathOrJson = $credentials;
        } else {
            $isAbsolutePath = str_starts_with($credentials, '/') || (strlen($credentials) >= 2 && $credentials[1] === ':');
            $pathOrJson = $isAbsolutePath ? $credentials : self::resolveCredentialsPath($credentials);
        }
        $factory = new \Kreait\Firebase\Factory();
        $factory = $factory->withServiceAccount($pathOrJson);
        return $factory->createMessaging();
    }

    /**
     * Resolve relative credentials path to an absolute path that exists.
     */
    private static function resolveCredentialsPath(string $relativePath): string
    {
        $candidates = [
            base_path($relativePath),
            base_path('storage/app/' . basename($relativePath)),
            storage_path('app/' . basename($relativePath)),
        ];
        foreach ($candidates as $path) {
            if (is_file($path) && is_readable($path)) {
                return $path;
            }
        }
        throw new \InvalidArgumentException(sprintf(
            'FCM credentials file not found or not readable. Tried: %s',
            implode(', ', $candidates)
        ));
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
