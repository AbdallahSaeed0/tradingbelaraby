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

        $total = $cartItems->sum(function ($item) {
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

        if ($user->hasInCart($course)) {
            return response()->json([
                'success' => false,
                'message' => custom_trans('Course is already in your cart', 'front'),
            ]);
        }

        if ($user->blocksCoursePurchase($course->id)) {
            return response()->json([
                'success' => false,
                'message' => custom_trans('You are already enrolled in this course', 'front'),
            ]);
        }

        CartItem::create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => custom_trans('Course added to cart successfully', 'front'),
        ]);
    }

    /**
     * Remove a course from cart
     */
    public function remove(Request $request, Course $course)
    {
        $user = Auth::user();
        $user->cartItems()->where('course_id', $course->id)->delete();

        return response()->json([
            'success' => true,
            'message' => custom_trans('Course removed from cart successfully', 'front'),
        ]);
    }

    /**
     * Add a bundle to cart
     */
    public function addBundle(Request $request, Bundle $bundle)
    {
        $user = Auth::user();

        $existingItem = $user->cartItems()->where('bundle_id', $bundle->id)->first();
        if ($existingItem) {
            return response()->json([
                'success' => false,
                'message' => custom_trans('Bundle is already in your cart', 'front'),
            ]);
        }

        CartItem::create([
            'user_id'   => $user->id,
            'bundle_id' => $bundle->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => custom_trans('Bundle added to cart successfully', 'front'),
        ]);
    }

    /**
     * Remove a bundle from cart
     */
    public function removeBundle(Request $request, Bundle $bundle)
    {
        $user = Auth::user();
        $user->cartItems()->where('bundle_id', $bundle->id)->delete();

        return response()->json([
            'success' => true,
            'message' => custom_trans('Bundle removed from cart successfully', 'front'),
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
            'message' => custom_trans('Cart cleared successfully', 'front'),
        ]);
    }
}
