<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseCategoryResource;
use App\Models\CourseCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of course categories
     */
    public function index(Request $request)
    {
        $query = CourseCategory::query();

        // Only active categories (with published courses)
        if ($request->get('active', true)) {
            $query->active();
        }

        // Featured filter
        if ($request->filled('is_featured')) {
            $query->where('is_featured', filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN));
        }

        // With courses count
        $query->withCount(['courses' => function ($q) {
            $q->published();
        }]);

        $query->orderBy('name');

        $categories = $query->get();

        return response()->json([
            'data' => CourseCategoryResource::collection($categories),
        ]);
    }

    /**
     * Display the specified category
     */
    public function show($id)
    {
        $category = CourseCategory::withCount(['courses' => function ($q) {
            $q->published();
        }])->findOrFail($id);

        return new CourseCategoryResource($category);
    }
}
