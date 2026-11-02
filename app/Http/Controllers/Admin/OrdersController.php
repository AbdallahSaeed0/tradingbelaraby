<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\CourseEnrollment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderItems.course', 'orderItems.bundle', 'coupon']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('payment_gateway_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $perPage = admin_per_page();
        $orders = $query->latest()->paginate($perPage)->appends($request->query());

        $stats = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'bank_transfer_pending' => Order::where('payment_method', 'bank_transfer')->where('status', 'pending')->count(),
            'revenue' => Order::where('status', 'completed')->sum('total'),
        ];

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.course', 'orderItems.bundle.courses', 'coupon']);

        $courseIds = $order->getCourseIds();
        $enrollments = CourseEnrollment::with('course')
            ->where('user_id', $order->user_id)
            ->whereIn('course_id', $courseIds)
            ->get();

        return view('admin.orders.show', compact('order', 'enrollments'));
    }

    public function confirm(Order $order)
    {
        if ($order->status === 'completed') {
            return back()->with('error', 'This order is already confirmed.');
        }

        DB::transaction(fn () => $this->confirmOrder($order));

        return back()->with('success', 'Order confirmed and enrollments activated successfully.');
    }

    public function reject(Order $order)
    {
        if ($order->status === 'completed') {
            return back()->with('error', 'Cannot reject a completed order.');
        }

        DB::transaction(fn () => $this->rejectOrder($order));

        return back()->with('success', 'Order rejected and pending enrollments cancelled.');
    }

    public function destroy(Order $order)
    {
        DB::transaction(function () use ($order) {
            $this->deleteOrder($order);
            CouponUsage::pruneOrphanedUsages();
        });

        return redirect()
            ->route('admin.orders.index')
            ->with('success', 'Order and linked enrollments deleted successfully.');
    }

    /**
     * Confirm, reject or delete several orders at once (same rules as the single actions)
     */
    public function bulkAction(Request $request)
    {
        $data = $request->validate([
            'action' => 'required|in:confirm,reject,delete',
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:orders,id',
        ]);

        $orders = Order::whereIn('id', $data['ids'])->get();
        $action = $data['action'];

        [$done, $skipped] = DB::transaction(function () use ($orders, $action) {
            $done = 0;
            $skipped = 0;

            foreach ($orders as $order) {
                if ($action === 'confirm') {
                    if ($order->status === 'completed') {
                        $skipped++;
                        continue;
                    }
                    $this->confirmOrder($order);
                } elseif ($action === 'reject') {
                    if ($order->status === 'completed') {
                        $skipped++;
                        continue;
                    }
                    $this->rejectOrder($order);
                } else {
                    $this->deleteOrder($order);
                }
                $done++;
            }

            if ($action === 'delete') {
                CouponUsage::pruneOrphanedUsages();
            }

            return [$done, $skipped];
        });

        $verb = ['confirm' => 'confirmed', 'reject' => 'rejected', 'delete' => 'deleted'][$action];
        $message = $done . ' ' . \Illuminate\Support\Str::plural('order', $done) . ' ' . $verb . '.';

        if ($skipped > 0) {
            $message .= ' ' . $skipped . ' already completed ' . \Illuminate\Support\Str::plural('order', $skipped) . ' skipped.';
        }

        return back()->with('success', $message);
    }

    private function confirmOrder(Order $order): void
    {
        CourseEnrollment::where('user_id', $order->user_id)
            ->whereIn('course_id', $order->getCourseIds())
            ->where('status', 'pending')
            ->update([
                'status' => 'active',
                'enrolled_at' => now(),
            ]);

        $order->update(['status' => 'completed']);
        $order->applyEnrollmentAccess();
    }

    private function rejectOrder(Order $order): void
    {
        CourseEnrollment::where('user_id', $order->user_id)
            ->whereIn('course_id', $order->getCourseIds())
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        $this->releaseCouponUsage($order);

        $order->update(['status' => 'cancelled']);
    }

    private function deleteOrder(Order $order): void
    {
        $couponIds = $this->couponIdsForOrder($order);

        $this->linkedEnrollments($order)->delete();
        $this->releaseCouponUsage($order);
        $order->delete();

        $this->syncCouponsUsedCount($couponIds);
    }

    private function linkedEnrollments(Order $order)
    {
        $courseIds = $order->getCourseIds();

        // Only enrollments created by this order; an extension/upgrade order must not remove the existing enrollment
        return CourseEnrollment::where('user_id', $order->user_id)
            ->whereIn('course_id', $courseIds)
            ->whereIn('transaction_id', array_filter([$order->order_number, $order->payment_gateway_id]));
    }

    private function couponIdsForOrder(Order $order): array
    {
        $ids = CouponUsage::where('order_id', $order->id)->pluck('coupon_id')->all();

        if ($order->coupon_id) {
            $ids[] = $order->coupon_id;
        }

        return array_values(array_unique($ids));
    }

    private function releaseCouponUsage(Order $order): void
    {
        $couponIds = $this->couponIdsForOrder($order);

        CouponUsage::where('order_id', $order->id)->delete();

        $this->syncCouponsUsedCount($couponIds);
    }

    private function syncCouponsUsedCount(array $couponIds): void
    {
        foreach (array_unique($couponIds) as $couponId) {
            Coupon::find($couponId)?->syncUsedCount();
        }
    }
}
