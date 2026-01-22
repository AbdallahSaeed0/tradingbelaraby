<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademyPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AcademyPolicyController extends Controller
{
    /**
     * Display the academy policy management page.
     */
    public function index()
    {
        $academyPolicy = AcademyPolicy::first() ?? new AcademyPolicy();
        return view('admin.settings.academy-policy.index', compact('academyPolicy'));
    }

    /**
     * Update the academy policy.
     */
    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'required|string',
            'slug' => 'required|string|max:255|unique:academy_policies,slug,' . ($request->id ?? 'NULL'),
            'is_active' => 'boolean',
        ]);

        try {
            // Get existing record or create new one
            $academyPolicy = AcademyPolicy::first();
            if (!$academyPolicy) {
                $academyPolicy = new AcademyPolicy();
            }

            $data = [
                'title' => $request->title,
                'title_ar' => $request->title_ar,
                'description' => $request->description,
                'description_ar' => $request->description_ar,
                'slug' => $request->slug,
                'is_active' => $request->input('is_active', '0') === '1' || $request->has('is_active'),
            ];

            $academyPolicy->fill($data);
            $academyPolicy->save();

            return redirect()->route('admin.settings.academy-policy.index')
                ->with('success', 'Academy Policy updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating Academy Policy: ' . $e->getMessage())
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
