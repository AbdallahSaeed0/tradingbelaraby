<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Course;
use App\Models\CartItem;
use App\Notifications\CourseEnrollmentNotification;
use App\Services\Payment\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PayPalController extends Controller
{
    protected PayPalService $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->middleware('auth');
        $this->paypalService = $paypalService;
    }

    /**
     * Handle successful payment return from PayPal
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function success(Request $request)
    {
        $paypalOrderId = $request->query('token');
        $orderId = session('paypal_order_id');

        if (!$paypalOrderId || !$orderId) {
            Log::error('PayPal Success: Missing order information', [
                'paypal_order_id' => $paypalOrderId,
                'session_order_id' => $orderId,
            ]);
            return redirect()->route('checkout.index')
                ->with('error', 'Payment information not found. Please try again.');
        }

        try {
            // Find the order
            $order = Order::find($orderId);

            if (!$order) {
                Log::error('PayPal Success: Order not found', ['order_id' => $orderId]);
                return redirect()->route('checkout.index')
                    ->with('error', 'Order not found.');
            }

            // Verify order belongs to current user
            if ($order->user_id !== Auth::id()) {
                Log::error('PayPal Success: Order user mismatch', [
                    'order_id' => $orderId,
                    'order_user_id' => $order->user_id,
                    'current_user_id' => Auth::id(),
                ]);
                abort(403);
            }

            // Check if already completed
            if ($order->status === 'completed') {
                session()->forget('paypal_order_id');
                return redirect()->route('checkout.success', $order->id)
                    ->with('success', 'Payment already processed successfully!');
            }

            DB::beginTransaction();

            try {
                // Capture the payment
                $captureData = $this->paypalService->captureOrder($paypalOrderId);

                // Check capture status
                $captureStatus = $captureData['status'] ?? null;

                if ($captureStatus !== 'COMPLETED') {
                    Log::warning('PayPal Capture: Unexpected status', [
                        'order_id' => $orderId,
                        'status' => $captureStatus,
                        'capture_data' => $captureData,
                    ]);

                    DB::rollBack();
                    return redirect()->route('checkout.index')
                        ->with('error', 'Payment could not be completed. Status: ' . $captureStatus);
                }

                // Update order
                $order->update([
                    'status' => 'completed',
                    'payment_gateway_id' => $paypalOrderId,
                ]);

                // Activate all pending enrollments for this order
                $user = $order->user;
                $courseIds = $order->items->pluck('course_id')->filter()->toArray();
                
                if (!empty($courseIds)) {
                    $enrollments = $user->enrollments()
                        ->whereIn('course_id', $courseIds)
                        ->where('status', 'pending')
                        ->get();

                    foreach ($enrollments as $enrollment) {
                        $enrollment->update([
                            'status' => 'active',
                            'enrolled_at' => now(),
                        ]);

                        // Send enrollment notification
                        try {
                            $course = Course::find($enrollment->course_id);
                            if ($course) {
                                $language = Session::get('frontend_locale', config('app.locale'));
                                $language = in_array($language, ['ar', 'en']) ? $language : 'en';
                                $user->notify(new CourseEnrollmentNotification($course, $order, $language));
                            }
                        } catch (\Exception $e) {
                            Log::error('Failed to send enrollment notification', [
                                'user_id' => $user->id,
                                'course_id' => $enrollment->course_id,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }

                    Log::info('PayPal Payment Success: Enrollments activated', [
                        'order_id' => $order->id,
                        'enrollments_count' => $enrollments->count(),
                    ]);
                }

                DB::commit();

                // Clear session
                session()->forget('paypal_order_id');

                return redirect()->route('checkout.success', $order->id)
                    ->with('success', 'Payment completed successfully!');

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('PayPal Capture Error: ' . $e->getMessage(), [
                    'order_id' => $orderId,
                    'paypal_order_id' => $paypalOrderId,
                ]);

                return redirect()->route('checkout.index')
                    ->with('error', 'Failed to process payment: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            Log::error('PayPal Success Handler Error: ' . $e->getMessage());
            return redirect()->route('checkout.index')
                ->with('error', 'An error occurred while processing your payment.');
        }
    }

    /**
     * Handle payment cancellation
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancel(Request $request)
    {
        $orderId = session('paypal_order_id');

        if ($orderId) {
            try {
                $order = Order::find($orderId);

                if ($order && $order->user_id === Auth::id()) {
                    // Update order status
                    $order->update(['status' => 'cancelled']);

                    // Get order items to restore to cart
                    $orderItems = $order->items;
                    $user = Auth::user();

                    // Restore items to cart
                    foreach ($orderItems as $orderItem) {
                        if ($orderItem->course_id) {
                            // Check if not already in cart
                            $existingCartItem = CartItem::where('user_id', $user->id)
                                ->where('course_id', $orderItem->course_id)
                                ->first();

                            if (!$existingCartItem) {
                                CartItem::create([
                                    'user_id' => $user->id,
                                    'course_id' => $orderItem->course_id,
                                ]);
                            }
                        } elseif ($orderItem->bundle_id) {
                            // Check if not already in cart
                            $existingCartItem = CartItem::where('user_id', $user->id)
                                ->where('bundle_id', $orderItem->bundle_id)
                                ->first();

                            if (!$existingCartItem) {
                                CartItem::create([
                                    'user_id' => $user->id,
                                    'bundle_id' => $orderItem->bundle_id,
                                ]);
                            }
                        }
                    }

                    // Cancel pending enrollments
                    $user->enrollments()
                        ->whereIn('course_id', $orderItems->pluck('course_id'))
                        ->where('status', 'pending')
                        ->update(['status' => 'cancelled']);

                    Log::info('PayPal Payment Cancelled', [
                        'order_id' => $orderId,
                        'user_id' => $user->id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('PayPal Cancel Handler Error: ' . $e->getMessage(), [
                    'order_id' => $orderId,
                ]);
            }

            // Clear session
            session()->forget('paypal_order_id');
        }

        return redirect()->route('checkout.index')
            ->with('info', 'Payment was cancelled. Your items have been restored to your cart.');
    }

    /**
     * Handle payment failure
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function failure(Request $request)
    {
        $orderId = session('paypal_order_id');

        if ($orderId) {
            try {
                $order = Order::find($orderId);

                if ($order && $order->user_id === Auth::id()) {
                    // Update order status
                    $order->update(['status' => 'failed']);

                    // Cancel pending enrollments
                    $order->user->enrollments()
                        ->whereIn('course_id', $order->items->pluck('course_id'))
                        ->where('status', 'pending')
                        ->update(['status' => 'cancelled']);

                    Log::warning('PayPal Payment Failed', [
                        'order_id' => $orderId,
                        'user_id' => $order->user_id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('PayPal Failure Handler Error: ' . $e->getMessage(), [
                    'order_id' => $orderId,
                ]);
            }

            // Clear session
            session()->forget('paypal_order_id');
        }

        return redirect()->route('checkout.index')
            ->with('error', 'Payment failed. Please try again or use a different payment method.');
    }
}

