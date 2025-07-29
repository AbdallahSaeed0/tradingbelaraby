<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display the user's cart
     */
    public function index()
    {
        $user = Auth::user();
        $cartItems = $user->cartItems()->with('course.category', 'course.instructor')->get();

        $total = $cartItems->sum(function($item) {
            return $item->course->price;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Add a course to cart
     */
    public function add(Request $request, Course $course)
    {
        $user = Auth::user();

        // Check if already in cart
        if ($user->hasInCart($course)) {
            return response()->json([
                'success' => false,
                'message' => 'Course is already in your cart'
            ]);
        }

        // Check if already enrolled
        if ($user->enrollments()->where('course_id', $course->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'You are already enrolled in this course'
            ]);
        }

        // Add to cart
        CartItem::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Course added to cart successfully'
        ]);
    }

    /**
     * Remove a course from cart
     */
    public function remove(Request $request, Course $course)
    {
        $user = Auth::user();

        // Remove from cart
        $user->cartItems()->where('course_id', $course->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Course removed from cart successfully'
        ]);
    }

    /**
     * Clear the entire cart
     */
    public function clear()
    {
        $user = Auth::user();
        $user->cartItems()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }

    /**
     * Apply coupon to cart
     */
    public function applyCoupon(Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|string|max:50'
        ]);

        $user = Auth::user();
        $cartItems = $user->cartItems()->with('course')->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty'
            ]);
        }

        $subtotal = $cartItems->sum(function($item) {
            return $item->course->price;
        });

        // For now, let's implement a simple coupon system
        // You can extend this with a proper Coupon model later
        $couponCode = strtoupper($request->coupon_code);

        // Example coupon codes
        $validCoupons = [
            'WELCOME10' => 10, // 10% discount
            'SAVE20' => 20,    // 20% discount
            'FREECOURSE' => 100 // Free course (up to ₹100)
        ];

        if (!array_key_exists($couponCode, $validCoupons)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid coupon code'
            ]);
        }

        $discountPercent = $validCoupons[$couponCode];
        $discount = ($subtotal * $discountPercent) / 100;
        $total = $subtotal - $discount;

        return response()->json([
            'success' => true,
            'message' => "Coupon applied! You saved ₹" . number_format($discount, 2),
            'discount' => $discount,
            'total' => $total
        ]);
    }
}
