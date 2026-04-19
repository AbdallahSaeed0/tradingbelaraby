<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Models\Course;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CourseEnrollment;
use App\Models\Coupon;
use App\Support\Platform;
use App\Services\Payment\PayPalService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Create a new order
     */
    public function store(Request $request, PayPalService $paypalService): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $request->validate([
            'course_ids' => 'required|array|min:1',
            'course_ids.*' => 'exists:courses,id',
            'payment_method' => 'required|in:visa,free,cash_on_delivery,paypal',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        DB::beginTransaction();
        try {
            // Get courses
            $courses = Course::whereIn('id', $request->course_ids)->get();
            
            if ($courses->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid courses found',
                ], 400);
            }

            if (Platform::isIOS($request)) {
                $hasPaidCourse = $courses->contains(fn ($course) => !$course->is_free && (float) $course->price > 0);
                if ($hasPaidCourse) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Paid courses are not available on this platform.',
                    ], 403);
                }
            }

            // Calculate subtotal
            $subtotal = $courses->sum('price');
            $discountAmount = 0;
            $couponId = null;

            // Apply coupon if provided
            if ($request->coupon_code) {
                $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();
                if ($coupon && $coupon->isValidForUser($user)) {
                    $discountAmount = $coupon->calculateDiscount($subtotal);
                    $couponId = $coupon->id;
                }
            }

            $total = $subtotal - $discountAmount;

            // For free courses, total should be 0
            $allFree = $courses->every(fn($course) => $course->is_free || $course->price == 0);
            if ($allFree) {
                $total = 0;
                $subtotal = 0;
            }

            // PayPal not valid for free-only orders
            if ($request->payment_method === 'paypal' && $total <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'PayPal is only for paid orders.',
                ], 400);
            }

            // Generate order number
            $orderNumber = 'ORD-' . strtoupper(Str::random(8)) . '-' . now()->format('Ymd');

            // Create order (billing from user profile for API/mobile checkout)
            $parts = explode(' ', $user->name, 2);
            $order = Order::create([
                'user_id' => $user->id,
                'order_number' => $orderNumber,
                'subtotal' => $subtotal,
                'total' => $total,
                'discount_amount' => $discountAmount,
                'coupon_id' => $couponId,
                'payment_method' => $request->payment_method,
                'payment_gateway_id' => null,
                'status' => $request->payment_method === 'cash_on_delivery' ? 'pending' : ($total == 0 ? 'completed' : 'pending'),
                'billing_email' => $user->email,
                'billing_first_name' => $parts[0] ?? '',
                'billing_last_name' => $parts[1] ?? '',
                'billing_phone' => $user->phone ?? '',
                'billing_address' => $user->address ?? '',
                'billing_city' => $user->city ?? '',
                'billing_state' => $user->state ?? '',
                'billing_postal_code' => $user->postal_code ?? '',
                'billing_country' => $user->country ?? '',
            ]);

            // Create order items
            foreach ($courses as $course) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'course_id' => $course->id,
                    'price' => $course->price,
                ]);
            }

            $approvalUrl = null;

            // PayPal: create PayPal order and return approval URL for mobile to open
            if ($request->payment_method === 'paypal' && $total > 0) {
                $items = $courses->map(fn($course) => [
                    'id' => $course->id,
                    'title' => $course->name,
                    'description' => 'Course: ' . $course->name,
                    'quantity' => 1,
                    'unit_price' => $course->price,
                ])->toArray();
                $customer = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '',
                ];
                try {
                    $paypalOrder = $paypalService->createOrder($order, $items, $customer);
                    $order->update(['payment_gateway_id' => $paypalOrder['id'] ?? null]);
                    foreach ($paypalOrder['links'] ?? [] as $link) {
                        if (($link['rel'] ?? '') === 'approve') {
                            $approvalUrl = $link['href'];
                            break;
                        }
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('PayPal API order create failed: ' . $e->getMessage());
                    return response()->json([
                        'success' => false,
                        'message' => 'Unable to start PayPal payment: ' . $e->getMessage(),
                    ], 502);
                }
            }

            // If cash-on-delivery or free (not PayPal), create enrollments
            if (in_array($request->payment_method, ['cash_on_delivery']) || $total == 0) {
                foreach ($courses as $course) {
                    $existingEnrollment = CourseEnrollment::where('user_id', $user->id)
                        ->where('course_id', $course->id)
                        ->first();

                    if (!$existingEnrollment) {
                        CourseEnrollment::create([
                            'user_id' => $user->id,
                            'course_id' => $course->id,
                            'transaction_id' => $order->order_number,
                            'status' => $total == 0 ? 'active' : 'pending',
                            'enrolled_at' => $total == 0 ? now() : null,
                            'progress_percentage' => 0,
                            'payment_method' => $request->payment_method,
                            'amount_paid' => $course->price,
                        ]);
                    }
                }
            }

            if ($total == 0) {
                $order->update(['status' => 'completed']);
            }

            DB::commit();

            $response = [
                'success' => true,
                'message' => $total == 0
                    ? 'Order created and enrollment completed successfully'
                    : ($request->payment_method === 'paypal'
                        ? 'Complete your payment in the browser.'
                        : ($request->payment_method === 'cash_on_delivery'
                            ? 'Order created. Enrollment will be activated after payment confirmation.'
                            : 'Order created successfully')),
                'data' => new OrderResource($order->load('items')),
            ];
            if ($approvalUrl !== null) {
                $response['approval_url'] = $approvalUrl;
            }

            return response()->json($response, 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's orders
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (Platform::isIOS($request)) {
            return response()->json([
                'success' => true,
                'data' => [],
                'meta' => [
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 20,
                    'total' => 0,
                ],
            ]);
        }

        $orders = $user->orders()
            ->with('items.course')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => OrderResource::collection($orders->items()),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    /**
     * Get order details
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (Platform::isIOS($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Orders are not available on this platform.',
            ], 403);
        }

        $order = $user->orders()
            ->with('items.course')
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new OrderResource($order),
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        if (Platform::isIOS($request)) {
            return response()->json([
                'success' => false,
                'message' => 'Orders are not available on this platform.',
            ], 403);
        }

        $order = $user->orders()->findOrFail($id);

        if ($order->status === 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel a completed order',
            ], 400);
        }

        $order->update(['status' => 'cancelled']);

        // Cancel related enrollments
        CourseEnrollment::where('transaction_id', $order->order_number)
            ->where('user_id', $user->id)
            ->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully',
            'data' => new OrderResource($order->load('items')),
        ]);
    }
}
