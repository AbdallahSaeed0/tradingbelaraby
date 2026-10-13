<?php

namespace App\Services\Payment;

use App\Models\Order;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * National Bank of Egypt — Mastercard Gateway (MPGS) Hosted Checkout.
 *
 * Flow: initiateCheckout() creates a session server-side, the browser opens
 * it with checkout.min.js, the gateway redirects back to returnUrl, and
 * retrieveOrder() is the authoritative check of what was actually paid.
 */
class NbeService
{
    protected Client $client;
    protected string $merchantId;

    public function __construct()
    {
        $this->merchantId = (string) config('nbe.merchant_id');

        $this->client = new Client([
            'base_uri' => config('nbe.base_url'),
            'timeout' => 30,
            'auth' => ['merchant.' . $this->merchantId, (string) config('nbe.api_password')],
        ]);
    }

    /**
     * URL of the hosted checkout JS library.
     */
    public function checkoutScriptUrl(): string
    {
        return config('nbe.base_url') . '/static/checkout/checkout.min.js';
    }

    /**
     * Convert an order's SAR total into the amount NBE will charge, in nbe.currency.
     */
    public function chargeAmountForOrder(Order $order): string
    {
        $rate = (float) config('nbe.sar_rate', 13.0);

        return number_format(((float) $order->total) * $rate, 2, '.', '');
    }

    /**
     * INITIATE_CHECKOUT — returns ['session_id' => ..., 'success_indicator' => ...].
     */
    public function initiateCheckout(Order $order, string $returnUrl, string $cancelUrl): array
    {
        $payload = [
            'apiOperation' => 'INITIATE_CHECKOUT',
            'checkoutMode' => 'WEBSITE',
            'interaction' => [
                'operation' => 'PURCHASE',
                'returnUrl' => $returnUrl,
                'cancelUrl' => $cancelUrl,
                'locale' => app()->getLocale() === 'ar' ? 'ar' : 'en_US',
                'merchant' => [
                    'name' => config('nbe.merchant_name'),
                ],
            ],
            'order' => [
                'id' => $order->order_number,
                'amount' => $this->chargeAmountForOrder($order),
                'currency' => config('nbe.currency', 'EGP'),
                'description' => 'Order ' . $order->order_number,
            ],
            'customer' => array_filter([
                'firstName' => $order->billing_first_name,
                'lastName' => $order->billing_last_name,
                'email' => $order->billing_email,
            ]),
        ];

        $response = $this->request('POST', $this->path('/session'), $payload);

        if (($response['result'] ?? null) !== 'SUCCESS' || empty($response['session']['id'])) {
            throw new Exception('NBE INITIATE_CHECKOUT did not return a session: ' . json_encode($response));
        }

        return [
            'session_id' => $response['session']['id'],
            'success_indicator' => $response['successIndicator'] ?? null,
        ];
    }

    /**
     * RETRIEVE ORDER — the gateway's own record of the order and its transactions.
     */
    public function retrieveOrder(string $orderNumber): array
    {
        return $this->request('GET', $this->path('/order/' . rawurlencode($orderNumber)));
    }

    /**
     * True only when the gateway reports the order fully captured for the
     * expected amount and currency. Fails closed on anything else.
     */
    public function isOrderPaid(array $gatewayOrder, Order $order): bool
    {
        return ($gatewayOrder['result'] ?? null) === 'SUCCESS'
            && ($gatewayOrder['status'] ?? null) === 'CAPTURED'
            && ($gatewayOrder['currency'] ?? null) === config('nbe.currency', 'EGP')
            && bccomp((string) ($gatewayOrder['totalCapturedAmount'] ?? '0'), $this->chargeAmountForOrder($order), 2) === 0;
    }

    protected function path(string $suffix): string
    {
        return '/api/rest/version/' . config('nbe.api_version') . '/merchant/' . rawurlencode($this->merchantId) . $suffix;
    }

    protected function request(string $method, string $path, ?array $payload = null): array
    {
        $options = ['headers' => ['Accept' => 'application/json']];
        if ($payload !== null) {
            $options['json'] = $payload;
        }

        try {
            $response = $this->client->request($method, $path, $options);

            return json_decode((string) $response->getBody(), true) ?? [];
        } catch (RequestException $e) {
            $errorBody = $e->getResponse() ? (string) $e->getResponse()->getBody() : $e->getMessage();
            Log::error('NBE API Error', [
                'method' => $method,
                'path' => $path,
                'error' => $errorBody,
            ]);
            throw new Exception('NBE request failed: ' . $errorBody);
        }
    }
}
