<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MainContentSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'logo_alt_text' => 'nullable|string|max:255',
            'site_name' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:500',
            'site_keywords' => 'nullable|string|max:500',
            'site_author' => 'nullable|string|max:255',
        ]);

        try {
            // Get existing settings or create new ones
            $settings = MainContentSettings::getActive();
            if (!$settings) {
                $settings = new MainContentSettings();
                $settings->is_active = true;
            }

            $data = $request->except(['logo']);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                // Delete old logo if exists
                if ($settings->logo && !filter_var($settings->logo, FILTER_VALIDATE_URL)) {
                    Storage::disk('public')->delete($settings->logo);
                }

                $logoPath = $request->file('logo')->store('logos', 'public');
                $data['logo'] = $logoPath;
            }

            $settings->fill($data);
            $settings->save();

            return redirect()->route('admin.settings.main-content.index')
                ->with('success', 'Main content settings updated successfully.');

        } catch (\Exception $e) {
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
}
