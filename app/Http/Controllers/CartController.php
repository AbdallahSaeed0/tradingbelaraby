<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Bundle;
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
        $cartItems = $user->cartItems()->with('course.category', 'course.instructor', 'bundle.courses')->get();

        $total = $cartItems->sum(function($item) {
            return $item->getPrice();
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
     * Add a bundle to cart
     */
    public function addBundle(Request $request, Bundle $bundle)
    {
        $user = Auth::user();

        // Check if already in cart
        $existingItem = $user->cartItems()->where('bundle_id', $bundle->id)->first();
        if ($existingItem) {
            return response()->json([
                'success' => false,
                'message' => 'Bundle is already in your cart'
            ]);
        }

        // Add to cart
        CartItem::create([
            'user_id' => $user->id,
            'bundle_id' => $bundle->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Bundle added to cart successfully'
        ]);
    }

    /**
     * Remove a bundle from cart
     */
    public function removeBundle(Request $request, Bundle $bundle)
    {
        $user = Auth::user();

        // Remove from cart
        $user->cartItems()->where('bundle_id', $bundle->id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bundle removed from cart successfully'
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
}
