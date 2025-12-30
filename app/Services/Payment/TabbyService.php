<?php

namespace App\Services\Payment;

use App\Models\Order;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class TabbyService
{
    protected ?Client $client = null;
    protected ?string $baseUrl;
    protected ?string $secretKey;
    protected ?string $merchantCode;
    protected ?array $urls;
    protected ?string $currency;

    public function __construct()
    {
        $this->baseUrl = config('tabby.base_url', 'https://api.tabby.ai');
        $this->secretKey = config('tabby.secret_key');
        $this->merchantCode = config('tabby.merchant_code');
        $this->urls = config('tabby.urls');
        $this->currency = config('tabby.currency', 'SAR');

        // Only initialize client if credentials are present
        if ($this->secretKey && $this->baseUrl) {
            $this->client = new Client([
                'base_uri' => $this->baseUrl,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->secretKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ],
            ]);
        }
    }

    /**
     * Create a Tabby Checkout Session
     *
     * @param Order $order
     * @param array $items
     * @param array $customer
     * @param array $shippingAddress
     * @return array
     * @throws \Exception
     */
    public function createCheckoutSession(Order $order, array $items, array $customer, array $shippingAddress): array
    {
        if (!$this->client) {
            throw new \Exception('Tabby payment service is not configured. Please check your Tabby credentials.');
        }

        $payload = [
            'payment' => [
                'amount' => (string) number_format($order->total, 2, '.', ''),
                'currency' => $this->currency,
                'description' => 'Order #' . $order->order_number,
                'buyer' => [
                    'name' => $customer['name'],
                    'phone' => $customer['phone'],
                    'email' => $customer['email'],
                ],
                'shipping_address' => [
                    'city' => $shippingAddress['city'],
                    'address' => $shippingAddress['address'],
                    'zip' => $shippingAddress['zip'],
                ],
                'order' => [
                    'reference_id' => (string) $order->order_number, // using order_number as reference
                    'items' => array_map(function ($item) {
                        return [
                            'reference_id' => (string) $item['id'],
                            'title' => $item['title'],
                            'quantity' => $item['quantity'],
                            'unit_price' => (string) number_format($item['unit_price'], 2, '.', ''),
                            'category' => 'Digital', // Assuming courses are digital
                        ];
                    }, $items),
                ],
            ],
            'lang' => app()->getLocale(),
            'merchant_code' => $this->merchantCode,
            'merchant_urls' => [
                'success' => $this->urls['success'],
                'cancel' => $this->urls['cancel'],
                'failure' => $this->urls['failure'],
            ],
        ];

        try {
            $response = $this->client->post('/api/v2/checkout', [
                'json' => $payload,
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('Tabby Create Session Error: ' . $e->getMessage(), ['payload' => $payload]);
            throw $e;
        }
    }

    /**
     * Retrieve a payment by ID
     *
     * @param string $paymentId
     * @return array
     */
    public function getPayment(string $paymentId): array
    {
        if (!$this->client) {
            throw new \Exception('Tabby payment service is not configured. Please check your Tabby credentials.');
        }

        try {
            $response = $this->client->get("/api/v2/payments/{$paymentId}");
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            Log::error('Tabby Get Payment Error: ' . $e->getMessage());
            throw $e;
        }
    }
}

