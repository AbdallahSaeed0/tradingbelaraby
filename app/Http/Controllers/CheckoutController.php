<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Bundle;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Notifications\CourseEnrollmentNotification;
use App\Services\Payment\TabbyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the checkout page
     */
    public function index()
    {
        $user = Auth::user();
        $cartItems = $user->cartItems()->with('course.category', 'course.instructor', 'bundle.courses')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->getPrice();
        });

        $discount = 0;
        $coupon = null;
        
        // Check if coupon is stored in session
        if (session()->has('applied_coupon')) {
            $couponCode = session('applied_coupon');
            $coupon = Coupon::where('code', $couponCode)->first();
            
            if ($coupon && $coupon->isValidForUser($user) && $coupon->appliesToCart($cartItems)) {
                $discount = $coupon->calculateDiscountForCart($cartItems);
            } else {
                // Remove invalid coupon from session
                session()->forget('applied_coupon');
                $coupon = null;
            }
        }

        $total = $subtotal - $discount;

        return view('checkout.index', compact('cartItems', 'subtotal', 'total', 'discount', 'coupon', 'user'));
    }

    /**
     * Apply coupon to checkout
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50'
        ]);

        $user = Auth::user();
        $cartItems = $user->cartItems()->with('course', 'bundle')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty'
            ]);
        }

        $couponCode = strtoupper($request->coupon_code);
        $coupon = Coupon::where('code', $couponCode)->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ]);
        }

        if (!$coupon->isValidForUser($user)) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon is not valid for you or has reached its usage limit'
            ]);
        }

        if (!$coupon->appliesToCart($cartItems)) {
            return response()->json([
                'success' => false,
                'message' => 'This coupon does not apply to items in your cart'
            ]);
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->getPrice();
        });

        $discount = $coupon->calculateDiscountForCart($cartItems);
        $total = $subtotal - $discount;

        // Store coupon in session
        session(['applied_coupon' => $couponCode]);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully!',
            'discount' => number_format($discount, 2),
            'total' => number_format($total, 2)
        ]);
    }

    /**
     * Remove coupon from checkout
     */
    public function removeCoupon()
    {
        session()->forget('applied_coupon');

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully'
        ]);
    }

    /**
     * Process the checkout
     */
    public function process(Request $request, TabbyService $tabbyService)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'payment_method' => 'required|in:visa,free,tabby',
        ]);

        $user = Auth::user();
        $cartItems = $user->cartItems()->with('course', 'bundle.courses')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->getPrice();
        });

        // Apply coupon if exists
        $discount = 0;
        $coupon = null;
        if (session()->has('applied_coupon')) {
            $couponCode = session('applied_coupon');
            $coupon = Coupon::where('code', $couponCode)->first();
            
            if ($coupon && $coupon->isValidForUser($user) && $coupon->appliesToCart($cartItems)) {
                $discount = $coupon->calculateDiscountForCart($cartItems);
            }
        }

        $total = $subtotal - $discount;

        // Check if all courses are free
        $allFree = $cartItems->every(function($item) {
            return $item->course->price == 0;
        });

        // Validate payment method
        if ($request->payment_method === 'free' && !$allFree) {
            return back()->withErrors(['payment_method' => 'Free enrollment is only available for free courses']);
        }

        if (($request->payment_method === 'visa' || $request->payment_method === 'tabby') && $allFree) {
            return back()->withErrors(['payment_method' => 'Free courses do not require payment']);
        }

        try {
            DB::beginTransaction();

            // Create order
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'subtotal' => $subtotal,
                'total' => $total,
                'coupon_id' => $coupon ? $coupon->id : null,
                'discount_amount' => $discount,
                'payment_method' => $request->payment_method,
                'status' => $request->payment_method === 'free' ? 'completed' : 'pending',
                'billing_first_name' => $request->first_name,
                'billing_last_name' => $request->last_name,
                'billing_email' => $request->email,
                'billing_phone' => $request->phone,
                'billing_address' => $request->address,
                'billing_city' => $request->city,
                'billing_state' => $request->state,
                'billing_postal_code' => $request->postal_code,
                'billing_country' => $request->country,
            ]);

            // Create order items and enrollments
            foreach ($cartItems as $cartItem) {
                if ($cartItem->isBundle()) {
                    // Handle bundle
                    OrderItem::create([
                        'order_id' => $order->id,
                        'bundle_id' => $cartItem->bundle_id,
                        'price' => $cartItem->bundle->price,
                    ]);

                    // Enroll user in all courses in the bundle
                    $enrollmentStatus = ($request->payment_method === 'free') ? 'active' : 'active';
                    if ($request->payment_method === 'tabby') {
                        $enrollmentStatus = 'pending';
                    }

                    foreach ($cartItem->bundle->courses as $course) {
                        // Check if not already enrolled
                        if (!$user->enrollments()->where('course_id', $course->id)->exists()) {
                            $user->enrollments()->create([
                                'course_id' => $course->id,
                                'status' => $enrollmentStatus,
                                'enrolled_at' => now(),
                                'progress_percentage' => 0,
                            ]);
                            
                            // Send enrollment notification email
                            try {
                                $user->notify(new CourseEnrollmentNotification($course));
                            } catch (\Exception $e) {
                                Log::error('Failed to send enrollment notification', [
                                    'user_id' => $user->id,
                                    'course_id' => $course->id,
                                    'error' => $e->getMessage()
                                ]);
                            }
                        }
                    }
                } else {
                    // Handle individual course
                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $cartItem->course_id,
                    'price' => $cartItem->course->price,
                ]);

                    $enrollmentStatus = ($request->payment_method === 'free') ? 'active' : 'active';
                if ($request->payment_method === 'tabby') {
                    $enrollmentStatus = 'pending';
                }

                $enrollment = $user->enrollments()->create([
                    'course_id' => $cartItem->course_id,
                    'status' => $enrollmentStatus,
                    'enrolled_at' => now(),
                    'progress_percentage' => 0,
                ]);
                
                // Send enrollment notification email
                try {
                    $course = Course::find($cartItem->course_id);
                    if ($course) {
                        $user->notify(new CourseEnrollmentNotification($course));
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send enrollment notification', [
                        'user_id' => $user->id,
                        'course_id' => $cartItem->course_id,
                        'error' => $e->getMessage()
                    ]);
                }
                }
            }

            // Track coupon usage
            if ($coupon) {
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id' => $user->id,
                    'order_id' => $order->id,
                    'used_at' => now(),
                ]);
                
                $coupon->incrementUsage();
                
                // Clear coupon from session
                session()->forget('applied_coupon');
            }

            // Clear cart
            $user->cartItems()->delete();

            DB::commit();

            if ($request->payment_method === 'free') {
                return redirect()->route('checkout.success', $order->id)
                    ->with('success', 'Courses enrolled successfully!');
            } elseif ($request->payment_method === 'tabby') {
                // Prepare data for Tabby
                $items = $cartItems->map(function ($item) {
                     return [
                         'id' => $item->course_id,
                         'title' => $item->course->name,
                         'quantity' => 1,
                         'unit_price' => $item->course->price,
                     ];
                })->toArray();

                $customer = [
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                ];

                $shippingAddress = [
                    'city' => $request->city,
                    'address' => $request->address,
                    'zip' => $request->postal_code,
                ];

                try {
                    $session = $tabbyService->createCheckoutSession($order, $items, $customer, $shippingAddress);

                    // Find web_url
                    $webUrl = $session['configuration']['available_products']['installments'][0]['web_url'] ?? null;
                    if (!$webUrl) {
                        // Try alternative path
                        $webUrl = $session['configuration']['available_products']['pay_later'][0]['web_url'] ?? null;
                    }

                    if ($webUrl) {
                         return redirect()->away($webUrl);
                    }

                    throw new \Exception('Tabby payment URL not found in response.');

                } catch (\Exception $e) {
                    // Log error and redirect back
                    Log::error('Tabby Checkout Error: ' . $e->getMessage());
                    return redirect()->route('checkout.index')->with('error', 'Unable to initiate Tabby payment: ' . $e->getMessage());
                }

            } else {
                // For Visa payment, redirect to payment gateway (placeholder)
                return redirect()->route('checkout.payment', $order->id);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout Process Error: ' . $e->getMessage());
            return back()->with('error', 'An error occurred during checkout. Please try again.');
        }
    }

    /**
     * Display success page
     */
    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }

    /**
     * Display payment page (placeholder for Visa payment)
     */
    public function payment(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.payment', compact('order'));
    }
}
