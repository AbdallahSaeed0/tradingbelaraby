<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class AppleIapService
{
    private const PRODUCTION_URL = 'https://buy.itunes.apple.com/verifyReceipt';

    private const SANDBOX_URL = 'https://sandbox.itunes.apple.com/verifyReceipt';

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
     * Verify an App Store receipt and ensure the transaction matches the expected product.
     *
     * @return array{transaction_id: string, product_id: string}
     */
    public function verifyPurchase(string $receiptData, string $transactionId, string $expectedProductId): array
    {
        $receiptData = trim($receiptData);
        $transactionId = trim($transactionId);
        $expectedProductId = trim($expectedProductId);

        if ($receiptData === '' || $transactionId === '' || $expectedProductId === '') {
            throw new RuntimeException('Invalid App Store purchase payload.');
        }

        $response = $this->verifyReceipt($receiptData, self::PRODUCTION_URL);

        if ((int) ($response['status'] ?? -1) === 21007) {
            $response = $this->verifyReceipt($receiptData, self::SANDBOX_URL);
        }

        $status = (int) ($response['status'] ?? -1);
        if ($status !== 0) {
            Log::warning('Apple IAP receipt verification failed', [
                'status' => $status,
                'transaction_id' => $transactionId,
                'expected_product_id' => $expectedProductId,
            ]);
            throw new RuntimeException('App Store receipt verification failed.');
        }

        $transaction = $this->findTransaction($response, $transactionId, $expectedProductId);
        if ($transaction === null) {
            throw new RuntimeException('App Store transaction not found in receipt.');
        }

        return $transaction;
    }

    private function verifyReceipt(string $receiptData, string $url): array
    {
        $payload = [
            'receipt-data' => $receiptData,
            'exclude-old-transactions' => true,
        ];

        $sharedSecret = config('services.apple.iap_shared_secret');
        if (is_string($sharedSecret) && $sharedSecret !== '') {
            $payload['password'] = $sharedSecret;
        }

        $httpResponse = Http::timeout(20)->post($url, $payload);

        if (! $httpResponse->successful()) {
            throw new RuntimeException('Unable to contact App Store verification service.');
        }

        return $httpResponse->json() ?? [];
    }

    /**
     * @param  array<string, mixed>  $response
     * @return array{transaction_id: string, product_id: string}|null
     */
    private function findTransaction(array $response, string $transactionId, string $expectedProductId): ?array
    {
        $candidates = [];

        if (isset($response['latest_receipt_info']) && is_array($response['latest_receipt_info'])) {
            $candidates = array_merge($candidates, $response['latest_receipt_info']);
        }

        if (isset($response['receipt']['in_app']) && is_array($response['receipt']['in_app'])) {
            $candidates = array_merge($candidates, $response['receipt']['in_app']);
        }

        foreach ($candidates as $entry) {
            if (! is_array($entry)) {
                continue;
            }

            $entryTransactionId = (string) ($entry['transaction_id'] ?? $entry['original_transaction_id'] ?? '');
            $entryProductId = (string) ($entry['product_id'] ?? '');

            if ($entryTransactionId === $transactionId && $entryProductId === $expectedProductId) {
                return [
                    'transaction_id' => $entryTransactionId,
                    'product_id' => $entryProductId,
                ];
            }
        }

        return null;
    }
}
