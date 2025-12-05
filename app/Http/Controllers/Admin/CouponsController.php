<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::with(['course', 'user']);

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('name', 'like', '%' . $request->search . '%');
        }

        // Apply status filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Apply type filter
        if ($request->filled('discount_type')) {
            $query->where('discount_type', $request->discount_type);
        }

        // Pagination
        $perPage = (int) $request->get('per_page', 10);
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $coupons = $query->latest()->paginate($perPage);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $courses = Course::published()->orderBy('name')->get();
        $users = User::orderBy('name')->get();
        
        return view('admin.coupons.create', compact('courses', 'users'));
    }

    public function store(Request $request)
    {
        try {
            // Handle is_active before validation (checkboxes don't send value when unchecked)
            $request->merge(['is_active' => $request->has('is_active') ? 1 : 0]);
            
            $data = $request->validate([
                'code' => 'required|string|max:255|unique:coupons,code',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'required|numeric|min:0',
                'scope' => 'required|in:all_courses,specific_course',
                'course_id' => 'required_if:scope,specific_course|nullable|exists:courses,id',
                'user_scope' => 'required|in:all_users,specific_user',
                'user_id' => 'required_if:user_scope,specific_user|nullable|exists:users,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'usage_limit' => 'nullable|integer|min:1',
                'per_user_limit' => 'required|integer|min:1',
                'is_active' => 'required|boolean',
            ], [
                'image.max' => 'The image must not be greater than 2048 kilobytes.',
            ]);

            // Convert code to uppercase
            $data['code'] = strtoupper($data['code']);

            // Set is_active (already handled in merge above, but ensure boolean)
            $data['is_active'] = (bool) $data['is_active'];

            // Create coupon
            Coupon::create($data);

            return redirect()->route('admin.coupons.index')
                ->with('success', 'Coupon created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Coupon $coupon)
    {
        $coupon->load(['course', 'user', 'usages.user', 'usages.order']);
        
        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        $courses = Course::published()->orderBy('name')->get();
        $users = User::orderBy('name')->get();
        
        return view('admin.coupons.edit', compact('coupon', 'courses', 'users'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        try {
            // Handle is_active before validation (checkboxes don't send value when unchecked)
            $request->merge(['is_active' => $request->has('is_active') ? 1 : 0]);
            
            $data = $request->validate([
                'code' => 'required|string|max:255|unique:coupons,code,' . $coupon->id,
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'discount_type' => 'required|in:percentage,fixed',
                'discount_value' => 'required|numeric|min:0',
                'scope' => 'required|in:all_courses,specific_course',
                'course_id' => 'required_if:scope,specific_course|nullable|exists:courses,id',
                'user_scope' => 'required|in:all_users,specific_user',
                'user_id' => 'required_if:user_scope,specific_user|nullable|exists:users,id',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
                'usage_limit' => 'nullable|integer|min:1',
                'per_user_limit' => 'required|integer|min:1',
                'is_active' => 'required|boolean',
            ]);

            // Convert code to uppercase
            $data['code'] = strtoupper($data['code']);

            // Set is_active (already handled in merge above, but ensure boolean)
            $data['is_active'] = (bool) $data['is_active'];

            // Update coupon
            $coupon->update($data);

            return redirect()->route('admin.coupons.index')
                ->with('success', 'Coupon updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }
}

