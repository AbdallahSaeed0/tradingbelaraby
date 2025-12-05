<?php

namespace App\Http\Controllers;

use App\Models\Bundle;
use Illuminate\Http\Request;

class BundlesController extends Controller
{
    /**
     * Display a listing of bundles
     */
    public function index(Request $request)
    {
        $query = Bundle::with('courses')->published();

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Apply featured filter
        if ($request->filled('featured')) {
            $query->where('is_featured', true);
        }

        $bundles = $query->latest()->paginate(12);

        return view('bundles.index', compact('bundles'));
    }

    /**
     * Display the specified bundle
     */
    public function show(Bundle $bundle)
    {
        // Check if bundle is published
        if ($bundle->status !== 'published') {
            abort(404);
        }

        $bundle->load('courses.category', 'courses.instructor');

        // Get related bundles
        $relatedBundles = Bundle::published()
            ->where('id', '!=', $bundle->id)
            ->inRandomOrder()
            ->limit(3)
            ->get();

        return view('bundles.show', compact('bundle', 'relatedBundles'));
    }
}

