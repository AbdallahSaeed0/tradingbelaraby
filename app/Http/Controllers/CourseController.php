<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::published()
            ->with(['category', 'instructor', 'ratings'])
            ->withCount(['enrollments', 'ratings']);

        // Category filter
        if ($request->filled('categories')) {
            $query->whereIn('category_id', $request->categories);
        }

        // Price filter
        if ($request->filled('price')) {
            if ($request->price === 'free') {
                $query->where('is_free', true);
            } elseif ($request->price === 'paid') {
                $query->where('is_free', false);
            }
        }

        // Level filter - removed as level field doesn't exist in courses table
        // if ($request->filled('level')) {
        //     $query->where('level', $request->level);
        // }

        // Rating filter
        if ($request->filled('rating')) {
            $query->where('average_rating', '>=', $request->rating);
        }

        // Sorting
        switch ($request->get('sort', 'latest')) {
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
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $courses = $query->paginate(12)->appends($request->query());

        $categories = CourseCategory::active()
            ->withCount('courses')
            ->orderBy('name')
            ->get();

        return view('courses.index', compact('courses', 'categories'));
    }

    public function show(Course $course)
    {
        $course->load(['instructor', 'category', 'sections.lectures', 'ratings.user', 'publishedHomework']);
        $relatedCourses = Course::where('category_id', $course->category_id)
            ->where('id', '!=', $course->id)
            ->take(4)->get();
        $totalStudents = $course->enrollments()->count();
        $averageRating = $course->ratings()->avg('rating');
        $totalRatings = $course->ratings()->count();
        $isEnrolled = Auth::check() ? Auth::user()->enrollments()->where('course_id', $course->id)->exists() : false;
        $userEnrollment = Auth::check() ? Auth::user()->enrollments()->where('course_id', $course->id)->first() : null;
        return view('courses.detail', compact(
            'course', 'relatedCourses', 'totalStudents', 'averageRating', 'totalRatings', 'isEnrolled', 'userEnrollment'
        ));
    }

    public function search(Request $request)
    {
        $query = Course::query();
        $searchQuery = $request->get('q', '');

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        // Apply category filter if provided
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Apply sorting
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'popular':
                $query->withCount('enrollments')->orderByDesc('enrollments_count');
                break;
            case 'price_low':
                $query->orderBy('price');
                break;
            case 'price_high':
                $query->orderByDesc('price');
                break;
            case 'newest':
            default:
                $query->orderByDesc('created_at');
                break;
        }

        $courses = $query->paginate(12);

        // Get categories for the sidebar filter
        $categories = CourseCategory::active()
            ->withCount(['courses' => function($query) use ($searchQuery) {
                if ($searchQuery) {
                    $query->where('name', 'like', '%' . $searchQuery . '%');
                }
            }])
            ->orderBy('name')
            ->get();

        return view('courses.search', compact('courses', 'searchQuery', 'categories'));
    }

    public function category(CourseCategory $category)
    {
        $courses = $category->courses()->paginate(12);
        return view('pages.category-show', compact('category', 'courses'));
    }
}
