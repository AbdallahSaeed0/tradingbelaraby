<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\Payment\TabbyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TabbyController extends Controller
{
    protected TabbyService $tabbyService;

    public function __construct(TabbyService $tabbyService)
    {
        $this->tabbyService = $tabbyService;
    }

    /**
     * Handle success callback from Tabby
     */
    public function success(Request $request)
    {
        $paymentId = $request->input('payment_id');

        if (!$paymentId) {
            return redirect()->route('checkout.index')->with('error', 'Invalid payment response from Tabby.');
        }

        try {
            $payment = $this->tabbyService->getPayment($paymentId);

            // Verify payment status
            if ($payment['status'] !== 'AUTHORIZED' && $payment['status'] !== 'CAPTURED') {
                return redirect()->route('checkout.index')->with('error', 'Payment was not authorized.');
            }

            // Find order by reference_id (which we stored as order_number)
            // or we could use metadata if we attached it, but we used order_number as reference_id
            $orderNumber = $payment['order']['reference_id'];
            $order = Order::where('order_number', $orderNumber)->firstOrFail();

            if ($order->status === 'completed') {
                return redirect()->route('checkout.success', $order->id);
            }

            DB::beginTransaction();

            // Update order status
            $order->update([
                'status' => 'completed',
                // You might want to store payment_id somewhere
                // 'payment_id' => $paymentId
            ]);

            // Enroll user if not already enrolled (or update status)
            // Assuming enrollments were created as 'pending' or not created yet.
            // Based on CheckoutController logic, let's assume we create them here if we delayed it,
            // OR update them if we created them as pending.
            
            // Re-fetching logic from CheckoutController for enrollment:
            $user = $order->user;
            
            // If enrollments were already created in CheckoutController (as pending maybe?), update them.
            // But CheckoutController currently creates them as 'active'.
            // We will modify CheckoutController to create them as 'pending' for Tabby.
            
            foreach ($order->orderItems as $item) {
                 $enrollment = $user->enrollments()->where('course_id', $item->course_id)->first();
                 if ($enrollment) {
                     $enrollment->update(['status' => 'active']);
                 } else {
                     // Fallback if not created
                     $user->enrollments()->create([
                        'course_id' => $item->course_id,
                        'status' => 'active',
                        'enrolled_at' => now(),
                        'progress_percentage' => 0,
                    ]);
                 }
            }

            DB::commit();

            return redirect()->route('checkout.success', $order->id)->with('success', 'Payment successful via Tabby!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Tabby Success Callback Error: ' . $e->getMessage());
            return redirect()->route('checkout.index')->with('error', 'An error occurred processing your payment.');
        }
    }

    /**
     * Handle cancel callback
     */
    public function cancel()
    {
        return redirect()->route('checkout.index')->with('error', 'Payment was cancelled.');
    }

    /**
     * Handle failure callback
     */
    public function failure()
    {
        return redirect()->route('checkout.index')->with('error', 'Payment failed.');
    }

    /**
     * Handle Tabby Webhook
     */
    public function webhook(Request $request)
    {
        // Verify webhook signature if Tabby provides one (omitted for brevity, but recommended)
        
        $data = $request->all();
        $paymentId = $data['id'] ?? null;
        $status = $data['status'] ?? null; // AUTHORIZED, CAPTURED, CLOSED, etc.
        $orderNumber = $data['order']['reference_id'] ?? null;

        if (!$paymentId || !$orderNumber) {
            return response()->json(['status' => 'ignored'], 200);
        }

        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return response()->json(['status' => 'order_not_found'], 404);
        }

        if ($status === 'AUTHORIZED' || $status === 'CAPTURED') {
             if ($order->status !== 'completed') {
                // Update order and enrollments similar to success method
                DB::transaction(function() use ($order) {
                    $order->update(['status' => 'completed']);
                    foreach ($order->orderItems as $item) {
                        $enrollment = $order->user->enrollments()->where('course_id', $item->course_id)->first();
                        if ($enrollment) {
                            $enrollment->update(['status' => 'active']);
                        }
                    }
                });
             }
        } elseif ($status === 'EXPIRED' || $status === 'REJECTED') {
             if ($order->status !== 'cancelled') {
                 $order->update(['status' => 'cancelled']);
                 // potentially cancel enrollments
             }
        }

        return response()->json(['status' => 'ok']);
    }
}

