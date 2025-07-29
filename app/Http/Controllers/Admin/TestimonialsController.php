<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialsController extends Controller
{
    /**
     * Display a listing of testimonials.
     */
    public function index(Request $request)
    {
        $query = Testimonial::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort
        $sort = $request->get('sort', 'order');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $testimonials = $query->paginate(15);

        return view('admin.settings.testimonials.index', compact('testimonials'));
    }

    /**
     * Store a newly created testimonial.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'position' => 'required|string|max:255',
            'position_ar' => 'nullable|string|max:255',
            'company' => 'required|string|max:255',
            'company_ar' => 'nullable|string|max:255',
            'content' => 'required|string',
            'content_ar' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rating' => 'required|integer|min:1|max:5',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('avatar');
        $data['is_active'] = $request->has('is_active');

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('testimonials', 'public');
            $data['avatar'] = $avatarPath;
        }

        Testimonial::create($data);

        return response()->json([
            'success' => true,
            'message' => __('Testimonial created successfully'),
        ]);
    }

    /**
     * Update the specified testimonial.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'position' => 'required|string|max:255',
            'position_ar' => 'nullable|string|max:255',
            'company' => 'required|string|max:255',
            'company_ar' => 'nullable|string|max:255',
            'content' => 'required|string',
            'content_ar' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'rating' => 'required|integer|min:1|max:5',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('avatar');
        $data['is_active'] = $request->has('is_active');

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($testimonial->avatar) {
                Storage::disk('public')->delete($testimonial->avatar);
            }

            $avatarPath = $request->file('avatar')->store('testimonials', 'public');
            $data['avatar'] = $avatarPath;
        }

        $testimonial->update($data);

        return response()->json([
            'success' => true,
            'message' => __('Testimonial updated successfully'),
        ]);
    }

    /**
     * Remove the specified testimonial.
     */
    public function destroy(Testimonial $testimonial)
    {
        // Delete avatar file
        if ($testimonial->avatar) {
            Storage::disk('public')->delete($testimonial->avatar);
        }

        $testimonial->delete();

        return response()->json([
            'success' => true,
            'message' => __('Testimonial deleted successfully'),
        ]);
    }

    /**
     * Toggle testimonial status.
     */
    public function toggleStatus(Testimonial $testimonial)
    {
        $testimonial->update(['is_active' => !$testimonial->is_active]);

        return response()->json([
            'success' => true,
            'message' => __('Testimonial status updated successfully'),
            'is_active' => $testimonial->is_active,
        ]);
    }

    /**
     * Update testimonials order.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'testimonials' => 'required|array',
            'testimonials.*.id' => 'required|exists:testimonials,id',
            'testimonials.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->testimonials as $item) {
            Testimonial::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Testimonials order updated successfully'),
        ]);
    }

    /**
     * Bulk action on testimonials.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'testimonials' => 'required|array',
            'testimonials.*' => 'exists:testimonials,id',
        ]);

        $testimonials = Testimonial::whereIn('id', $request->testimonials);

        switch ($request->action) {
            case 'activate':
                $testimonials->update(['is_active' => true]);
                $message = __('Testimonials activated successfully');
                break;
            case 'deactivate':
                $testimonials->update(['is_active' => false]);
                $message = __('Testimonials deactivated successfully');
                break;
            case 'delete':
                // Delete avatar files
                $testimonials->get()->each(function($testimonial) {
                    if ($testimonial->avatar) {
                        Storage::disk('public')->delete($testimonial->avatar);
                    }
                });
                $testimonials->delete();
                $message = __('Testimonials deleted successfully');
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
}
