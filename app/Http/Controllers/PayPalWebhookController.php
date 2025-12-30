<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Course;
use App\Notifications\CourseEnrollmentNotification;
use App\Services\Payment\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class PayPalWebhookController extends Controller
{
    protected PayPalService $paypalService;

    public function __construct(PayPalService $paypalService)
    {
        $this->paypalService = $paypalService;
    }

    /**
     * Handle PayPal webhook events
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleWebhook(Request $request)
    {
        $webhookBody = $request->getContent();
        $webhookHeaders = $request->headers->all();

        // Log incoming webhook
        Log::info('PayPal Webhook Received', [
            'event_type' => $request->input('event_type'),
            'resource_type' => $request->input('resource_type'),
        ]);

        // Verify webhook signature
        try {
            $isValid = $this->paypalService->verifyWebhookSignature($webhookHeaders, $webhookBody);

            if (!$isValid) {
                Log::error('PayPal Webhook: Invalid signature');
                return response()->json(['error' => 'Invalid signature'], 401);
            }
        } catch (\Exception $e) {
            Log::error('PayPal Webhook Verification Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Verification failed'], 500);
        }

        // Parse webhook data
        $webhookData = $request->all();
        $eventType = $webhookData['event_type'] ?? null;

        try {
            switch ($eventType) {
                case 'CHECKOUT.ORDER.APPROVED':
                    $this->handleOrderApproved($webhookData);
                    break;

                case 'PAYMENT.CAPTURE.COMPLETED':
                    $this->handlePaymentCaptureCompleted($webhookData);
                    break;

                case 'PAYMENT.CAPTURE.DENIED':
                case 'PAYMENT.CAPTURE.DECLINED':
                    $this->handlePaymentCaptureDenied($webhookData);
                    break;

                case 'PAYMENT.CAPTURE.REFUNDED':
                    $this->handlePaymentRefunded($webhookData);
                    break;

                default:
                    Log::info('PayPal Webhook: Unhandled event type', ['event_type' => $eventType]);
            }

            return response()->json(['status' => 'success'], 200);
        } catch (\Exception $e) {
            Log::error('PayPal Webhook Processing Error: ' . $e->getMessage(), [
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);

            // Still return 200 to prevent PayPal from retrying
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 200);
        }
    }

    /**
     * Handle order approved event
     *
     * @param array $webhookData
     * @return void
     */
    protected function handleOrderApproved(array $webhookData): void
    {
        $paypalOrderId = $webhookData['resource']['id'] ?? null;

        if (!$paypalOrderId) {
            Log::error('PayPal Webhook: Missing order ID in CHECKOUT.ORDER.APPROVED');
            return;
        }

        Log::info('PayPal Order Approved', ['paypal_order_id' => $paypalOrderId]);

        // Find order by PayPal order ID
        $order = Order::where('payment_gateway_id', $paypalOrderId)->first();

        if ($order) {
            $order->update(['status' => 'processing']);
            Log::info('Order status updated to processing', ['order_id' => $order->id]);
        }
    }

    /**
     * Handle payment capture completed event
     *
     * @param array $webhookData
     * @return void
     */
    protected function handlePaymentCaptureCompleted(array $webhookData): void
    {
        // Extract order reference from webhook
        $customId = $webhookData['resource']['custom_id'] ?? null;
        $paypalOrderId = $this->paypalService->extractOrderIdFromWebhook($webhookData);

        if (!$customId && !$paypalOrderId) {
            Log::error('PayPal Webhook: Missing order reference in PAYMENT.CAPTURE.COMPLETED');
            return;
        }

        // Find order
        $order = null;
        if ($customId) {
            $order = Order::find($customId);
        }
        if (!$order && $paypalOrderId) {
            $order = Order::where('payment_gateway_id', $paypalOrderId)->first();
        }

        if (!$order) {
            Log::error('PayPal Webhook: Order not found', [
                'custom_id' => $customId,
                'paypal_order_id' => $paypalOrderId,
            ]);
            return;
        }

        // Check if already completed
        if ($order->status === 'completed') {
            Log::info('PayPal Webhook: Order already completed', ['order_id' => $order->id]);
            return;
        }

        Log::info('Processing PayPal Payment Capture', ['order_id' => $order->id]);

        // Update order status
        $order->update([
            'status' => 'completed',
            'payment_gateway_id' => $paypalOrderId,
        ]);

        // Activate enrollments
        $enrollments = $order->user->enrollments()
            ->whereIn('course_id', $order->items->pluck('course_id'))
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
                    $order->user->notify(new CourseEnrollmentNotification($course, $order, $language));
                }
            } catch (\Exception $e) {
                Log::error('Failed to send enrollment notification via webhook', [
                    'user_id' => $order->user_id,
                    'course_id' => $enrollment->course_id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('PayPal Payment Completed Successfully', [
            'order_id' => $order->id,
            'enrollments_activated' => $enrollments->count(),
        ]);
    }

    /**
     * Handle payment capture denied event
     *
     * @param array $webhookData
     * @return void
     */
    protected function handlePaymentCaptureDenied(array $webhookData): void
    {
        $customId = $webhookData['resource']['custom_id'] ?? null;
        $paypalOrderId = $this->paypalService->extractOrderIdFromWebhook($webhookData);

        if (!$customId && !$paypalOrderId) {
            Log::error('PayPal Webhook: Missing order reference in PAYMENT.CAPTURE.DENIED');
            return;
        }

        // Find order
        $order = null;
        if ($customId) {
            $order = Order::find($customId);
        }
        if (!$order && $paypalOrderId) {
            $order = Order::where('payment_gateway_id', $paypalOrderId)->first();
        }

        if (!$order) {
            Log::error('PayPal Webhook: Order not found for denied payment', [
                'custom_id' => $customId,
                'paypal_order_id' => $paypalOrderId,
            ]);
            return;
        }

        Log::warning('PayPal Payment Denied', ['order_id' => $order->id]);

        // Update order status
        $order->update(['status' => 'failed']);

        // Cancel enrollments
        $order->user->enrollments()
            ->whereIn('course_id', $order->items->pluck('course_id'))
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);
    }

    /**
     * Handle payment refunded event
     *
     * @param array $webhookData
     * @return void
     */
    protected function handlePaymentRefunded(array $webhookData): void
    {
        $customId = $webhookData['resource']['custom_id'] ?? null;

        if (!$customId) {
            Log::error('PayPal Webhook: Missing order reference in PAYMENT.CAPTURE.REFUNDED');
            return;
        }

        $order = Order::find($customId);

        if (!$order) {
            Log::error('PayPal Webhook: Order not found for refund', ['custom_id' => $customId]);
            return;
        }

        Log::warning('PayPal Payment Refunded', ['order_id' => $order->id]);

        // Update order status
        $order->update(['status' => 'refunded']);

        // Optionally, you might want to deactivate enrollments or handle refunds differently
        // For now, we'll just log it
        Log::info('PayPal Refund Processed', ['order_id' => $order->id]);
    }
}

