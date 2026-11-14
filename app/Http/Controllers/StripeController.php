<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payment\StripeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except('webhook');
    }

    /**
     * Create a Stripe Checkout Session and send the payer to Stripe's hosted page.
     */
    public function pay(Order $order, StripeService $stripe)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status === 'failed') {
            return redirect()->route('cart.index')
                ->with('error', 'This payment session has expired. Please try again.');
        }

        if (!$order->isAwaitingPayment()) {
            return redirect()->route('checkout.success', $order->id);
        }

        try {
            // {CHECKOUT_SESSION_ID} is substituted by Stripe on redirect.
            $session = $stripe->createCheckoutSession(
                $order,
                route('stripe.return', $order->id) . '?session_id={CHECKOUT_SESSION_ID}',
                route('stripe.cancel', $order->id),
            );
        } catch (\Exception $e) {
            Log::error('Stripe Create Session Error: ' . $e->getMessage(), ['order_id' => $order->id]);
            return redirect()->route('checkout.index')
                ->with('error', 'Unable to start the payment session. Please try again.');
        }

        return redirect()->away($session['url']);
    }

    /**
     * Stripe redirects here (success_url) after the payer finishes.
     * The session is re-fetched from Stripe before anything is marked paid.
     */
    public function return(Request $request, Order $order, StripeService $stripe)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$order->isAwaitingPayment()) {
            return redirect()->route('checkout.success', $order->id);
        }

        $sessionId = (string) $request->query('session_id', '');
        if ($sessionId === '') {
            return redirect()->route('checkout.index')
                ->with('error', 'Payment was not completed. Please try again.');
        }

        try {
            $session = $stripe->retrieveSession($sessionId);

            Log::info('Stripe Retrieve Session Response', [
                'order_id' => $order->id,
                'session_id' => $sessionId,
                'payment_status' => $session['payment_status'] ?? null,
                'amount' => $session['amount_total'] ?? null,
                'currency' => $session['currency'] ?? null,
            ]);

            if (!$stripe->isSessionPaid($session, $order)) {
                return redirect()->route('checkout.index')
                    ->with('error', 'Payment was not completed. Please try again.');
            }

            $this->completeOrder($order, $session);

            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Payment completed successfully!');
        } catch (\Exception $e) {
            Log::error('Stripe Return Error: ' . $e->getMessage(), ['order_id' => $order->id]);
            return redirect()->route('checkout.index')
                ->with('error', 'Unable to confirm payment. Please contact support if you were charged.');
        }
    }

    /**
     * The payer cancelled on the hosted page (cancel_url).
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return redirect()->route('checkout.index')
            ->with('error', 'Payment was cancelled.');
    }

    /**
     * Stripe webhook — confirms orders even when the payer never returns to the site.
     */
    public function webhook(Request $request, StripeService $stripe)
    {
        try {
            $event = $stripe->constructWebhookEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature', ''),
            );
        } catch (\Exception $e) {
            Log::warning('Stripe Webhook Rejected: ' . $e->getMessage());
            return response()->json(['error' => 'invalid signature'], 400);
        }

        $type = $event['type'] ?? null;
        if (!in_array($type, ['checkout.session.completed', 'checkout.session.async_payment_succeeded'], true)) {
            return response()->json(['received' => true]);
        }

        $session = $event['data']['object'] ?? [];
        $order = Order::find($session['client_reference_id'] ?? null);

        if (!$order) {
            Log::warning('Stripe Webhook: order not found', ['session_id' => $session['id'] ?? null]);
            return response()->json(['received' => true]);
        }

        if (!$order->isAwaitingPayment()) {
            return response()->json(['received' => true]);
        }

        if (!$stripe->isSessionPaid($session, $order)) {
            Log::info('Stripe Webhook: session not paid yet', [
                'order_id' => $order->id,
                'payment_status' => $session['payment_status'] ?? null,
            ]);
            return response()->json(['received' => true]);
        }

        try {
            $this->completeOrder($order, $session);
        } catch (\Exception $e) {
            Log::error('Stripe Webhook Error: ' . $e->getMessage(), ['order_id' => $order->id]);
            // Non-2xx makes Stripe retry later.
            return response()->json(['error' => 'processing failed'], 500);
        }

        return response()->json(['received' => true]);
    }

    /**
     * Mark the order paid and activate its enrollments. Safe to call from both
     * the return redirect and the webhook: the row lock makes it run once.
     */
    protected function completeOrder(Order $order, array $session): void
    {
        DB::transaction(function () use ($order, $session) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();

            if (!$order->isAwaitingPayment()) {
                return;
            }

            $order->update([
                'status' => 'completed',
                'payment_gateway_id' => $session['payment_intent'] ?? $session['id'],
            ]);

            $order->user->enrollments()
                ->whereIn('course_id', $order->getCourseIds())
                ->where('status', 'pending')
                ->update([
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]);

            $order->applyEnrollmentAccess();

            $order->user->cartItems()->delete();
        });
    }
}
