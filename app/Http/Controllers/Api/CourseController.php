<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of courses with filters
     */
    public function index(Request $request)
    {
        $query = Course::query()
            ->published()
            ->with(['category', 'instructor', 'instructors']);

        // Category filter
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Instructor filter
        if ($request->filled('instructor_id')) {
            $query->where('instructor_id', $request->instructor_id);
        }

        // Price filter (free/paid)
        if ($request->filled('is_free')) {
            $query->where('is_free', filter_var($request->is_free, FILTER_VALIDATE_BOOLEAN));
        }

        // Featured filter
        if ($request->filled('is_featured')) {
            $query->where('is_featured', filter_var($request->is_featured, FILTER_VALIDATE_BOOLEAN));
        }

        // Rating filter
        if ($request->filled('rating')) {
            $query->where('average_rating', '>=', $request->rating);
        }

        // Search query
        if ($request->filled('search_query') || $request->filled('q')) {
            $searchQuery = $request->input('search_query') ?? $request->input('q');
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('name_ar', 'like', '%' . $searchQuery . '%')
                    ->orWhere('description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('description_ar', 'like', '%' . $searchQuery . '%');
            });
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->orderBy('enrolled_students', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Pagination
        $pageSize = $request->get('page_size', 20);
        $pageSize = min($pageSize, 100); // Max 100 items per page

        $courses = $query->paginate($pageSize);

        return CourseResource::collection($courses);
    }

    /**
     * Display the specified course with full details
     */
    public function show($id)
    {
        $course = Course::published()
            ->with([
                'category',
                'instructor',
                'instructors',
                'sections' => function ($query) {
                    $query->where('is_published', true)
                        ->orderBy('order')
                        ->with(['lectures' => function ($q) {
                            $q->where('is_published', true)->orderBy('order');
                        }]);
                },
                'approvedRatings.user'
            ])
            ->findOrFail($id);

        return response()->json([
            'data' => new CourseResource($course),
        ]);
    }

    /**
     * Get featured courses
     */
    public function featured(Request $request)
    {
        $limit = $request->get('limit', 10);
        $limit = min($limit, 50); // Max 50 items

        $courses = Course::published()
            ->featured()
            ->with(['category', 'instructor', 'instructors'])
            ->orderBy('enrolled_students', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'data' => CourseResource::collection($courses),
        ]);
    }

    /**
     * Search courses
     */
    public function search(Request $request)
    {
        $searchQuery = $request->input('q', '');

        $query = Course::published()
            ->with(['category', 'instructor', 'instructors']);

        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('name_ar', 'like', '%' . $searchQuery . '%')
                    ->orWhere('description', 'like', '%' . $searchQuery . '%')
                    ->orWhere('description_ar', 'like', '%' . $searchQuery . '%');
            });
        }

        // Apply category filter if provided
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular':
                $query->orderBy('enrolled_students', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $pageSize = $request->get('page_size', 20);
        $pageSize = min($pageSize, 100);

        $courses = $query->paginate($pageSize);

        return CourseResource::collection($courses);
    }
}
