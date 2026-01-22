<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AboutUs;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AboutUsController extends Controller
{
    /**
     * Display the about us management page.
     */
    public function index()
    {
        $aboutUs = AboutUs::first() ?? new AboutUs();
        return view('admin.settings.about-us.index', compact('aboutUs'));
    }

    /**
     * Update the about us.
     */
    public function update(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'required|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'required|string',
            'slug' => 'required|string|max:255|unique:about_us,slug,' . ($request->id ?? 'NULL'),
            'is_active' => 'boolean',
        ]);

        try {
            // Get existing record or create new one
            $aboutUs = AboutUs::first();
            if (!$aboutUs) {
                $aboutUs = new AboutUs();
            }

            $data = [
                'title' => $request->title,
                'title_ar' => $request->title_ar,
                'description' => $request->description,
                'description_ar' => $request->description_ar,
                'slug' => $request->slug,
                'is_active' => $request->input('is_active', '0') === '1' || $request->has('is_active'),
            ];

            $aboutUs->fill($data);
            $aboutUs->save();

            return redirect()->route('admin.settings.about-us.index')
                ->with('success', 'About Us updated successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating About Us: ' . $e->getMessage())
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
