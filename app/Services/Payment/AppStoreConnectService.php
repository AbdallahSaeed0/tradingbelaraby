<?php

namespace App\Services\Payment;

use App\Models\Course;
use Firebase\JWT\JWT;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Keeps App Store Connect non-consumable In-App Purchases in sync with paid courses,
 * via the App Store Connect API (https://developer.apple.com/documentation/appstoreconnectapi).
 *
 * Unlike Google Play, every new product must be reviewed by Apple before it can be bought,
 * so syncProduct() creates and submits the product; it becomes purchasable after approval.
 */
class AppStoreConnectService
{
    private const API_BASE = 'https://api.appstoreconnect.apple.com';

    public function __construct(private readonly AppleIapService $appleIapService)
    {
    }

    public function isConfigured(): bool
    {
        $path = config('services.apple.asc_private_key_path');

        return config('services.apple.asc_key_id')
            && config('services.apple.iap_issuer_id')
            && $path && is_file($path);
    }

    /**
     * Create the IAP for a course if it doesn't exist yet, otherwise keep its price in sync.
     * Safe to call repeatedly.
     *
     * @return string 'created' | 'updated'
     */
    public function syncProduct(Course $course): string
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('App Store Connect API is not configured (APPLE_ASC_KEY_ID / key file).');
        }

        $productId = $this->appleIapService->expectedProductIdForCourse($course->id);
        $appId = $this->appId();

        $existingId = $this->findInAppPurchaseId($appId, $productId);
        if ($existingId !== null) {
            // Name/description edits on an approved IAP require re-review, so only the price is synced.
            $this->setPrice($existingId, (float) $course->price);
            Log::info('App Store IAP price synced', ['course_id' => $course->id, 'product_id' => $productId]);

            return 'updated';
        }

        $iapId = $this->createInAppPurchase($appId, $course, $productId);
        $this->createLocalizations($iapId, $course);
        $this->setPrice($iapId, (float) $course->price);
        $this->setAvailability($iapId);

        if ($this->uploadReviewScreenshot($iapId)) {
            $this->submitForReview($iapId, $productId);
        }

        Log::info('App Store IAP created', ['course_id' => $course->id, 'product_id' => $productId]);

        return 'created';
    }

    private function appId(): string
    {
        $bundleId = config('services.apple.iap_bundle_id');

        return Cache::remember('asc_app_id_' . $bundleId, 86400, function () use ($bundleId) {
            $id = $this->request('get', '/v1/apps', ['filter[bundleId]' => $bundleId, 'fields[apps]' => 'bundleId'])
                ->json('data.0.id');

            if (! $id) {
                throw new RuntimeException("App with bundle ID {$bundleId} not found in App Store Connect.");
            }

            return $id;
        });
    }

    private function findInAppPurchaseId(string $appId, string $productId): ?string
    {
        return $this->request('get', "/v1/apps/{$appId}/inAppPurchasesV2", ['filter[productId]' => $productId])
            ->json('data.0.id');
    }

    private function createInAppPurchase(string $appId, Course $course, string $productId): string
    {
        $response = $this->request('post', '/v2/inAppPurchases', [
            'data' => [
                'type' => 'inAppPurchases',
                'attributes' => [
                    'name' => $this->truncate("Course {$course->id} - " . ($course->name ?: $course->name_ar), 64),
                    'productId' => $productId,
                    'inAppPurchaseType' => 'NON_CONSUMABLE',
                    'familySharable' => false,
                    'reviewNote' => 'Unlocks lifetime access to the paid course "' . $this->truncate((string) $course->name, 100)
                        . '" in the app. Purchase it from the course page: Buy Now > Checkout > App Store.',
                ],
                'relationships' => [
                    'app' => ['data' => ['type' => 'apps', 'id' => $appId]],
                ],
            ],
        ]);

        return $response->json('data.id');
    }

    private function createLocalizations(string $iapId, Course $course): void
    {
        $locales = [
            'ar-SA' => [$course->name_ar ?: $course->name, $course->description_ar ?: $course->description],
            'en-US' => [$course->name ?: $course->name_ar, $course->description ?: $course->description_ar],
        ];

        foreach ($locales as $locale => [$name, $description]) {
            $name = $this->truncate((string) $name, 30);
            $description = $this->truncate(strip_tags((string) $description) ?: $name, 45);

            $this->request('post', '/v1/inAppPurchaseLocalizations', [
                'data' => [
                    'type' => 'inAppPurchaseLocalizations',
                    'attributes' => [
                        'locale' => $locale,
                        'name' => $name,
                        'description' => $description,
                    ],
                    'relationships' => [
                        'inAppPurchaseV2' => ['data' => ['type' => 'inAppPurchases', 'id' => $iapId]],
                    ],
                ],
            ]);
        }
    }

    private function setPrice(string $iapId, float $price): void
    {
        $territory = config('services.apple.asc_base_territory', 'SAU');
        $pricePointId = $this->closestPricePointId($iapId, $territory, $price);

        $this->request('post', '/v1/inAppPurchasePriceSchedules', [
            'data' => [
                'type' => 'inAppPurchasePriceSchedules',
                'relationships' => [
                    'inAppPurchase' => ['data' => ['type' => 'inAppPurchases', 'id' => $iapId]],
                    'baseTerritory' => ['data' => ['type' => 'territories', 'id' => $territory]],
                    'manualPrices' => ['data' => [['type' => 'inAppPurchasePrices', 'id' => '${price1}']]],
                ],
            ],
            'included' => [
                [
                    'type' => 'inAppPurchasePrices',
                    'id' => '${price1}',
                    'attributes' => ['startDate' => null],
                    'relationships' => [
                        'inAppPurchasePricePoint' => ['data' => ['type' => 'inAppPurchasePricePoints', 'id' => $pricePointId]],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Apple only allows fixed price points, so pick the one nearest to the course price.
     */
    private function closestPricePointId(string $iapId, string $territory, float $price): string
    {
        $bestId = null;
        $bestDiff = PHP_FLOAT_MAX;
        $path = "/v2/inAppPurchases/{$iapId}/pricePoints";
        $query = ['filter[territory]' => $territory, 'limit' => 200];

        while ($path !== null) {
            $response = $this->request('get', $path, $query);

            foreach ($response->json('data', []) as $point) {
                $diff = abs((float) ($point['attributes']['customerPrice'] ?? 0) - $price);
                if ($diff < $bestDiff) {
                    $bestDiff = $diff;
                    $bestId = $point['id'];
                }
            }

            $next = $response->json('links.next');
            $path = $next ? substr($next, strlen(self::API_BASE)) : null;
            $query = [];
        }

        if ($bestId === null) {
            throw new RuntimeException("No App Store price points found for territory {$territory}.");
        }

        return $bestId;
    }

    private function setAvailability(string $iapId): void
    {
        $territories = Cache::remember('asc_territories', 86400, function () {
            return collect($this->request('get', '/v1/territories', ['limit' => 200])->json('data', []))
                ->pluck('id')->all();
        });

        $this->request('post', '/v1/inAppPurchaseAvailabilities', [
            'data' => [
                'type' => 'inAppPurchaseAvailabilities',
                'attributes' => ['availableInNewTerritories' => true],
                'relationships' => [
                    'inAppPurchase' => ['data' => ['type' => 'inAppPurchases', 'id' => $iapId]],
                    'availableTerritories' => [
                        'data' => array_map(fn ($id) => ['type' => 'territories', 'id' => $id], $territories),
                    ],
                ],
            ],
        ]);
    }

    /**
     * Apple requires a review screenshot before an IAP can be submitted.
     * Returns false (and leaves the product as "Missing Metadata") if none is configured.
     */
    private function uploadReviewScreenshot(string $iapId): bool
    {
        $path = config('services.apple.asc_review_screenshot_path');
        if (! $path || ! is_file($path)) {
            Log::warning('App Store IAP created without review screenshot; add one in App Store Connect and submit it manually.', [
                'iap_id' => $iapId,
                'expected_path' => $path,
            ]);

            return false;
        }

        $bytes = file_get_contents($path);

        $reservation = $this->request('post', '/v1/inAppPurchaseAppStoreReviewScreenshots', [
            'data' => [
                'type' => 'inAppPurchaseAppStoreReviewScreenshots',
                'attributes' => ['fileName' => basename($path), 'fileSize' => strlen($bytes)],
                'relationships' => [
                    'inAppPurchaseV2' => ['data' => ['type' => 'inAppPurchases', 'id' => $iapId]],
                ],
            ],
        ]);

        foreach ($reservation->json('data.attributes.uploadOperations', []) as $operation) {
            $headers = collect($operation['requestHeaders'] ?? [])->pluck('value', 'name')->all();
            $chunk = substr($bytes, $operation['offset'], $operation['length']);

            $upload = Http::withHeaders($headers)
                ->withBody($chunk, $headers['Content-Type'] ?? 'application/octet-stream')
                ->timeout(60)
                ->send($operation['method'], $operation['url']);

            if (! $upload->successful()) {
                throw new RuntimeException('Failed to upload IAP review screenshot: HTTP ' . $upload->status());
            }
        }

        $screenshotId = $reservation->json('data.id');
        $this->request('patch', "/v1/inAppPurchaseAppStoreReviewScreenshots/{$screenshotId}", [
            'data' => [
                'type' => 'inAppPurchaseAppStoreReviewScreenshots',
                'id' => $screenshotId,
                'attributes' => ['uploaded' => true, 'sourceFileChecksum' => md5($bytes)],
            ],
        ]);

        return true;
    }

    private function submitForReview(string $iapId, string $productId): void
    {
        try {
            $this->request('post', '/v1/inAppPurchaseSubmissions', [
                'data' => [
                    'type' => 'inAppPurchaseSubmissions',
                    'relationships' => [
                        'inAppPurchaseV2' => ['data' => ['type' => 'inAppPurchases', 'id' => $iapId]],
                    ],
                ],
            ]);
        } catch (RuntimeException $e) {
            // Apple rejects standalone IAP submissions until the app has an approved version;
            // the product stays "Ready to Submit" and can be included with the next app version.
            Log::warning('App Store IAP created but not submitted for review', [
                'product_id' => $productId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function request(string $method, string $path, array $data = []): Response
    {
        $client = Http::withToken($this->authToken())->acceptJson()->timeout(30);

        $response = $method === 'get'
            ? $client->get(self::API_BASE . $path, $data)
            : $client->{$method}(self::API_BASE . $path, $data);

        if (! $response->successful()) {
            Log::error('App Store Connect API request failed', [
                'method' => strtoupper($method),
                'path' => $path,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            $detail = $response->json('errors.0.detail') ?? $response->body();
            throw new RuntimeException("App Store Connect API {$method} {$path} failed (HTTP {$response->status()}): {$detail}");
        }

        return $response;
    }

    private function authToken(): string
    {
        // Apple rejects tokens valid for more than 20 minutes; cache for slightly less.
        return Cache::remember('asc_api_token', 900, function () {
            $now = time();

            return JWT::encode([
                'iss' => config('services.apple.iap_issuer_id'),
                'iat' => $now,
                'exp' => $now + 1200,
                'aud' => 'appstoreconnect-v1',
            ], file_get_contents(config('services.apple.asc_private_key_path')), 'ES256', config('services.apple.asc_key_id'));
        });
    }

    private function truncate(string $value, int $length): string
    {
        $value = trim(preg_replace('/\s+/u', ' ', $value));

        return mb_strlen($value) > $length ? mb_substr($value, 0, $length - 1) . '…' : $value;
    }
}
