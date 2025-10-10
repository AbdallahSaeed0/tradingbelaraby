<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display all categories
     */
    public function index()
    {
        $categories = CourseCategory::withCount('courses')
            ->withCount(['courses as instructors_count' => function($query) {
                $query->distinct('instructor_id');
            }])
            ->orderBy('name')
            ->paginate(12);

        $featuredCategories = CourseCategory::withCount('courses')
            ->where('is_featured', true)
            ->orderBy('courses_count', 'desc')
            ->take(4)
            ->get();

        return view('pages.categories-index', compact('categories', 'featuredCategories'));
    }

    /**
     * Display courses for a specific category
     */
    public function show($slug)
    {
        $category = CourseCategory::where('slug', $slug)->firstOrFail();

        $courses = Course::where('category_id', $category->id)
            ->with(['instructor', 'instructors', 'category'])
            ->withCount('enrollments as enrolled_students')
            ->withAvg('ratings', 'rating')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        // Get category statistics
        $instructorsCount = Course::where('category_id', $category->id)
            ->distinct('instructor_id')
            ->count('instructor_id');

        $totalStudents = Course::where('category_id', $category->id)
            ->withCount('enrollments')
            ->get()
            ->sum('enrollments_count');

        $totalDuration = Course::where('category_id', $category->id)
            ->sum('duration');

        return view('pages.category-show', compact(
            'category',
            'courses',
            'instructorsCount',
            'totalStudents',
            'totalDuration'
        ));
    }

    /**
     * Search categories
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        $categories = CourseCategory::where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->withCount('courses')
            ->orderBy('courses_count', 'desc')
            ->paginate(12);

        return view('pages.categories-index', compact('categories'));
    }

    /**
     * Get categories for AJAX requests
     */
    public function getCategories()
    {
        $categories = CourseCategory::withCount('courses')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }
}
