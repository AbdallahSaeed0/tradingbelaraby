<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Payment\NbeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NbeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Create an NBE Hosted Checkout session and show the payment page.
     */
    public function pay(Order $order, NbeService $nbe)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('checkout.success', $order->id);
        }

        try {
            $checkout = $nbe->initiateCheckout(
                $order,
                route('nbe.return', $order->id),
                route('nbe.cancel', $order->id),
            );
        } catch (\Exception $e) {
            Log::error('NBE Initiate Checkout Error: ' . $e->getMessage(), ['order_id' => $order->id]);
            return redirect()->route('checkout.index')
                ->with('error', 'Unable to start the payment session. Please try again.');
        }

        session(['nbe_success_indicator.' . $order->id => $checkout['success_indicator']]);

        return view('checkout.nbe-payment', [
            'order' => $order,
            'sessionId' => $checkout['session_id'],
            'scriptUrl' => $nbe->checkoutScriptUrl(),
            'chargeAmount' => $nbe->chargeAmountForOrder($order),
            'chargeCurrency' => config('nbe.currency', 'EGP'),
        ]);
    }

    /**
     * The gateway redirects here (returnUrl) after the payer finishes.
     * resultIndicator is only a hint; the order is confirmed against the
     * gateway's own record before anything is marked paid.
     */
    public function return(Request $request, Order $order, NbeService $nbe)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return redirect()->route('checkout.success', $order->id);
        }

        $expectedIndicator = session()->pull('nbe_success_indicator.' . $order->id);
        $resultIndicator = (string) $request->query('resultIndicator', '');

        if ($expectedIndicator && !hash_equals($expectedIndicator, $resultIndicator)) {
            Log::warning('NBE resultIndicator mismatch', ['order_id' => $order->id]);
        }

        try {
            $gatewayOrder = $nbe->retrieveOrder($order->order_number);

            Log::info('NBE Retrieve Order Response', [
                'order_id' => $order->id,
                'result' => $gatewayOrder['result'] ?? null,
                'status' => $gatewayOrder['status'] ?? null,
                'amount' => $gatewayOrder['totalCapturedAmount'] ?? null,
                'currency' => $gatewayOrder['currency'] ?? null,
            ]);

            if (!$nbe->isOrderPaid($gatewayOrder, $order)) {
                return redirect()->route('checkout.index')
                    ->with('error', 'Payment was not completed. Please try again.');
            }

            $transactionId = collect($gatewayOrder['transaction'] ?? [])
                ->pluck('transaction.id')
                ->filter()
                ->last();

            DB::beginTransaction();

            $order->update([
                'status' => 'completed',
                'payment_gateway_id' => $transactionId ?: $order->order_number,
            ]);

            $order->user->enrollments()
                ->whereIn('course_id', $order->getCourseIds())
                ->where('status', 'pending')
                ->update([
                    'status' => 'active',
                    'enrolled_at' => now(),
                ]);

            $order->user->cartItems()->delete();

            DB::commit();

            return redirect()->route('checkout.success', $order->id)
                ->with('success', 'Payment completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('NBE Return Error: ' . $e->getMessage(), ['order_id' => $order->id]);
            return redirect()->route('checkout.index')
                ->with('error', 'Unable to confirm payment. Please contact support if you were charged.');
        }
    }

    /**
     * The payer cancelled on the hosted page (cancelUrl).
     */
    public function cancel(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return redirect()->route('checkout.index')
            ->with('error', 'Payment was cancelled.');
    }
}
