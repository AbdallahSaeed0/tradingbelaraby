<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Bundle;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\PaymentSettings;
use App\Notifications\CourseEnrollmentNotification;
use App\Services\Payment\TabbyService;
use App\Services\Payment\PayPalService;
use App\Support\CheckoutPricing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

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
        $cartItems = $user->cartItems()->with('course.instructor', 'course.instructors', 'bundle.courses')->get();

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
        $paymentSettings = PaymentSettings::getSettings();

        return view('checkout.index', compact('cartItems', 'subtotal', 'total', 'discount', 'coupon', 'user', 'paymentSettings'));
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
        CouponUsage::pruneOrphanedUsages();
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
    public function process(Request $request, TabbyService $tabbyService, PayPalService $paypalService)
    {
        $request->validate([
            'first_name'            => 'required|string|max:255',
            'last_name'             => 'required|string|max:255',
            'email'                 => 'required|email',
            'phone'                 => 'required|string|max:20',
            'address'               => 'nullable|string',
            'city'                  => 'nullable|string|max:255',
            'state'                 => 'nullable|string|max:255',
            'postal_code'           => 'nullable|string|max:20',
            'country'               => 'nullable|string|max:255',
            'payment_method'        => 'required|in:visa,free,paypal,bank_transfer',
            'transaction_reference' => 'nullable|string|max:255',
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

        if (in_array($request->payment_method, ['visa', 'paypal']) && $allFree) {
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
                'payment_gateway_id' => $request->payment_method === 'bank_transfer' ? $request->transaction_reference : null,
                'billing_first_name' => $request->first_name,
                'billing_last_name' => $request->last_name,
                'billing_email' => $request->email,
                'billing_phone' => $request->phone,
                'billing_address' => $request->address ?? '',
                'billing_city' => $request->city ?? '',
                'billing_state' => $request->state ?? '',
                'billing_postal_code' => $request->postal_code ?? '',
                'billing_country' => $request->country ?? '',
            ]);

            $lineItems = $cartItems->map(function ($cartItem) {
                return [
                    'key' => $cartItem->isBundle()
                        ? 'bundle_' . $cartItem->bundle_id
                        : 'course_' . $cartItem->course_id,
                    'price' => $cartItem->getPrice(),
                ];
            })->all();

            $paidByLine = CheckoutPricing::allocateLineTotals($lineItems, $subtotal, $discount);

            // Create order items and enrollments
            foreach ($cartItems as $cartItem) {
                if ($cartItem->isBundle()) {
                    $bundlePaid = $paidByLine['bundle_' . $cartItem->bundle_id] ?? $cartItem->getPrice();
                    $coursePaidAmounts = CheckoutPricing::splitBundleAmountAmongCourses(
                        $cartItem->bundle->courses,
                        $bundlePaid
                    );

                    // Handle bundle
                    OrderItem::create([
                        'order_id' => $order->id,
                        'bundle_id' => $cartItem->bundle_id,
                        'price' => $bundlePaid,
                    ]);

                    // Enroll user in all courses in the bundle
                    $enrollmentStatus = 'active';
                    if (in_array($request->payment_method, ['paypal', 'bank_transfer'])) {
                        $enrollmentStatus = 'pending';
                    }

                    foreach ($cartItem->bundle->courses as $course) {
                        if ($user->enrollments()->where('course_id', $course->id)->blockingPurchase()->exists()) {
                            continue;
                        }

                        $user->enrollments()->updateOrCreate(
                            ['course_id' => $course->id],
                            [
                                'status' => $enrollmentStatus,
                                'enrolled_at' => $enrollmentStatus === 'active' ? now() : null,
                                'progress_percentage' => 0,
                                'payment_method' => $request->payment_method,
                                'transaction_id' => $request->payment_method === 'bank_transfer' && $request->transaction_reference
                                    ? $request->transaction_reference
                                    : $order->order_number,
                                'notes' => $request->payment_method === 'bank_transfer'
                                    ? 'Bank transfer reference: ' . ($request->transaction_reference ?? 'Not provided')
                                    : null,
                                'amount_paid' => $coursePaidAmounts[$course->id] ?? 0,
                            ]
                        );

                        try {
                            $language = Session::get('frontend_locale', config('app.locale'));
                            $language = in_array($language, ['ar', 'en']) ? $language : 'en';
                            $user->notify(new CourseEnrollmentNotification($course, $order, $language));
                        } catch (\Exception $e) {
                            Log::error('Failed to send enrollment notification', [
                                'user_id' => $user->id,
                                'course_id' => $course->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                } else {
                    $coursePaid = $paidByLine['course_' . $cartItem->course_id] ?? $cartItem->getPrice();

                    // Handle individual course
                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $cartItem->course_id,
                    'price' => $coursePaid,
                ]);

                    $enrollmentStatus = 'active';
                    if (in_array($request->payment_method, ['paypal', 'bank_transfer'])) {
                        $enrollmentStatus = 'pending';
                    }

                $enrollment = $user->enrollments()->updateOrCreate(
                    ['course_id' => $cartItem->course_id],
                    [
                        'status'              => $enrollmentStatus,
                        'enrolled_at'         => $enrollmentStatus === 'active' ? now() : null,
                        'progress_percentage' => 0,
                        'payment_method'      => $request->payment_method,
                        'transaction_id'      => $request->payment_method === 'bank_transfer' && $request->transaction_reference
                            ? $request->transaction_reference
                            : $order->order_number,
                        'notes'               => $request->payment_method === 'bank_transfer'
                            ? 'Bank transfer reference: ' . ($request->transaction_reference ?? 'Not provided')
                            : null,
                        'amount_paid'         => $coursePaid,
                    ]
                );

                // Send enrollment notification email
                try {
                    $course = Course::find($cartItem->course_id);
                    if ($course) {
                        $language = Session::get('frontend_locale', config('app.locale'));
                        $language = in_array($language, ['ar', 'en']) ? $language : 'en';
                        $user->notify(new CourseEnrollmentNotification($course, $order, $language));
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

            DB::commit();

            if ($request->payment_method === 'free') {
                // Clear cart for free enrollments
                $user->cartItems()->delete();

                return redirect()->route('checkout.success', $order->id)
                    ->with('success', 'Courses enrolled successfully!');
            } elseif ($request->payment_method === 'bank_transfer') {
                $user->cartItems()->delete();

                return redirect()->route('checkout.bank-transfer-pending', $order->id);
            }
            // Tabby payment commented out - not configured
            /* elseif ($request->payment_method === 'tabby') {
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

            } */
            elseif ($request->payment_method === 'paypal') {
                // Prepare data for PayPal
                $items = $cartItems->map(function ($item) {
                    if ($item->isBundle()) {
                        return [
                            'id' => $item->bundle_id,
                            'title' => $item->bundle->title,
                            'description' => 'Bundle: ' . $item->bundle->title,
                            'quantity' => 1,
                            'unit_price' => $item->bundle->price,
                        ];
                    } else {
                        return [
                            'id' => $item->course_id,
                            'title' => $item->course->name,
                            'description' => 'Course: ' . $item->course->name,
                            'quantity' => 1,
                            'unit_price' => $item->course->price,
                        ];
                    }
                })->toArray();

                $customer = [
                    'name' => $request->first_name . ' ' . $request->last_name,
                    'phone' => $request->phone,
                    'email' => $request->email,
                ];

                try {
                    $paypalOrder = $paypalService->createOrder($order, $items, $customer);

                    // Extract approval URL
                    $approvalUrl = null;
                    foreach ($paypalOrder['links'] ?? [] as $link) {
                        if ($link['rel'] === 'approve') {
                            $approvalUrl = $link['href'];
                            break;
                        }
                    }

                    if (!$approvalUrl) {
                        throw new \Exception('PayPal approval URL not found in response.');
                    }

                    // Store order ID in session for later verification
                    session(['paypal_order_id' => $order->id]);

                    // Store PayPal order ID in database
                    $order->update(['payment_gateway_id' => $paypalOrder['id']]);

                    if ($request->query('utm_source') === 'app' || $request->query('return_app') === '1') {
                        $request->session()->put('return_to_app', true);
                    }

                    return redirect()->away($approvalUrl);

                } catch (\Exception $e) {
                    DB::rollBack();
                    // Log error and redirect back
                    Log::error('PayPal Checkout Error: ' . $e->getMessage(), [
                        'order_id' => $order->id ?? null,
                        'user_id' => $user->id,
                        'trace' => $e->getTraceAsString()
                    ]);
                    return redirect()->route('cart.index')->with('error', 'Unable to initiate PayPal payment: ' . $e->getMessage());
                }

            } else {
                // For Visa payment, redirect to payment gateway (placeholder)
                // Note: Cart will be cleared after successful payment confirmation
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

    /**
     * Display bank transfer pending confirmation page
     */
    public function bankTransferPending(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.bank-transfer-pending', compact('order'));
    }
}
