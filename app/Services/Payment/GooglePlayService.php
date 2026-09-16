<?php

namespace App\Services\Payment;

use App\Models\Course;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Google Play Billing integration: verifies Android purchases and keeps the
 * Play Console in-app product catalog in sync with paid courses, via the
 * Android Publisher API (https://developers.google.com/android-publisher).
 */
class GooglePlayService
{
    private const TOKEN_URI = 'https://oauth2.googleapis.com/token';

    private const API_BASE = 'https://androidpublisher.googleapis.com/androidpublisher/v3/applications/';

    private const SCOPE = 'https://www.googleapis.com/auth/androidpublisher';

    public function expectedProductIdForCourse(int|string $courseId): string
    {
        return config('services.google_play.product_id_prefix', 'course_') . $courseId;
    }

    public function courseIdFromProductId(string $productId): ?string
    {
        $prefix = config('services.google_play.product_id_prefix', 'course_');

        if (! str_starts_with($productId, $prefix)) {
            return null;
        }

        $courseId = substr($productId, strlen($prefix));

        return $courseId === '' ? null : $courseId;
    }

    /**
     * Verify a purchased product token and ensure it matches the expected product,
     * then acknowledge it (Play auto-refunds unacknowledged purchases after 3 days).
     *
     * @return array{order_id: string, product_id: string}
     */
    public function verifyPurchase(string $productId, string $purchaseToken, string $expectedProductId): array
    {
        $productId = trim($productId);
        $purchaseToken = trim($purchaseToken);

        if ($productId === '' || $purchaseToken === '' || $expectedProductId === '') {
            throw new RuntimeException('Invalid Google Play purchase payload.');
        }

        if ($productId !== $expectedProductId) {
            throw new RuntimeException('Google Play purchase does not match expected product.');
        }

        $packageName = $this->packageName();
        $url = self::API_BASE . rawurlencode($packageName) . '/purchases/products/' . rawurlencode($productId) . '/tokens/' . rawurlencode($purchaseToken);

        $response = Http::withToken($this->getAccessToken())
            ->timeout(20)
            ->get($url);

        if (! $response->successful()) {
            Log::warning('Google Play purchase lookup failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('Unable to verify Google Play purchase.');
        }

        $data = $response->json();

        // purchaseState: 0 = purchased, 1 = cancelled, 2 = pending
        if ((int) ($data['purchaseState'] ?? 1) !== 0) {
            throw new RuntimeException('Google Play purchase is not in a completed state.');
        }

        $orderId = (string) ($data['orderId'] ?? '');
        if ($orderId === '') {
            throw new RuntimeException('Google Play purchase is missing an order ID.');
        }

        if ((int) ($data['acknowledgementState'] ?? 0) === 0) {
            $this->acknowledgePurchase($productId, $purchaseToken);
        }

        return [
            'order_id' => $orderId,
            'product_id' => $productId,
        ];
    }

    private function acknowledgePurchase(string $productId, string $purchaseToken): void
    {
        $packageName = $this->packageName();
        $url = self::API_BASE . rawurlencode($packageName) . '/purchases/products/' . rawurlencode($productId) . '/tokens/' . rawurlencode($purchaseToken) . ':acknowledge';

        $response = Http::withToken($this->getAccessToken())
            ->timeout(20)
            ->post($url);

        if (! $response->successful()) {
            // Not fatal to the purchase flow — Play will keep the purchase purchased,
            // just log so it can be acknowledged manually before the 3-day refund window.
            Log::warning('Google Play purchase acknowledgement failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        }
    }

    /**
     * Create (or update the price/listing of) the Play in-app product for a course.
     * Safe to call repeatedly — used for both new courses and price/name edits.
     */
    public function syncProduct(Course $course): void
    {
        if (! $this->isConfigured()) {
            return;
        }

        $productId = $this->expectedProductIdForCourse($course->id);
        $packageName = $this->packageName();
        $currency = config('services.google_play.default_currency', 'SAR');

        $payload = [
            'packageName' => $packageName,
            'sku' => $productId,
            'status' => 'active',
            'purchaseType' => 'managedUser',
            'defaultPrice' => [
                'priceMicros' => (string) (int) round(((float) $course->price) * 1_000_000),
                'currency' => $currency,
            ],
            'listings' => [
                'en-US' => [
                    'title' => $this->truncate($course->name ?: ('Course #' . $course->id), 55),
                    'description' => $this->truncate(strip_tags((string) $course->description) ?: $course->name, 200),
                ],
            ],
            'defaultLanguage' => 'en-US',
        ];

        $url = self::API_BASE . rawurlencode($packageName) . '/inappproducts/' . rawurlencode($productId);

        $accessToken = $this->getAccessToken();

        // Try update first; fall back to insert if the product doesn't exist yet.
        $response = Http::withToken($accessToken)->timeout(20)->put($url, $payload);

        if ($response->status() === 404) {
            $response = Http::withToken($accessToken)
                ->timeout(20)
                ->post(self::API_BASE . rawurlencode($packageName) . '/inappproducts', $payload);
        }

        if (! $response->successful()) {
            Log::error('Google Play product sync failed', [
                'course_id' => $course->id,
                'product_id' => $productId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new RuntimeException('Failed to sync Google Play product: ' . $response->body());
        }

        Log::info('Google Play product synced', ['course_id' => $course->id, 'product_id' => $productId]);
    }

    public function isConfigured(): bool
    {
        $path = config('services.google_play.service_account_path');

        return (bool) $path && is_file($path);
    }

    private function packageName(): string
    {
        return config('services.google_play.package_name');
    }

    private function getAccessToken(): string
    {
        return Cache::remember('google_play_access_token', 3300, function () {
            $credentials = $this->loadServiceAccount();

            $now = time();
            $jwt = JWT::encode([
                'iss' => $credentials['client_email'],
                'scope' => self::SCOPE,
                'aud' => self::TOKEN_URI,
                'iat' => $now,
                'exp' => $now + 3600,
            ], $credentials['private_key'], 'RS256');

            $response = Http::asForm()->timeout(20)->post(self::TOKEN_URI, [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if (! $response->successful()) {
                Log::error('Google Play OAuth token request failed', ['body' => $response->body()]);
                throw new RuntimeException('Unable to authenticate with Google Play.');
            }

            return $response->json('access_token');
        });
    }

    /**
     * @return array{client_email: string, private_key: string}
     */
    private function loadServiceAccount(): array
    {
        $path = config('services.google_play.service_account_path');

        if (! $path || ! is_file($path)) {
            throw new RuntimeException('Google Play service account credentials are not configured.');
        }

        $data = json_decode(file_get_contents($path), true);

        if (! is_array($data) || empty($data['client_email']) || empty($data['private_key'])) {
            throw new RuntimeException('Google Play service account file is invalid.');
        }

        return [
            'client_email' => $data['client_email'],
            'private_key' => $data['private_key'],
        ];
    }

    private function truncate(string $value, int $length): string
    {
        $value = trim($value);

        return mb_strlen($value) > $length ? mb_substr($value, 0, $length - 1) . '…' : $value;
    }
}
