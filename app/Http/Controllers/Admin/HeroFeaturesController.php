<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class HeroFeaturesController extends Controller
{
    /**
     * Display a listing of hero features.
     */
    public function index(Request $request)
    {
        $query = HeroFeature::query();

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('title_ar', 'like', "%{$search}%")
                  ->orWhere('subtitle', 'like', "%{$search}%")
                  ->orWhere('subtitle_ar', 'like', "%{$search}%");
            });
        }

        // Order by
        $orderBy = $request->get('order_by', 'order');
        $orderDirection = $request->get('order_direction', 'asc');
        
        if (in_array($orderBy, ['title', 'subtitle', 'order', 'created_at'])) {
            $query->orderBy($orderBy, $orderDirection);
        } else {
            $query->orderBy('order', 'asc');
        }

        $heroFeatures = $query->paginate(10);

        return view('admin.settings.hero-features.index', compact('heroFeatures'));
    }

    /**
     * Perform bulk actions on hero features.
     */
    public function bulkAction(Request $request)
    {
        try {
            $request->validate([
                'hero_feature_ids' => 'required|array',
                'hero_feature_ids.*' => 'exists:hero_features,id',
                'action' => 'required|in:activate,deactivate,delete'
            ]);

            $heroFeatureIds = $request->hero_feature_ids;
            $action = $request->action;

            switch ($action) {
                case 'activate':
                    HeroFeature::whereIn('id', $heroFeatureIds)->update(['is_active' => true]);
                    $message = __('Selected hero features have been activated successfully');
                    break;

                case 'deactivate':
                    HeroFeature::whereIn('id', $heroFeatureIds)->update(['is_active' => false]);
                    $message = __('Selected hero features have been deactivated successfully');
                    break;

                case 'delete':
                    HeroFeature::whereIn('id', $heroFeatureIds)->delete();
                    $message = __('Selected hero features have been deleted successfully');
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
     * Store a newly created hero feature.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'subtitle' => 'required|string|max:255',
            'subtitle_ar' => 'nullable|string|max:255',
            'icon' => 'required|string|max:255',
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
            $data['is_active'] = $request->has('is_active') && $request->input('is_active') === 'on';

            $heroFeature = HeroFeature::create($data);

            return response()->json([
                'success' => true,
                'message' => __('Hero feature created successfully'),
                'heroFeature' => $heroFeature
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error creating hero feature: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified hero feature.
     */
    public function update(Request $request, HeroFeature $heroFeature)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'subtitle' => 'required|string|max:255',
            'subtitle_ar' => 'nullable|string|max:255',
            'icon' => 'required|string|max:255',
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
            $data['is_active'] = $request->has('is_active') && $request->input('is_active') === 'on';

            $heroFeature->update($data);

            return response()->json([
                'success' => true,
                'message' => __('Hero feature updated successfully'),
                'heroFeature' => $heroFeature
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating hero feature: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified hero feature.
     */
    public function destroy(HeroFeature $heroFeature)
    {
        try {
            $heroFeature->delete();

            return response()->json([
                'success' => true,
                'message' => __('Hero feature deleted successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting hero feature: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle hero feature status.
     */
    public function toggleStatus(HeroFeature $heroFeature)
    {
        try {
            $heroFeature->update(['is_active' => !$heroFeature->is_active]);

            return response()->json([
                'success' => true,
                'message' => __('Hero feature status updated successfully'),
                'is_active' => $heroFeature->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating hero feature status: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update hero features order.
     */
    public function updateOrder(Request $request)
    {
        try {
            $request->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:hero_features,id',
                'orders.*.order' => 'required|integer|min:0'
            ]);

            foreach ($request->orders as $item) {
                HeroFeature::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => __('Hero features order updated successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating hero features order: ') . $e->getMessage()
            ], 500);
        }
    }
}
