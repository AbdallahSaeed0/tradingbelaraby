<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FeaturesController extends Controller
{
    /**
     * Display the features index page with filters.
     */
    public function index(Request $request)
    {
        $query = Feature::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        $orderBy = $request->get('order_by', 'order');
        switch ($orderBy) {
            case 'title':
                $query->orderBy('title', 'asc');
                break;
            case 'created_at':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('order', 'asc');
                break;
        }

        $features = $query->get();

        return view('admin.settings.features.index', compact('features'));
    }

    /**
     * Perform bulk actions on features.
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'feature_ids' => 'required|array',
            'feature_ids.*' => 'exists:features,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $featureIds = $request->feature_ids;
            $action = $request->action;

            switch ($action) {
                case 'activate':
                    Feature::whereIn('id', $featureIds)->update(['is_active' => true]);
                    $message = __('Selected features have been activated successfully');
                    break;

                case 'deactivate':
                    Feature::whereIn('id', $featureIds)->update(['is_active' => false]);
                    $message = __('Selected features have been deactivated successfully');
                    break;

                case 'delete':
                    $features = Feature::whereIn('id', $featureIds)->get();
                    foreach ($features as $feature) {
                        // Delete icon file
                        if ($feature->icon && !filter_var($feature->icon, FILTER_VALIDATE_URL)) {
                            Storage::disk('public')->delete($feature->icon);
                        }
                    }
                    Feature::whereIn('id', $featureIds)->delete();
                    $message = __('Selected features have been deleted successfully');
                    break;
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error performing bulk action: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created feature.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'number' => 'required|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            // Handle icon upload
            if ($request->hasFile('icon')) {
                $iconPath = $request->file('icon')->store('features', 'public');
                $data['icon'] = $iconPath;
            }

            $data['is_active'] = $request->has('is_active') && $request->input('is_active') === 'on';

            $feature = Feature::create($data);

            return response()->json([
                'success' => true,
                'message' => __('Feature created successfully'),
                'feature' => $feature
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error creating feature: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified feature.
     */
    public function update(Request $request, Feature $feature)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'number' => 'required|integer|min:0',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            // Handle icon upload
            if ($request->hasFile('icon')) {
                // Delete old icon
                if ($feature->icon && !filter_var($feature->icon, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($feature->icon);
                }

                $iconPath = $request->file('icon')->store('features', 'public');
                $data['icon'] = $iconPath;
            }

            $data['is_active'] = $request->has('is_active') && $request->input('is_active') === 'on';

            $feature->update($data);

            return response()->json([
                'success' => true,
                'message' => __('Feature updated successfully'),
                'feature' => $feature
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating feature: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified feature.
     */
    public function destroy(Feature $feature)
    {
        try {
            // Delete icon file
            if ($feature->icon && !filter_var($feature->icon, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($feature->icon);
            }

            $feature->delete();

            return response()->json([
                'success' => true,
                'message' => __('Feature deleted successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting feature: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle feature status.
     */
    public function toggleStatus(Feature $feature)
    {
        try {
            $feature->update(['is_active' => !$feature->is_active]);

            return response()->json([
                'success' => true,
                'message' => __('Feature status updated successfully'),
                'is_active' => $feature->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating feature status: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update feature order.
     */
    public function updateOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'features' => 'required|array',
            'features.*.id' => 'required|exists:features,id',
            'features.*.order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->features as $featureData) {
                Feature::where('id', $featureData['id'])->update(['order' => $featureData['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => __('Feature order updated successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating feature order: ') . $e->getMessage()
            ], 500);
        }
    }
}
