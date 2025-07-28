<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUniversity;
use App\Models\AboutUniversityFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class AboutUniversityController extends Controller
{
    /**
     * Display the about university management page.
     */
    public function index()
    {
        $aboutUniversity = AboutUniversity::first();
        $features = AboutUniversityFeature::active()->ordered()->get();
        
        return view('admin.settings.about-university.index', compact('aboutUniversity', 'features'));
    }

    /**
     * Store or update about university content.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'background_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->except(['image', 'background_image']);
            $data['is_active'] = $request->has('is_active') && $request->input('is_active') === 'on';

            // Handle image upload
            if ($request->hasFile('image')) {
                if ($request->file('image')->isValid()) {
                    $imagePath = $request->file('image')->store('about-university', 'public');
                    $data['image'] = $imagePath;
                }
            }

            // Handle background image upload
            if ($request->hasFile('background_image')) {
                if ($request->file('background_image')->isValid()) {
                    $bgImagePath = $request->file('background_image')->store('about-university', 'public');
                    $data['background_image'] = $bgImagePath;
                }
            }

            $aboutUniversity = AboutUniversity::first();
            
            if ($aboutUniversity) {
                $aboutUniversity->update($data);
                $message = __('About university content updated successfully');
            } else {
                $aboutUniversity = AboutUniversity::create($data);
                $message = __('About university content created successfully');
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'aboutUniversity' => $aboutUniversity
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error saving about university content: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle about university status.
     */
    public function toggleStatus()
    {
        try {
            $aboutUniversity = AboutUniversity::first();
            
            if ($aboutUniversity) {
                $aboutUniversity->update(['is_active' => !$aboutUniversity->is_active]);

                return response()->json([
                    'success' => true,
                    'message' => __('About university status updated successfully'),
                    'is_active' => $aboutUniversity->is_active
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => __('No about university content found')
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating about university status: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a new about university feature.
     */
    public function storeFeature(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'number' => 'required|integer|min:1',
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

            $feature = AboutUniversityFeature::create($data);

            return response()->json([
                'success' => true,
                'message' => __('About university feature created successfully'),
                'feature' => $feature
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error creating about university feature: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an about university feature.
     */
    public function updateFeature(Request $request, AboutUniversityFeature $feature)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'number' => 'required|integer|min:1',
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

            $feature->update($data);

            return response()->json([
                'success' => true,
                'message' => __('About university feature updated successfully'),
                'feature' => $feature
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating about university feature: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete an about university feature.
     */
    public function destroyFeature(AboutUniversityFeature $feature)
    {
        try {
            $feature->delete();

            return response()->json([
                'success' => true,
                'message' => __('About university feature deleted successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting about university feature: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle about university feature status.
     */
    public function toggleFeatureStatus(AboutUniversityFeature $feature)
    {
        try {
            $feature->update(['is_active' => !$feature->is_active]);

            return response()->json([
                'success' => true,
                'message' => __('About university feature status updated successfully'),
                'is_active' => $feature->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating about university feature status: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update about university features order.
     */
    public function updateFeaturesOrder(Request $request)
    {
        try {
            $request->validate([
                'orders' => 'required|array',
                'orders.*.id' => 'required|exists:about_university_features,id',
                'orders.*.order' => 'required|integer|min:0'
            ]);

            foreach ($request->orders as $item) {
                AboutUniversityFeature::where('id', $item['id'])->update(['order' => $item['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => __('About university features order updated successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating about university features order: ') . $e->getMessage()
            ], 500);
        }
    }
}
