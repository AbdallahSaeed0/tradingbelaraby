<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $cartItems = $user->cartItems()->with('course.category', 'course.instructor')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->course->price;
        });

        $total = $subtotal; // No discount applied initially

        return view('checkout.index', compact('cartItems', 'subtotal', 'total', 'user'));
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
        $cartItems = $user->cartItems()->with('course')->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->course->price;
        });

        $total = $subtotal;

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

            // Create order items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $cartItem->course_id,
                    'price' => $cartItem->course->price,
                ]);

                // Enroll user in course (pending for Tabby)
                $enrollmentStatus = ($request->payment_method === 'free') ? 'active' : 'active'; // Default active for visa placeholder?
                // Actually, for Tabby we want to defer 'active' status or use 'pending' if system supports it.
                // Assuming system doesn't have 'pending' status logic fully built, we might just not create enrollment yet or create as active?
                // The prompt says "Our app stores Tabby IDs and statuses against the order."
                // "Order... can be a DB table... status..."
                // CourseEnrollment has status.
                // Let's set it to 'pending' if Tabby.

                if ($request->payment_method === 'tabby') {
                    // Check if 'pending' is a valid status enum for enrollment?
                    // CourseEnrollment.php doesn't define enum but uses strings.
                    // Assuming 'pending' is safe.
                    // But if the user can access course if record exists, we must be careful.
                    // CourseEnrollment::scopeActive uses 'active'.
                    // So 'pending' should be safe (user won't see course).
                    $enrollmentStatus = 'pending';
                }

                $user->enrollments()->create([
                    'course_id' => $cartItem->course_id,
                    'status' => $enrollmentStatus,
                    'enrolled_at' => now(),
                    'progress_percentage' => 0,
                ]);
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
