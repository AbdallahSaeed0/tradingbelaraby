<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Services\Payment\CyberSource\HttpSignatureAuth;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Exception;

class CyberSourceService
{
    protected Client $client;
    protected HttpSignatureAuth $auth;
    protected string $host;

    public function __construct()
    {
        $baseUrl = config('cybersource.base_url');
        $this->host = parse_url($baseUrl, PHP_URL_HOST);

        $this->client = new Client([
            'base_uri' => $baseUrl,
            'timeout' => 30,
        ]);

        $this->auth = new HttpSignatureAuth(
            (string) config('cybersource.merchant_id'),
            (string) config('cybersource.key'),
            (string) config('cybersource.secret'),
        );
    }

    /**
     * Convert an order's SAR total into the USD amount CyberSource will charge.
     */
    public function usdAmountForOrder(Order $order): string
    {
        $rate = (float) config('cybersource.sar_to_usd_rate', 3.75);

        return number_format(((float) $order->total) / $rate, 2, '.', '');
    }

    /**
     * Generate a one-time-use JWT capture context for Unified Checkout.
     */
    public function generateCaptureContext(Order $order): string
    {
        $payload = [
            'clientVersion' => config('cybersource.client_version'),
            'targetOrigins' => config('cybersource.target_origins'),
            'allowedCardNetworks' => ['VISA', 'MASTERCARD', 'AMEX'],
            'allowedPaymentTypes' => ['PANENTRY'],
            'country' => 'US',
            'locale' => app()->getLocale() === 'ar' ? 'ar_SA' : 'en_US',
            'data' => [
                'orderInformation' => [
                    'amountDetails' => [
                        'totalAmount' => $this->usdAmountForOrder($order),
                        'currency' => config('cybersource.currency', 'USD'),
                    ],
                ],
            ],
        ];

        // The Sessions API responds with the JWT as a raw text body.
        return $this->post('/uc/v1/sessions', $payload);
    }

    /**
     * Fetch a transaction's authoritative status directly from CyberSource.
     * Never trust the client-supplied result JWT alone — always re-confirm here.
     */
    public function getPayment(string $paymentId): array
    {
        $resourcePath = '/tss/v2/transactions/' . $paymentId;
        $headers = $this->auth->headers('GET', $this->host, $resourcePath);
        $headers['Accept'] = 'application/json;charset=utf-8';

        try {
            $response = $this->client->get($resourcePath, ['headers' => $headers]);

            return json_decode((string) $response->getBody(), true) ?? [];
        } catch (RequestException $e) {
            $errorBody = $e->getResponse() ? (string) $e->getResponse()->getBody() : $e->getMessage();
            Log::error('CyberSource Get Payment Error', [
                'payment_id' => $paymentId,
                'error' => $errorBody,
            ]);
            throw new Exception('Failed to fetch CyberSource payment status: ' . $errorBody);
        }
    }

    /**
     * Decode the JWT payload without verifying its signature. Only safe to use
     * to pull out a pointer (the payment id) — the actual status must always
     * come from getPayment(), never from this decoded payload.
     */
    public function decodeUnverifiedJwtPayload(string $jwt): array
    {
        $parts = explode('.', $jwt);
        if (count($parts) < 2) {
            return [];
        }

        $payload = base64_decode(strtr($parts[1], '-_', '+/'));

        return json_decode($payload, true) ?? [];
    }

    protected function post(string $resourcePath, array $payload): string
    {
        $body = json_encode($payload);
        $headers = $this->auth->headers('POST', $this->host, $resourcePath, $body);
        $headers['Content-Type'] = 'application/json;charset=utf-8';
        $headers['Accept'] = 'application/json;charset=utf-8';

        try {
            $response = $this->client->post($resourcePath, [
                'headers' => $headers,
                'body' => $body,
            ]);

            return (string) $response->getBody();
        } catch (RequestException $e) {
            $errorBody = $e->getResponse() ? (string) $e->getResponse()->getBody() : $e->getMessage();
            Log::error('CyberSource API Error', [
                'resource' => $resourcePath,
                'error' => $errorBody,
            ]);
            throw new Exception('CyberSource request failed: ' . $errorBody);
        }
    }
}
