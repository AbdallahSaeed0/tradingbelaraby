<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsConditions;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TermsConditionsController extends Controller
{
    /**
     * Display the terms and conditions management page.
     */
    public function index()
    {
        $termsConditions = TermsConditions::first() ?? new TermsConditions();
        return view('admin.settings.terms-conditions.index', compact('termsConditions'));
    }

    /**
     * Update the terms and conditions.
     */
    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'required|string',
            'slug' => 'required|string|max:255|unique:terms_conditions,slug,' . ($request->id ?? 'NULL'),
            'is_active' => 'boolean',
        ]);

        try {
            // Get existing record or create new one
            $termsConditions = TermsConditions::first();
            if (!$termsConditions) {
                $termsConditions = new TermsConditions();
            }

            $data = [
                'title' => $request->title,
                'title_ar' => $request->title_ar,
                'description' => $request->description,
                'description_ar' => $request->description_ar,
                'slug' => $request->slug,
                'is_active' => $request->input('is_active', '0') === '1' || $request->has('is_active'),
            ];

            $termsConditions->fill($data);
            $termsConditions->save();

            return redirect()->route('admin.settings.terms-conditions.index')
                ->with('success', 'Terms and Conditions updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Terms and Conditions: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate slug from title
     */
    public function generateSlug(Request $request)
    {
        $title = $request->input('title', '');
        $slug = Str::slug($title);

        return response()->json(['slug' => $slug]);
    }
}

