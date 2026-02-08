<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\CourseRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    /**
     * Get paginated reviews for a course (same data as website).
     */
    public function getReviews(Request $request, $id)
    {
        $course = Course::published()->findOrFail($id);

        $query = $course->approvedRatings()->with('user');

        $page = max(1, (int) $request->get('page', 1));
        $pageSize = min(50, max(1, (int) $request->get('page_size', 20)));

        $ratings = $query->orderByDesc('created_at')->paginate($pageSize, ['*'], 'page', $page);

        $items = $ratings->getCollection()->map(function ($r) {
            return [
                'id' => (string) $r->id,
                'user_name' => $r->user ? $r->user->name : __('Anonymous'),
                'rating' => (int) $r->rating,
                'review' => $r->review,
                'content_quality' => $r->content_quality,
                'instructor_quality' => $r->instructor_quality,
                'value_for_money' => $r->value_for_money,
                'course_material' => $r->course_material,
                'created_at' => $r->created_at?->toIso8601String(),
            ];
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $ratings->currentPage(),
                'last_page' => $ratings->lastPage(),
                'per_page' => $ratings->perPage(),
                'total' => $ratings->total(),
            ],
        ]);
    }

    /**
     * Submit a course review (enrolled students only, with star ratings).
     */
    public function storeReview(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated.'], 401);
        }

        $course = Course::published()->findOrFail($id);

        if (!$course->isEnrolledBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You must be enrolled in this course to leave a review.',
            ], 403);
        }

        $validated = $request->validate([
            'review' => 'nullable|string|max:2000',
            'rating' => 'required|integer|min:1|max:5',
            'content_quality' => 'nullable|integer|min:1|max:5',
            'instructor_quality' => 'nullable|integer|min:1|max:5',
            'value_for_money' => 'nullable|integer|min:1|max:5',
            'course_material' => 'nullable|integer|min:1|max:5',
        ]);

        $ratings = array_filter([
            $validated['content_quality'] ?? null,
            $validated['instructor_quality'] ?? null,
            $validated['value_for_money'] ?? null,
            $validated['course_material'] ?? null,
        ], fn ($v) => $v !== null && $v > 0);

        $overallRating = empty($ratings)
            ? (int) $validated['rating']
            : (int) round(array_sum($ratings) / count($ratings));
        $overallRating = max(1, min(5, $overallRating));

        $rating = CourseRating::updateOrCreate(
            [
                'course_id' => $course->id,
                'user_id' => $user->id,
            ],
            [
                'rating' => $overallRating,
                'review' => $validated['review'] ?? null,
                'content_quality' => $validated['content_quality'] ?? null,
                'instructor_quality' => $validated['instructor_quality'] ?? null,
                'value_for_money' => $validated['value_for_money'] ?? null,
                'course_material' => $validated['course_material'] ?? null,
                'status' => 'approved',
            ]
        );

        $course->average_rating = $course->ratings()->where('status', 'approved')->avg('rating') ?? 0;
        $course->save();

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your review!',
            'data' => [
                'id' => (string) $rating->id,
                'rating' => (int) $rating->rating,
                'review' => $rating->review,
                'created_at' => $rating->created_at?->toIso8601String(),
            ],
            'average_rating' => (float) $course->average_rating,
            'ratings_count' => $course->approvedRatings()->count(),
        ]);
    }
}
