<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturesSplit;
use App\Models\FeaturesSplitItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FeaturesSplitController extends Controller
{
    public function index()
    {
        $mainContent = FeaturesSplit::first();
        $features = FeaturesSplitItem::ordered()->get();

        return view('admin.settings.features-split.index', compact('mainContent', 'features'));
    }

    public function store(Request $request)
    {
        // Debug: Log the request data
        Log::info('Features Split Store Request:', $request->all());
        Log::info('Features Split Store Request Files:', $request->allFiles());
        Log::info('Features Split Store Request Has Title:', ['has_title' => $request->has('title')]);
        Log::info('Features Split Store Request Has Description:', ['has_description' => $request->has('description')]);

        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'title_ar' => 'nullable|string|max:255',
                'description' => 'required|string',
                'description_ar' => 'nullable|string',
                'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_active' => 'nullable|in:0,1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Features Split Validation Errors:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $data = $request->except(['background_image', 'main_image']);
        $data['is_active'] = $request->input('is_active') == '1';

        // Handle background image upload
        if ($request->hasFile('background_image')) {
            if ($request->old_background_image) {
                Storage::disk('public')->delete($request->old_background_image);
            }
            $data['background_image'] = $request->file('background_image')->store('features-split', 'public');
        }

        // Handle main image upload
        if ($request->hasFile('main_image')) {
            if ($request->old_main_image) {
                Storage::disk('public')->delete($request->old_main_image);
            }
            $data['main_image'] = $request->file('main_image')->store('features-split', 'public');
        }

        FeaturesSplit::updateOrCreate(['id' => 1], $data);

        return response()->json([
            'success' => true,
            'message' => __('Features Split Section updated successfully!')
        ]);
    }

    public function toggleStatus()
    {
        $content = FeaturesSplit::first();
        if ($content) {
            $content->update(['is_active' => !$content->is_active]);
            return response()->json([
                'success' => true,
                'message' => $content->is_active ? __('Section activated!') : __('Section deactivated!')
            ]);
        }
        return response()->json(['success' => false, 'message' => __('Content not found!')]);
    }

    // Features Items Management
    public function storeFeature(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'icon' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|in:0,1',
        ]);

        $data = $request->except(['is_active']);
        $data['is_active'] = $request->input('is_active') == '1';
        $data['order'] = $data['order'] ?? FeaturesSplitItem::max('order') + 1;

        FeaturesSplitItem::create($data);

        return response()->json([
            'success' => true,
            'message' => __('Feature added successfully!')
        ]);
    }

    public function updateFeature(Request $request, FeaturesSplitItem $feature)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'icon' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|in:0,1',
        ]);

        $data = $request->except(['is_active']);
        $data['is_active'] = $request->input('is_active') == '1';

        $feature->update($data);

        return response()->json([
            'success' => true,
            'message' => __('Feature updated successfully!')
        ]);
    }

    public function destroyFeature(FeaturesSplitItem $feature)
    {
        $feature->delete();

        return response()->json([
            'success' => true,
            'message' => __('Feature deleted successfully!')
        ]);
    }

    public function toggleFeatureStatus(FeaturesSplitItem $feature)
    {
        $feature->update(['is_active' => !$feature->is_active]);

        return response()->json([
            'success' => true,
            'message' => $feature->is_active ? __('Feature activated!') : __('Feature deactivated!')
        ]);
    }

    public function updateFeaturesOrder(Request $request)
    {
        $request->validate([
            'features' => 'required|array',
            'features.*.id' => 'required|exists:features_split_items,id',
            'features.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->features as $feature) {
            FeaturesSplitItem::where('id', $feature['id'])->update(['order' => $feature['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Features order updated successfully!')
        ]);
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'features' => 'required|array',
            'features.*' => 'exists:features_split_items,id',
        ]);

        $features = FeaturesSplitItem::whereIn('id', $request->features);

        switch ($request->action) {
            case 'activate':
                $features->update(['is_active' => true]);
                $message = __('Features activated successfully!');
                break;
            case 'deactivate':
                $features->update(['is_active' => false]);
                $message = __('Features deactivated successfully!');
                break;
            case 'delete':
                $features->delete();
                $message = __('Features deleted successfully!');
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }
}
