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
     * Create the IAP for a course if needed, fill in any missing metadata, and keep its price in sync.
     * Safe to call repeatedly — a product left half-configured by an earlier failure gets completed.
     * Existing names/descriptions are never edited, since that forces re-review of an approved IAP.
     * With $dryRun, nothing is written to App Store Connect; the planned steps are returned instead.
     *
     * @return list<string> human-readable steps performed (or planned)
     */
    public function syncProduct(Course $course, bool $dryRun = false): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('App Store Connect API is not configured (APPLE_ASC_KEY_ID / key file).');
        }

        $steps = [];
        $productId = $this->appleIapService->expectedProductIdForCourse($course->id);
        $appId = $this->appId();
        $existing = $this->findInAppPurchase($appId, $productId);

        if ($existing === null) {
            $steps[] = "create product {$productId} (reference name \"Course {$course->id}\")";
            $iapId = $dryRun ? null : $this->createInAppPurchase($appId, $course, $productId);
            $state = 'MISSING_METADATA';
        } else {
            $iapId = $existing['id'];
            $state = $existing['attributes']['state'] ?? 'UNKNOWN';
            $steps[] = "product {$productId} exists (state {$state})";
        }

        $existingLocales = $iapId ? $this->existingLocales($iapId) : [];
        foreach ($this->localizations($course) as $locale => [$name, $description]) {
            if (in_array($locale, $existingLocales, true)) {
                continue;
            }
            $steps[] = "add {$locale} name \"{$name}\" / description \"{$description}\"";
            if (! $dryRun) {
                $this->createLocalization($iapId, $locale, $name, $description);
            }
        }

        $territory = config('services.apple.asc_base_territory', 'SAU');
        [$pricePointId, $applePrice] = $this->closestPricePoint($iapId ?? $this->anyInAppPurchaseId($appId), $territory, (float) $course->price);
        $steps[] = "set price {$applePrice} ({$territory} base, course price {$course->price})";
        if (! $dryRun) {
            $this->setPrice($iapId, $territory, $pricePointId);
        }

        if (! $iapId || ! $this->hasAvailability($iapId)) {
            $steps[] = 'make available in all territories';
            if (! $dryRun) {
                $this->setAvailability($iapId);
            }
        }

        $hasScreenshot = $iapId && $this->hasCompleteScreenshot($iapId, $dryRun);
        if (! $hasScreenshot) {
            $path = config('services.apple.asc_review_screenshot_path');
            if ($path && is_file($path)) {
                [$width, $height] = getimagesize($path) ?: [0, 0];
                $steps[] = 'upload review screenshot ' . basename($path) . " ({$width}x{$height})";
                $hasScreenshot = $dryRun || $this->uploadReviewScreenshot($iapId, $path);
            } else {
                $steps[] = "WARNING: no review screenshot at {$path}; add one in App Store Connect";
            }
        }

        if (! $hasScreenshot || ! in_array($state, ['MISSING_METADATA', 'READY_TO_SUBMIT'], true)) {
            // Nothing to submit (already in review/approved, or still missing the screenshot).
        } elseif (! $this->appIsLive($appId)) {
            $steps[] = 'not submitted: app has no released version yet — include this IAP with the app version submission';
        } else {
            $steps[] = 'submit for review';
            if (! $dryRun) {
                $this->submitForReview($iapId, $productId);
            }
        }

        if (! $dryRun) {
            Log::info('App Store IAP synced', ['course_id' => $course->id, 'product_id' => $productId, 'steps' => $steps]);
        }

        return $steps;
    }

    /**
     * Apple only accepts a standalone IAP submission once the app has a released version;
     * before that, IAPs must be submitted together with an app version.
     */
    private function appIsLive(string $appId): bool
    {
        $states = collect($this->request('get', "/v1/apps/{$appId}/appStoreVersions", [
            'fields[appStoreVersions]' => 'appStoreState',
            'limit' => 200,
        ])->json('data', []))->pluck('attributes.appStoreState');

        return $states->contains(fn ($s) => in_array($s, ['READY_FOR_SALE', 'REPLACED_WITH_NEW_VERSION'], true));
    }

    /**
     * @return list<string>
     */
    private function existingLocales(string $iapId): array
    {
        return collect($this->request('get', "/v2/inAppPurchases/{$iapId}/inAppPurchaseLocalizations")->json('data', []))
            ->pluck('attributes.locale')->all();
    }

    private function hasAvailability(string $iapId): bool
    {
        $response = Http::withToken($this->authToken())->acceptJson()->timeout(30)
            ->get(self::API_BASE . "/v2/inAppPurchases/{$iapId}/inAppPurchaseAvailability");

        if ($response->status() === 404) {
            return false;
        }

        if (! $response->successful()) {
            throw new RuntimeException("App Store Connect API get availability failed (HTTP {$response->status()})");
        }

        return ! empty($response->json('data'));
    }

    /**
     * A screenshot whose upload never completed is deleted (unless dry-run) so it can be re-uploaded.
     */
    private function hasCompleteScreenshot(string $iapId, bool $dryRun): bool
    {
        $screenshot = $this->request('get', "/v2/inAppPurchases/{$iapId}/appStoreReviewScreenshot")->json('data');
        if (empty($screenshot)) {
            return false;
        }

        if (($screenshot['attributes']['assetDeliveryState']['state'] ?? null) === 'COMPLETE') {
            return true;
        }

        if (! $dryRun) {
            $this->request('delete', "/v1/inAppPurchaseAppStoreReviewScreenshots/{$screenshot['id']}");
        }

        return false;
    }

    private function anyInAppPurchaseId(string $appId): string
    {
        $id = $this->request('get', "/v1/apps/{$appId}/inAppPurchasesV2", ['limit' => 1])->json('data.0.id');
        if (! $id) {
            throw new RuntimeException('Dry run needs at least one existing IAP to look up price points.');
        }

        return $id;
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

    /**
     * @return array{id: string, attributes: array}|null
     */
    private function findInAppPurchase(string $appId, string $productId): ?array
    {
        return $this->request('get', "/v1/apps/{$appId}/inAppPurchasesV2", ['filter[productId]' => $productId])
            ->json('data.0');
    }

    private function createInAppPurchase(string $appId, Course $course, string $productId): string
    {
        $response = $this->request('post', '/v2/inAppPurchases', [
            'data' => [
                'type' => 'inAppPurchases',
                'attributes' => [
                    'name' => "Course {$course->id}",
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

    /**
     * Arabic is always added; English only when the course has a Latin-script name to show.
     *
     * @return array<string, array{0: string, 1: string}> locale => [name (≤30), description (≤45)]
     */
    private function localizations(Course $course): array
    {
        $candidates = [
            'ar-SA' => [$course->name_ar ?: $course->name, $course->description_ar ?: $course->description],
            'en-US' => [$course->name, $course->description],
        ];

        $result = [];
        foreach ($candidates as $locale => [$name, $description]) {
            $name = $this->truncate((string) $name, 30);
            if ($name === '' || ($locale === 'en-US' && ! preg_match('/[A-Za-z]/', $name))) {
                continue;
            }

            $plain = html_entity_decode(strip_tags((string) $description), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $description = $this->truncate($plain, 45) ?: $name;

            $result[$locale] = [$name, $description];
        }

        return $result;
    }

    private function createLocalization(string $iapId, string $locale, string $name, string $description): void
    {
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

    private function setPrice(string $iapId, string $territory, string $pricePointId): void
    {
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
     *
     * @return array{0: string, 1: float} [price point ID, Apple customer price]
     */
    private function closestPricePoint(string $iapId, string $territory, float $price): array
    {
        $bestId = null;
        $bestPrice = 0.0;
        $bestDiff = PHP_FLOAT_MAX;
        $path = "/v2/inAppPurchases/{$iapId}/pricePoints";
        $query = ['filter[territory]' => $territory, 'limit' => 200];
        $pages = 0;

        while ($path !== null) {
            if (++$pages > 20) {
                throw new RuntimeException('Too many price point pages from App Store Connect.');
            }

            $response = $this->request('get', $path, $query);

            foreach ($response->json('data', []) as $point) {
                $pointPrice = (float) ($point['attributes']['customerPrice'] ?? 0);
                $diff = abs($pointPrice - $price);
                if ($diff < $bestDiff) {
                    $bestDiff = $diff;
                    $bestId = $point['id'];
                    $bestPrice = $pointPrice;
                }
            }

            $next = $response->json('links.next');
            $path = $next ? substr($next, strlen(self::API_BASE)) : null;
            $query = [];
        }

        if ($bestId === null) {
            throw new RuntimeException("No App Store price points found for territory {$territory}.");
        }

        return [$bestId, $bestPrice];
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
     */
    private function uploadReviewScreenshot(string $iapId, string $path): bool
    {
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

        // Passing an empty query array to get() would wipe the query string already in $path
        // (e.g. the cursor in a "links.next" pagination URL), so only pass it when non-empty.
        $response = match (true) {
            $method === 'get' && $data === [] => $client->get(self::API_BASE . $path),
            $method === 'get' => $client->get(self::API_BASE . $path, $data),
            default => $client->{$method}(self::API_BASE . $path, $data),
        };

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

    /**
     * Apple counts length in UTF-16 code units, so emoji count as 2 characters.
     */
    private function truncate(string $value, int $length): string
    {
        // Apple rejects emoji and other symbol characters in IAP names/descriptions.
        $value = preg_replace('/[\p{So}\p{Sk}\p{Cs}\p{Co}\p{Cn}\x{FE0F}\x{FE0E}\x{200D}\x{20E3}]/u', '', $value);
        $value = strtr($value, ['“' => '"', '”' => '"', '‘' => "'", '’' => "'"]);
        $value = trim(preg_replace('/\s+/u', ' ', $value));

        if ($this->appleLength($value) <= $length) {
            return $value;
        }

        while ($value !== '' && $this->appleLength($value . '...') > $length) {
            $value = mb_substr($value, 0, -1);
        }

        return rtrim($value) . '...';
    }

    private function appleLength(string $value): int
    {
        return intdiv(strlen(mb_convert_encoding($value, 'UTF-16LE', 'UTF-8')), 2);
    }
}
