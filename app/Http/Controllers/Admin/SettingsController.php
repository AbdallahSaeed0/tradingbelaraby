<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        return view('admin.settings.index');
    }

    /**
     * Display the sliders index page with filters.
     */
    public function slidersIndex(Request $request)
    {
        $query = Slider::query();

        // Apply filters
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
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

        $sliders = $query->get();

        return view('admin.settings.sliders.index', compact('sliders'));
    }

    /**
     * Perform bulk actions on sliders.
     */
    public function slidersBulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete',
            'slider_ids' => 'required|array',
            'slider_ids.*' => 'exists:sliders,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $sliderIds = $request->slider_ids;
            $action = $request->action;

            switch ($action) {
                case 'activate':
                    Slider::whereIn('id', $sliderIds)->update(['is_active' => true]);
                    $message = __('Selected sliders have been activated successfully');
                    break;

                case 'deactivate':
                    Slider::whereIn('id', $sliderIds)->update(['is_active' => false]);
                    $message = __('Selected sliders have been deactivated successfully');
                    break;

                case 'delete':
                    $sliders = Slider::whereIn('id', $sliderIds)->get();
                    foreach ($sliders as $slider) {
                        // Delete image file
                        if ($slider->background_image && !filter_var($slider->background_image, FILTER_VALIDATE_URL)) {
                            Storage::disk('public')->delete($slider->background_image);
                        }
                    }
                    Slider::whereIn('id', $sliderIds)->delete();
                    $message = __('Selected sliders have been deleted successfully');
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
     * Store a newly created slider.
     */
    public function storeSlider(Request $request)
    {
        \App::setLocale(session('admin_locale', 'en'));

        // Check if file upload failed due to PHP/server limits (before validation)
        if ($request->hasFile('background_image') && !$request->file('background_image')->isValid()) {
            $err = $request->file('background_image')->getError();
            if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'background_image' => [
                            custom_trans('The image exceeds the server upload limit. Please contact your hosting provider to increase upload_max_filesize and post_max_size to at least 6MB.', 'admin')
                        ]
                    ]
                ], 422);
            }
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'welcome_text' => 'nullable|string|max:255',
            'welcome_text_ar' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'subtitle_ar' => 'nullable|string|max:255',
            'text_position' => 'nullable|string|in:top-left,top-center,top-right,center-left,center-center,center-right,bottom-left,bottom-center,bottom-right',
            'background_image' => 'required|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_text_ar' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:255',
            'search_placeholder' => 'nullable|string|max:255',
            'search_placeholder_ar' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $messages = [
            'background_image.required' => custom_trans('Please select an image to upload.', 'admin'),
            'background_image.image' => custom_trans('The file must be an image (jpg, jpeg, png, webp, gif).', 'admin'),
            'background_image.mimes' => custom_trans('The image must be a file of type: jpg, jpeg, png, webp, gif.', 'admin'),
            'background_image.max' => custom_trans('The image may not be greater than 5MB.', 'admin'),
            'background_image.uploaded' => custom_trans('The image could not be uploaded. Please ensure it is under 5MB and is a valid image file.', 'admin'),
        ];
        $validator->setCustomMessages($messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            // Handle image upload
            if ($request->hasFile('background_image')) {
                $imagePath = $request->file('background_image')->store('sliders', 'public');
                $data['background_image'] = $imagePath;
            }

            $data['is_active'] = $request->has('is_active') && $request->input('is_active') === 'on';

            $slider = Slider::create($data);

            return response()->json([
                'success' => true,
                'message' => __('Slider created successfully'),
                'slider' => $slider
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error creating slider: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified slider.
     */
    public function updateSlider(Request $request, Slider $slider)
    {
        \App::setLocale(session('admin_locale', 'en'));

        // Check if file upload failed due to PHP/server limits (before validation)
        if ($request->hasFile('background_image') && !$request->file('background_image')->isValid()) {
            $err = $request->file('background_image')->getError();
            if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) {
                return response()->json([
                    'success' => false,
                    'errors' => [
                        'background_image' => [
                            custom_trans('The image exceeds the server upload limit. Please contact your hosting provider to increase upload_max_filesize and post_max_size to at least 6MB.', 'admin')
                        ]
                    ]
                ], 422);
            }
        }

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'welcome_text' => 'nullable|string|max:255',
            'welcome_text_ar' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'subtitle_ar' => 'nullable|string|max:255',
            'text_position' => 'nullable|string|in:top-left,top-center,top-right,center-left,center-center,center-right,bottom-left,bottom-center,bottom-right',
            'background_image' => 'nullable|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
            'button_text' => 'nullable|string|max:100',
            'button_text_ar' => 'nullable|string|max:100',
            'button_url' => 'nullable|url|max:255',
            'search_placeholder' => 'nullable|string|max:255',
            'search_placeholder_ar' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ]);

        $messages = [
            'background_image.image' => custom_trans('The file must be an image (jpg, jpeg, png, webp, gif).', 'admin'),
            'background_image.mimes' => custom_trans('The image must be a file of type: jpg, jpeg, png, webp, gif.', 'admin'),
            'background_image.max' => custom_trans('The image may not be greater than 5MB.', 'admin'),
            'background_image.uploaded' => custom_trans('The image could not be uploaded. Please ensure it is under 5MB and is a valid image file.', 'admin'),
        ];
        $validator->setCustomMessages($messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = $request->all();

            // Handle image upload
            if ($request->hasFile('background_image')) {
                // Delete old image
                if ($slider->background_image && !filter_var($slider->background_image, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($slider->background_image);
                }

                $imagePath = $request->file('background_image')->store('sliders', 'public');
                $data['background_image'] = $imagePath;
            }

            $data['is_active'] = $request->has('is_active') && $request->input('is_active') === 'on';

            $slider->update($data);

            return response()->json([
                'success' => true,
                'message' => __('Slider updated successfully'),
                'slider' => $slider
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating slider: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified slider.
     */
    public function destroySlider(Slider $slider)
    {
        try {
            // Delete image file
            if ($slider->background_image && !filter_var($slider->background_image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($slider->background_image);
            }

            $slider->delete();

            return response()->json([
                'success' => true,
                'message' => __('Slider deleted successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting slider: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle slider status.
     */
    public function toggleSliderStatus(Slider $slider)
    {
        try {
            $slider->update(['is_active' => !$slider->is_active]);

            return response()->json([
                'success' => true,
                'message' => __('Slider status updated successfully'),
                'is_active' => $slider->is_active
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating slider status: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update slider order.
     */
    public function updateSliderOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sliders' => 'required|array',
            'sliders.*.id' => 'required|exists:sliders,id',
            'sliders.*.order' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            foreach ($request->sliders as $sliderData) {
                Slider::where('id', $sliderData['id'])->update(['order' => $sliderData['order']]);
            }

            return response()->json([
                'success' => true,
                'message' => __('Slider order updated successfully')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error updating slider order: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update coming soon mode.
     */
    public function updateComingSoon(Request $request)
    {
        $request->validate([
            'coming_soon_enabled' => 'required|in:0,1'
        ]);

        $enabled = $request->input('coming_soon_enabled') == '1';

        // Get or create the main content settings
        $settings = \App\Models\MainContentSettings::getActive();
        if (!$settings) {
            $settings = new \App\Models\MainContentSettings();
            $settings->is_active = true;
        }

        // Update the coming soon setting
        $settings->coming_soon_enabled = $enabled;
        $settings->save();

        $status = $enabled ? 'enabled' : 'disabled';

        // Redirect back to the main content settings page with success message
        return redirect()->route('admin.settings.main-content.index')
            ->with('success', "Coming soon mode has been {$status} successfully.");
    }
}
