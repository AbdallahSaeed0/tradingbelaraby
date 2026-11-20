<?php

namespace App\Services;

use App\Models\LiveClass;

/**
 * Zoom Meeting SDK helpers: lets students join a live class inside the website.
 *
 * The browser never receives the Zoom link. It asks our server for a short-lived
 * signature (only for enrolled students, only around session time) and joins with
 * the meeting number + passcode through the SDK.
 */
class ZoomSdkService
{
    public function isConfigured(): bool
    {
        return (bool) config('services.zoom_sdk.key') && (bool) config('services.zoom_sdk.secret');
    }

    public function sdkKey(): ?string
    {
        return config('services.zoom_sdk.key');
    }

    public function version(): string
    {
        return config('services.zoom_sdk.version', '3.13.2');
    }

    /**
     * Meeting number from a Zoom link: zoom.us/j/123..., /wc/join/123..., /s/123..., /my/... is not supported.
     */
    public function meetingNumber(LiveClass $liveClass): ?string
    {
        $link = (string) $liveClass->link;
        if (!preg_match('~zoom\.(us|com)/~i', $link)) {
            return null;
        }
        if (preg_match('~/(?:j|wc/join|wc|s|w)/(\d{9,12})~', $link, $m)) {
            return $m[1];
        }

        return null;
    }

    /**
     * Plain passcode set by the admin; falls back to the link's pwd= value (works for some meetings).
     */
    public function passcode(LiveClass $liveClass): string
    {
        if (!empty($liveClass->meeting_passcode)) {
            return (string) $liveClass->meeting_passcode;
        }

        $query = parse_url((string) $liveClass->link, PHP_URL_QUERY) ?: '';
        parse_str($query, $params);

        return (string) ($params['pwd'] ?? '');
    }

    public function canEmbed(LiveClass $liveClass): bool
    {
        return $this->isConfigured() && $this->meetingNumber($liveClass) !== null;
    }

    /**
     * Meeting SDK signature (JWT, HS256), valid for 2 hours. Role 0 = attendee.
     */
    public function signature(string $meetingNumber, int $role = 0): string
    {
        $iat = time() - 30;
        $exp = $iat + 60 * 60 * 2;

        $header = ['alg' => 'HS256', 'typ' => 'JWT'];
        $payload = [
            'appKey' => $this->sdkKey(),
            'sdkKey' => $this->sdkKey(),
            'mn' => $meetingNumber,
            'role' => $role,
            'iat' => $iat,
            'exp' => $exp,
            'tokenExp' => $exp,
        ];

        $encode = fn (array $part) => rtrim(strtr(base64_encode(json_encode($part)), '+/', '-_'), '=');
        $unsigned = $encode($header) . '.' . $encode($payload);
        $signature = rtrim(strtr(base64_encode(hash_hmac('sha256', $unsigned, (string) config('services.zoom_sdk.secret'), true)), '+/', '-_'), '=');

        return $unsigned . '.' . $signature;
    }
}
