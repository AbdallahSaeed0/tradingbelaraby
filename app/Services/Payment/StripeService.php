<?php

namespace App\Services\Payment;

use App\Models\Order;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Stripe Checkout (hosted payment page).
 *
 * Flow: createCheckoutSession() returns a hosted URL the browser is sent to,
 * Stripe redirects back to success_url with the session id, and
 * retrieveSession() is the authoritative check of what was actually paid.
 * The checkout.session.completed webhook confirms the same order in case
 * the payer never makes it back to the site.
 */
class StripeService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.stripe.com',
            'timeout' => 30,
            'auth' => [(string) config('stripe.secret_key'), ''],
        ]);
    }

    /**
     * Convert an order's SAR total into Stripe's smallest currency unit
     * (halalas / cents) in stripe.currency.
     */
    public function chargeAmountForOrder(Order $order): int
    {
        $rate = (float) config('stripe.sar_rate', 1.0);

        return (int) round(((float) $order->total) * $rate * 100);
    }

    /**
     * Create a Checkout Session — returns Stripe's session object (with 'id' and 'url').
     */
    public function createCheckoutSession(Order $order, string $successUrl, string $cancelUrl): array
    {
        $payload = [
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'client_reference_id' => (string) $order->id,
            'customer_email' => $order->billing_email ?: null,
            // Checkout has no Arabic locale; 'auto' follows the browser language.
            'locale' => app()->getLocale() === 'en' ? 'en' : 'auto',
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => config('stripe.currency', 'sar'),
                    'unit_amount' => $this->chargeAmountForOrder($order),
                    'product_data' => [
                        'name' => 'Order ' . $order->order_number,
                    ],
                ],
            ]],
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ],
            'payment_intent_data' => [
                'description' => 'Order ' . $order->order_number,
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ],
            ],
        ];

        $session = $this->request('POST', '/v1/checkout/sessions', array_filter($payload, fn ($v) => $v !== null));

        if (empty($session['id']) || empty($session['url'])) {
            throw new Exception('Stripe did not return a checkout session: ' . json_encode($session));
        }

        return $session;
    }

    /**
     * Retrieve a Checkout Session — Stripe's own record of what was paid.
     */
    public function retrieveSession(string $sessionId): array
    {
        return $this->request('GET', '/v1/checkout/sessions/' . rawurlencode($sessionId));
    }

    /**
     * True only when the session belongs to this order and is paid in full
     * for the expected amount and currency. Fails closed on anything else.
     */
    public function isSessionPaid(array $session, Order $order): bool
    {
        return ($session['client_reference_id'] ?? null) === (string) $order->id
            && ($session['payment_status'] ?? null) === 'paid'
            && strtolower((string) ($session['currency'] ?? '')) === config('stripe.currency', 'sar')
            && (int) ($session['amount_total'] ?? -1) === $this->chargeAmountForOrder($order);
    }

    /**
     * Verify a webhook's Stripe-Signature header and return the decoded event.
     */
    public function constructWebhookEvent(string $payload, string $signatureHeader, int $tolerance = 300): array
    {
        $secret = (string) config('stripe.webhook_secret');
        if ($secret === '') {
            throw new Exception('STRIPE_WEBHOOK_SECRET is not configured.');
        }

        $timestamp = null;
        $signatures = [];
        foreach (explode(',', $signatureHeader) as $part) {
            [$key, $value] = array_pad(explode('=', trim($part), 2), 2, null);
            if ($key === 't') {
                $timestamp = (int) $value;
            } elseif ($key === 'v1' && $value) {
                $signatures[] = $value;
            }
        }

        if (!$timestamp || empty($signatures)) {
            throw new Exception('Malformed Stripe-Signature header.');
        }

        if (abs(time() - $timestamp) > $tolerance) {
            throw new Exception('Stripe webhook timestamp outside tolerance.');
        }

        $expected = hash_hmac('sha256', $timestamp . '.' . $payload, $secret);
        $valid = collect($signatures)->contains(fn ($sig) => hash_equals($expected, $sig));

        if (!$valid) {
            throw new Exception('Stripe webhook signature mismatch.');
        }

        return json_decode($payload, true) ?? [];
    }

    protected function request(string $method, string $path, ?array $payload = null): array
    {
        $options = ['headers' => ['Accept' => 'application/json']];
        if ($payload !== null) {
            $options['form_params'] = $payload;
        }

        try {
            $response = $this->client->request($method, $path, $options);

            return json_decode((string) $response->getBody(), true) ?? [];
        } catch (RequestException $e) {
            $errorBody = $e->getResponse() ? (string) $e->getResponse()->getBody() : $e->getMessage();
            Log::error('Stripe API Error', [
                'method' => $method,
                'path' => $path,
                'error' => $errorBody,
            ]);
            throw new Exception('Stripe request failed: ' . $errorBody);
        }
    }
}
