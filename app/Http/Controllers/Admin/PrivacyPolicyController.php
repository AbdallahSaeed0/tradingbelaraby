<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrivacyPolicy;
use Illuminate\Http\Request;

class PrivacyPolicyController extends Controller
{
    /**
     * Display the privacy policy management page.
     */
    public function index()
    {
        $privacyPolicy = PrivacyPolicy::first() ?? new PrivacyPolicy([
            'title' => 'Privacy Policy',
            'title_ar' => 'سياسة الخصوصية',
            'is_active' => true,
        ]);
        return view('admin.settings.privacy-policy.index', compact('privacyPolicy'));
    }

    /**
     * Update the privacy policy.
     */
    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'required|string',
            'is_active' => 'boolean',
        ]);

        try {
            $privacyPolicy = PrivacyPolicy::first() ?? new PrivacyPolicy();

            $privacyPolicy->fill([
                'title' => $request->title,
                'title_ar' => $request->title_ar,
                'description' => $request->description,
                'description_ar' => $request->description_ar,
                'is_active' => $request->input('is_active', '0') === '1',
            ]);
            $privacyPolicy->save();

            return redirect()->route('admin.settings.privacy-policy.index')
                ->with('success', 'Privacy Policy updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Privacy Policy: ' . $e->getMessage())
                ->withInput();
        }
    }
}
