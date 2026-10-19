<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Course;
use App\Models\Bundle;
use App\Models\CartItem;
use App\Models\CouponUsage;
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
        $cartItems = $user->cartItems()->with('course.category', 'course.instructor', 'course.instructors', 'bundle.courses', 'subscriptionPlan')->get();

        $total = $cartItems->sum(function ($item) {
            return $item->getPrice();
        });

        $appliedCoupon = null;
        $discount = 0;

        if (session()->has('applied_coupon') && $cartItems->isNotEmpty()) {
            CouponUsage::pruneOrphanedUsages();
            $appliedCoupon = Coupon::where('code', session('applied_coupon'))->first();

            if ($appliedCoupon && $appliedCoupon->isValidForUser($user) && $appliedCoupon->appliesToCart($cartItems)) {
                $discount = $appliedCoupon->calculateDiscountForCart($cartItems);
            } else {
                session()->forget('applied_coupon');
                $appliedCoupon = null;
            }
        }

        $grandTotal = max(0, $total - $discount);

        return view('cart.index', compact('cartItems', 'total', 'discount', 'grandTotal', 'appliedCoupon'));
    }

    /**
     * Add a course to cart
     */
    public function add(Request $request, Course $course)
    {
        $user = Auth::user();

        // plan_id = subscription period; empty = buy forever (lifetime)
        $planId = $request->input('plan_id') ?: null;
        $plan = null;

        if ($planId) {
            $plan = $course->offersSubscription()
                ? $course->activeSubscriptionPlans->firstWhere('id', (int) $planId)
                : null;

            if (!$plan) {
                return response()->json([
                    'success' => false,
                    'message' => custom_trans('This subscription plan is not available', 'front'),
                ]);
            }
        } elseif (!$course->offersLifetime()) {
            return response()->json([
                'success' => false,
                'message' => custom_trans('Please choose a subscription period', 'front'),
            ]);
        }

        $enrollment = $user->enrollments()->where('course_id', $course->id)->first();

        if ($enrollment && $enrollment->status === 'pending') {
            return response()->json([
                'success' => false,
                'message' => custom_trans('Your enrollment in this course is pending confirmation', 'front'),
            ]);
        }

        // Already owns the course forever: nothing left to buy
        if ($enrollment && $enrollment->isLifetime() && $enrollment->grantsAccess()) {
            return response()->json([
                'success' => false,
                'message' => custom_trans('You are already enrolled in this course', 'front'),
            ]);
        }

        // Existing subscribers may extend (plan) or upgrade to lifetime; anything else that blocks purchase stops here
        $isSubscriber = $enrollment && $enrollment->isSubscription();
        if (!$isSubscriber && $user->blocksCoursePurchase($course->id)) {
            return response()->json([
                'success' => false,
                'message' => custom_trans('You are already enrolled in this course', 'front'),
            ]);
        }

        $existing = $user->cartItems()->where('course_id', $course->id)->first();
        if ($existing) {
            if ((int) $existing->subscription_plan_id === (int) optional($plan)->id) {
                return response()->json([
                    'success' => false,
                    'message' => custom_trans('Course is already in your cart', 'front'),
                ]);
            }

            $existing->update(['subscription_plan_id' => optional($plan)->id]);

            return response()->json([
                'success' => true,
                'message' => custom_trans('Cart updated successfully', 'front'),
            ]);
        }

        CartItem::create([
            'user_id'              => $user->id,
            'course_id'            => $course->id,
            'subscription_plan_id' => optional($plan)->id,
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
