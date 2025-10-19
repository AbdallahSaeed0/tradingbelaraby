<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MainContentSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class MainContentSettingsController extends Controller
{
    /**
     * Display the main content settings page.
     */
    public function index()
    {
        $settings = MainContentSettings::getActive() ?? new MainContentSettings();
        return view('admin.settings.main-content.index', compact('settings'));
    }

    /**
     * Update the main content settings.
     */
    public function update(Request $request)
    {
        // Log the incoming request for debugging
        Log::info('MainContentSettings update request received', [
            'has_logo' => $request->hasFile('logo'),
            'has_favicon' => $request->hasFile('favicon'),
            'logo_file' => $request->hasFile('logo') ? $request->file('logo')->getClientOriginalName() : null,
            'favicon_file' => $request->hasFile('favicon') ? $request->file('favicon')->getClientOriginalName() : null,
        ]);

        // Enhanced validation with better error messages
        $validator = Validator::make($request->all(), [
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:ico,png,jpg|max:512',
            'logo_alt_text' => 'nullable|string|max:255',
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_keywords' => 'nullable|string|max:500',
            'site_author' => 'nullable|string|max:255',
        ], [
            'favicon.image' => 'The favicon must be an image file.',
            'favicon.mimes' => 'The favicon must be a file of type: ico, png, jpg.',
            'favicon.max' => 'The favicon may not be greater than 512 kilobytes.',
        ]);

        if ($validator->fails()) {
            Log::warning('MainContentSettings validation failed', [
                'errors' => $validator->errors()->toArray()
            ]);

            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check the form for errors: ' . implode(', ', $validator->errors()->all()));
        }

        try {
            // Get existing settings or create new ones
            $settings = MainContentSettings::getActive();
            if (!$settings) {
                $settings = new MainContentSettings();
                $settings->is_active = true;
            }

            $data = $request->except(['logo', 'favicon']);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logoFile = $request->file('logo');

                // Validate logo file is valid
                if ($logoFile->isValid()) {
                    // Delete old logo if exists
                    if ($settings->logo && !filter_var($settings->logo, FILTER_VALIDATE_URL)) {
                        Storage::disk('public')->delete($settings->logo);
                    }

                    $logoPath = $logoFile->store('logos', 'public');
                    $data['logo'] = $logoPath;
                } else {
                    return redirect()->back()
                        ->with('error', 'Logo upload failed. Please try again.')
                        ->withInput();
                }
            }

            // Handle favicon upload
            if ($request->hasFile('favicon')) {
                $faviconFile = $request->file('favicon');

                // Validate favicon file is valid
                if ($faviconFile->isValid()) {
                    // Delete old favicon if exists
                    if ($settings->favicon && !filter_var($settings->favicon, FILTER_VALIDATE_URL)) {
                        Storage::disk('public')->delete($settings->favicon);
                    }

                    $faviconPath = $faviconFile->store('favicons', 'public');
                    $data['favicon'] = $faviconPath;
                } else {
                    return redirect()->back()
                        ->with('error', 'Favicon upload failed. Please try again.')
                        ->withInput();
                }
            }

            $settings->fill($data);
            $settings->save();

            Log::info('MainContentSettings updated successfully', [
                'logo_updated' => isset($data['logo']),
                'favicon_updated' => isset($data['favicon']),
            ]);

            return redirect()->route('admin.settings.main-content.index')
                ->with('success', 'Main content settings updated successfully.');

        } catch (\Exception $e) {
            Log::error('MainContentSettings update error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Error updating settings: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the logo.
     */
    public function removeLogo()
    {
        try {
            $settings = MainContentSettings::getActive();
            if ($settings && $settings->logo) {
                // Delete logo file
                if (!filter_var($settings->logo, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($settings->logo);
                }

                $settings->update(['logo' => null]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Logo removed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing logo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the favicon.
     */
    public function removeFavicon()
    {
        try {
            $settings = MainContentSettings::getActive();
            if ($settings && $settings->favicon) {
                // Delete favicon file
                if (!filter_var($settings->favicon, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($settings->favicon);
                }
                $settings->update(['favicon' => null]);
            }
            return response()->json([
                'success' => true,
                'message' => 'Favicon removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing favicon: ' . $e->getMessage()
            ], 500);
        }
    }
}
