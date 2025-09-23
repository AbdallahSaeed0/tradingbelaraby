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
    public function process(Request $request)
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
            'payment_method' => 'required|in:visa,free',
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

        if ($request->payment_method === 'visa' && $allFree) {
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

                // Enroll user in course
                $user->enrollments()->create([
                    'course_id' => $cartItem->course_id,
                    'status' => 'active',
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
            } else {
                // For Visa payment, redirect to payment gateway (placeholder)
                return redirect()->route('checkout.payment', $order->id);
            }

        } catch (\Exception $e) {
            DB::rollBack();
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
