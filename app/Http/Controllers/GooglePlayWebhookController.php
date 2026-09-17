<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Firebase\JWT\JWK;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Handles Google Play Real-time Developer Notifications (RTDN), delivered via a
 * Cloud Pub/Sub push subscription. Currently only acts on voided (refunded/charged
 * back) one-time product purchases, to revoke course access that our own API
 * granted when the purchase originally completed.
 *
 * Setup (Google Cloud Console + Play Console, not code):
 * 1. Create a Pub/Sub topic and a push subscription pointing at this route's URL,
 *    with OIDC token authentication using this app's service account.
 * 2. Play Console -> Monetise with Play -> Monetisation setup -> Google Play Billing
 *    -> Real-time developer notifications -> enable, and set the topic name.
 */
class GooglePlayWebhookController extends Controller
{
    private const GOOGLE_CERTS_URL = 'https://www.googleapis.com/oauth2/v3/certs';

    public function handle(Request $request)
    {
        try {
            $this->verifyPushToken($request);
        } catch (\Throwable $e) {
            Log::warning('Google Play RTDN: token verification failed - ' . $e->getMessage());
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $encodedData = $request->input('message.data');
        if (! $encodedData) {
            Log::warning('Google Play RTDN: missing message.data');
            return response()->json(['status' => 'ignored'], 200);
        }

        $notification = json_decode(base64_decode($encodedData), true);
        if (! is_array($notification)) {
            Log::warning('Google Play RTDN: undecodable payload');
            return response()->json(['status' => 'ignored'], 200);
        }

        try {
            if (isset($notification['voidedPurchaseNotification'])) {
                $this->handleVoidedPurchase($notification['voidedPurchaseNotification']);
            } elseif (isset($notification['testNotification'])) {
                Log::info('Google Play RTDN: test notification received');
            } else {
                Log::info('Google Play RTDN: unhandled notification', ['notification' => $notification]);
            }
        } catch (\Throwable $e) {
            Log::error('Google Play RTDN processing error: ' . $e->getMessage(), ['notification' => $notification]);
            // Still acknowledge so Pub/Sub doesn't retry indefinitely on a permanent error.
        }

        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * A voided purchase (full/partial refund, chargeback, or Play-initiated cancellation)
     * means the course access we granted must be revoked.
     */
    protected function handleVoidedPurchase(array $data): void
    {
        $productType = (int) ($data['productType'] ?? 0);
        if ($productType !== 2) {
            // 1 = subscription, not handled here (this app only sells one-time products).
            return;
        }

        $purchaseToken = $data['purchaseToken'] ?? null;
        if (! $purchaseToken) {
            Log::warning('Google Play RTDN: voided purchase missing purchaseToken');
            return;
        }

        $order = Order::where('payment_method', 'google_play')
            ->where('payment_gateway_id', $purchaseToken)
            ->first();

        if (! $order) {
            Log::warning('Google Play RTDN: no order found for voided purchase token', ['purchase_token' => $purchaseToken]);
            return;
        }

        if ($order->status === 'cancelled') {
            Log::info('Google Play RTDN: order already cancelled', ['order_id' => $order->id]);
            return;
        }

        $order->update(['status' => 'cancelled']);

        $order->user->enrollments()
            ->whereIn('course_id', $order->items->pluck('course_id'))
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        Log::info('Google Play RTDN: order and enrollments cancelled after void', [
            'order_id' => $order->id,
            'purchase_token' => $purchaseToken,
        ]);
    }

    /**
     * Verifies the Pub/Sub push request's OIDC bearer token: signature (via Google's
     * published JWKs), issuer, and audience (must match this route's configured URL).
     */
    protected function verifyPushToken(Request $request): void
    {
        $authHeader = $request->header('Authorization', '');
        if (! str_starts_with($authHeader, 'Bearer ')) {
            throw new \RuntimeException('Missing bearer token.');
        }
        $token = substr($authHeader, 7);

        $keys = Cache::remember('google_oidc_certs', 3600, function () {
            $response = Http::timeout(10)->get(self::GOOGLE_CERTS_URL);
            if (! $response->successful()) {
                throw new \RuntimeException('Unable to fetch Google OIDC certs.');
            }
            return $response->json();
        });

        $payload = (array) JWT::decode($token, JWK::parseKeySet($keys));

        $audience = config('services.google_play.rtdn_audience');
        if (($payload['aud'] ?? null) !== $audience) {
            throw new \RuntimeException('Audience mismatch.');
        }

        if (! in_array($payload['iss'] ?? null, ['https://accounts.google.com', 'accounts.google.com'], true)) {
            throw new \RuntimeException('Unexpected issuer.');
        }
    }
}
