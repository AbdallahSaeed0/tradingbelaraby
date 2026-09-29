<?php

namespace App\Services\Payment;

use App\Models\Order;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Exception;

class PayPalService
{
    protected ?Client $client = null;
    protected ?string $baseUrl;
    protected ?string $clientId;
    protected ?string $secret;
    protected ?string $currency;
    protected ?array $urls;
    protected ?string $webhookId;
    protected ?string $accessToken = null;
    protected ?int $tokenExpiresAt = null;

    public function __construct()
    {
        $this->baseUrl = config('paypal.base_url');
        $this->clientId = config('paypal.client_id');
        $this->secret = config('paypal.secret');
        $this->currency = config('paypal.currency', 'SAR');
        $this->urls = config('paypal.urls');
        $this->webhookId = config('paypal.webhook_id');

        // Only initialize client if credentials are present
        if ($this->baseUrl && $this->clientId && $this->secret) {
            $this->client = new Client([
                'base_uri' => $this->baseUrl,
                'timeout' => 30,
            ]);
        }
    }

    /**
     * Get OAuth access token
     *
     * @return string
     * @throws Exception
     */
    protected function getAccessToken(): string
    {
        if (!$this->client) {
            throw new Exception('PayPal service is not configured. Please check your PayPal credentials in .env file.');
        }

        // Return cached token if still valid
        if ($this->accessToken && $this->tokenExpiresAt && time() < $this->tokenExpiresAt) {
            return $this->accessToken;
        }

        try {
            $response = $this->client->post('/v1/oauth2/token', [
                'auth' => [$this->clientId, $this->secret],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            $this->accessToken = $data['access_token'];
            // Set expiration to 5 minutes before actual expiry for safety
            $this->tokenExpiresAt = time() + $data['expires_in'] - 300;

            return $this->accessToken;
        } catch (Exception $e) {
            Log::error('PayPal OAuth Error: ' . $e->getMessage());
            throw new Exception('Failed to authenticate with PayPal: ' . $e->getMessage());
        }
    }

    /**
     * Create a PayPal order
     *
     * @param Order $order
     * @param array $items
     * @param array $customer
     * @return array
     * @throws Exception
     */
    public function createOrder(Order $order, array $items, array $customer): array
    {
        $accessToken = $this->getAccessToken();

        // Calculate item total
        $itemTotal = array_reduce($items, function ($carry, $item) {
            return $carry + ($item['unit_price'] * $item['quantity']);
        }, 0);

        // Prepare order items for PayPal
        $paypalItems = array_map(function ($item) {
            return [
                'name' => $item['title'],
                'description' => $item['description'] ?? $item['title'],
                'sku' => (string) $item['id'],
                'unit_amount' => [
                    'currency_code' => $this->currency,
                    'value' => number_format($item['unit_price'], 2, '.', ''),
                ],
                'quantity' => (string) $item['quantity'],
                'category' => 'DIGITAL_GOODS',
            ];
        }, $items);

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => $order->order_number,
                    'description' => 'Order #' . $order->order_number,
                    'custom_id' => (string) $order->id,
                    'soft_descriptor' => 'COURSE_PURCHASE',
                    'amount' => [
                        'currency_code' => $this->currency,
                        'value' => number_format($order->total, 2, '.', ''),
                        'breakdown' => [
                            'item_total' => [
                                'currency_code' => $this->currency,
                                'value' => number_format($itemTotal, 2, '.', ''),
                            ],
                            'discount' => [
                                'currency_code' => $this->currency,
                                'value' => number_format($order->discount_amount, 2, '.', ''),
                            ],
                        ],
                    ],
                    'items' => $paypalItems,
                    'payee' => [
                        'email_address' => $customer['email'],
                    ],
                ],
            ],
            'application_context' => [
                'brand_name' => config('app.name'),
                'landing_page' => 'NO_PREFERENCE',
                'user_action' => 'PAY_NOW',
                'return_url' => $this->urls['success'],
                'cancel_url' => $this->urls['cancel'],
            ],
        ];

        try {
            $response = $this->client->post('/v2/checkout/orders', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            Log::info('PayPal Order Created', [
                'order_id' => $order->id,
                'paypal_order_id' => $responseData['id'] ?? null,
            ]);

            return $responseData;
        } catch (Exception $e) {
            Log::error('PayPal Create Order Error: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'payload' => $payload,
            ]);
            throw new Exception('Failed to create PayPal order: ' . $e->getMessage());
        }
    }

    /**
     * Capture payment for an approved order
     *
     * @param string $paypalOrderId
     * @return array
     * @throws Exception
     */
    public function captureOrder(string $paypalOrderId): array
    {
        $accessToken = $this->getAccessToken();

        try {
            $response = $this->client->post("/v2/checkout/orders/{$paypalOrderId}/capture", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            Log::info('PayPal Order Captured', [
                'paypal_order_id' => $paypalOrderId,
                'status' => $responseData['status'] ?? null,
            ]);

            return $responseData;
        } catch (Exception $e) {
            Log::error('PayPal Capture Order Error: ' . $e->getMessage(), [
                'paypal_order_id' => $paypalOrderId,
            ]);
            throw new Exception('Failed to capture PayPal order: ' . $e->getMessage());
        }
    }

    /**
     * Get order details
     *
     * @param string $paypalOrderId
     * @return array
     * @throws Exception
     */
    public function getOrder(string $paypalOrderId): array
    {
        $accessToken = $this->getAccessToken();

        try {
            $response = $this->client->get("/v2/checkout/orders/{$paypalOrderId}", [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Accept' => 'application/json',
                ],
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $e) {
            Log::error('PayPal Get Order Error: ' . $e->getMessage(), [
                'paypal_order_id' => $paypalOrderId,
            ]);
            throw new Exception('Failed to get PayPal order details: ' . $e->getMessage());
        }
    }

    /**
     * Verify webhook signature
     *
     * @param array $headers
     * @param string $body
     * @return bool
     * @throws Exception
     */
    public function verifyWebhookSignature(array $headers, string $body): bool
    {
        if (!$this->webhookId) {
            Log::warning('PayPal Webhook ID not configured');
            return false;
        }

        $accessToken = $this->getAccessToken();

        // Extract webhook headers
        $transmissionId = $headers['paypal-transmission-id'] ?? $headers['PAYPAL-TRANSMISSION-ID'] ?? null;
        $transmissionTime = $headers['paypal-transmission-time'] ?? $headers['PAYPAL-TRANSMISSION-TIME'] ?? null;
        $transmissionSig = $headers['paypal-transmission-sig'] ?? $headers['PAYPAL-TRANSMISSION-SIG'] ?? null;
        $certUrl = $headers['paypal-cert-url'] ?? $headers['PAYPAL-CERT-URL'] ?? null;
        $authAlgo = $headers['paypal-auth-algo'] ?? $headers['PAYPAL-AUTH-ALGO'] ?? null;

        if (!$transmissionId || !$transmissionTime || !$transmissionSig || !$certUrl || !$authAlgo) {
            Log::error('PayPal Webhook: Missing required headers', ['headers' => $headers]);
            return false;
        }

        $verificationPayload = [
            'transmission_id' => $transmissionId,
            'transmission_time' => $transmissionTime,
            'cert_url' => $certUrl,
            'auth_algo' => $authAlgo,
            'transmission_sig' => $transmissionSig,
            'webhook_id' => $this->webhookId,
            'webhook_event' => json_decode($body, true),
        ];

        try {
            $response = $this->client->post('/v1/notifications/verify-webhook-signature', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
                'json' => $verificationPayload,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);

            $isVerified = isset($result['verification_status']) &&
                         $result['verification_status'] === 'SUCCESS';

            if (!$isVerified) {
                Log::warning('PayPal Webhook Verification Failed', ['result' => $result]);
            }

            return $isVerified;
        } catch (Exception $e) {
            Log::error('PayPal Webhook Verification Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Extract order ID from webhook data
     *
     * @param array $webhookData
     * @return string|null
     */
    public function extractOrderIdFromWebhook(array $webhookData): ?string
    {
        // Try different paths where order ID might be located
        return $webhookData['resource']['supplementary_data']['related_ids']['order_id'] ??
               $webhookData['resource']['id'] ??
               null;
    }

    /**
     * Get payment status from captured order
     *
     * @param array $captureData
     * @return string
     */
    public function getPaymentStatus(array $captureData): string
    {
        $status = $captureData['status'] ?? 'UNKNOWN';

        return match($status) {
            'COMPLETED' => 'completed',
            'APPROVED' => 'completed',
            'VOIDED' => 'cancelled',
            'DECLINED' => 'failed',
            default => 'pending',
        };
    }
}

