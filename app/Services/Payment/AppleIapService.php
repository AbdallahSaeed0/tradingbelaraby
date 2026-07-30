<?php

namespace App\Services\Payment;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AppleIapService
{
    private const PRODUCTION_URL = 'https://api.storekit.itunes.apple.com/inApps/v1/transactions/';

    private const SANDBOX_URL = 'https://api.storekit-sandbox.itunes.apple.com/inApps/v1/transactions/';

    /** @var array<string, string> */
    private const PRODUCT_ID_OVERRIDES = [
        // Original ...course.32 was deleted in App Store Connect and cannot be recreated.
        '32' => 'com.education.coursesApp.courses.32',
    ];

    public function expectedProductIdForCourse(int|string $courseId): string
    {
        $courseId = (string) $courseId;

        return self::PRODUCT_ID_OVERRIDES[$courseId]
            ?? 'com.education.coursesApp.course.' . $courseId;
    }

    /**
     * Verify an App Store purchase via the App Store Server API and ensure the
     * transaction matches the expected product.
     *
     * The `$receiptData` parameter from the client is ignored for verification purposes:
     * with StoreKit2 (the default in the in_app_purchase_storekit plugin), the client only
     * has a per-transaction JWS, not a legacy whole-app receipt, so the App Store Server API
     * (looked up by transaction ID) is the only reliable way to verify a purchase server-side.
     *
     * @return array{transaction_id: string, product_id: string}
     */
    public function verifyPurchase(string $receiptData, string $transactionId, string $expectedProductId): array
    {
        $transactionId = trim($transactionId);
        $expectedProductId = trim($expectedProductId);

        if ($transactionId === '' || $expectedProductId === '') {
            throw new RuntimeException('Invalid App Store purchase payload.');
        }

        $signedTransactionInfo = $this->fetchSignedTransaction($transactionId, self::PRODUCTION_URL);
        if ($signedTransactionInfo === null) {
            $signedTransactionInfo = $this->fetchSignedTransaction($transactionId, self::SANDBOX_URL);
        }

        if ($signedTransactionInfo === null) {
            throw new RuntimeException('App Store transaction not found.');
        }

        $payload = $this->decodeAndVerifyJws($signedTransactionInfo);

        $bundleId = config('services.apple.iap_bundle_id');
        if (($payload['bundleId'] ?? null) !== $bundleId) {
            throw new RuntimeException('App Store transaction bundle ID mismatch.');
        }

        $entryTransactionId = (string) ($payload['transactionId'] ?? '');
        $entryProductId = (string) ($payload['productId'] ?? '');

        if ($entryTransactionId !== $transactionId || $entryProductId !== $expectedProductId) {
            throw new RuntimeException('App Store transaction does not match expected purchase.');
        }

        return [
            'transaction_id' => $entryTransactionId,
            'product_id' => $entryProductId,
        ];
    }

    /**
     * Calls GET /inApps/v1/transactions/{transactionId}. Returns the signedTransactionInfo
     * JWS string, or null if this environment (production/sandbox) doesn't have the transaction.
     */
    private function fetchSignedTransaction(string $transactionId, string $baseUrl): ?string
    {
        $jwt = $this->buildApiAuthToken();

        $httpResponse = Http::withToken($jwt)
            ->timeout(20)
            ->get($baseUrl . $transactionId);

        if ($httpResponse->status() === 404) {
            return null;
        }

        if (! $httpResponse->successful()) {
            Log::warning('App Store Server API request failed', [
                'status' => $httpResponse->status(),
                'body' => $httpResponse->body(),
            ]);
            throw new RuntimeException(
                'Unable to contact App Store verification service. HTTP ' . $httpResponse->status() . ': ' . $httpResponse->body()
            );
        }

        $signedTransactionInfo = $httpResponse->json('signedTransactionInfo');
        if (! is_string($signedTransactionInfo) || $signedTransactionInfo === '') {
            throw new RuntimeException('App Store verification service returned an invalid response.');
        }

        return $signedTransactionInfo;
    }

    /**
     * Builds the ES256-signed JWT used to authenticate to the App Store Server API.
     */
    private function buildApiAuthToken(): string
    {
        $keyId = config('services.apple.iap_key_id');
        $issuerId = config('services.apple.iap_issuer_id');
        $bundleId = config('services.apple.iap_bundle_id');
        $privateKeyPath = config('services.apple.iap_private_key_path');

        if (! $keyId || ! $issuerId || ! $bundleId || ! $privateKeyPath || ! is_file($privateKeyPath)) {
            throw new RuntimeException('App Store Server API is not configured.');
        }

        $privateKey = file_get_contents($privateKeyPath);

        $now = time();
        $payload = [
            'iss' => $issuerId,
            'iat' => $now,
            'exp' => $now + 1200,
            'aud' => 'appstoreconnect-v1',
            'bid' => $bundleId,
        ];

        return JWT::encode($payload, $privateKey, 'ES256', $keyId);
    }

    /**
     * Verifies the JWS signature (using the leaf certificate Apple embeds in the JWS header)
     * and returns the decoded payload as an array.
     *
     * @return array<string, mixed>
     */
    private function decodeAndVerifyJws(string $jws): array
    {
        $parts = explode('.', $jws);
        if (count($parts) !== 3) {
            throw new RuntimeException('Malformed App Store transaction data.');
        }

        $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
        $x5c = $header['x5c'] ?? null;
        if (! is_array($x5c) || empty($x5c)) {
            throw new RuntimeException('App Store transaction is missing its signing certificate.');
        }

        $leafCertPem = "-----BEGIN CERTIFICATE-----\n" . chunk_split($x5c[0], 64, "\n") . "-----END CERTIFICATE-----\n";
        $publicKey = openssl_pkey_get_public($leafCertPem);
        if ($publicKey === false) {
            throw new RuntimeException('Unable to read App Store transaction signing certificate.');
        }

        $publicKeyDetails = openssl_pkey_get_details($publicKey);
        $publicKeyPem = $publicKeyDetails['key'] ?? null;
        if (! $publicKeyPem) {
            throw new RuntimeException('Unable to read App Store transaction signing certificate.');
        }

        $decoded = JWT::decode($jws, new Key($publicKeyPem, 'ES256'));

        return (array) $decoded;
    }
}
