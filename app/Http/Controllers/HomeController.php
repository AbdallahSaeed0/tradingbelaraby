<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Bundle;
use App\Models\CourseCategory;
use App\Models\Slider;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Fetch active sliders ordered by their order field
        $sliders = Slider::active()->ordered()->get();

        // Fetch featured courses with their relationships
        $featuredCourses = Course::featured()
            ->published()
            ->with(['category', 'instructor', 'instructors', 'ratings'])
            ->withCount(['enrollments', 'ratings'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Fetch latest courses
        $latestCourses = Course::published()
            ->with(['category', 'instructor', 'instructors', 'ratings'])
            ->withCount(['enrollments', 'ratings'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Fetch popular courses (by enrollment count)
        $popularCourses = Course::published()
            ->with(['category', 'instructor', 'instructors', 'ratings'])
            ->withCount(['enrollments', 'ratings'])
            ->orderBy('enrolled_students', 'desc')
            ->limit(6)
            ->get();

        // Fetch Top Discounted courses
        $topDiscountedCourses = Course::topDiscounted()
            ->published()
            ->with(['category', 'instructor', 'instructors', 'ratings'])
            ->withCount(['enrollments', 'ratings'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Fetch Subscription Bundles courses
        $subscriptionBundlesCourses = Course::subscriptionBundles()
            ->published()
            ->with(['category', 'instructor', 'instructors', 'ratings'])
            ->withCount(['enrollments', 'ratings'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Fetch published bundles for Subscription Bundles section
        $subscriptionBundles = Bundle::published()
            ->with('courses')
            ->withCount('courses')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Fetch Live Meeting courses
        $liveMeetingCourses = Course::liveMeeting()
            ->published()
            ->with(['category', 'instructor', 'instructors', 'ratings'])
            ->withCount(['enrollments', 'ratings'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Fetch Recent Courses
        $recentCoursesSection = Course::recentCourses()
            ->published()
            ->with(['category', 'instructor', 'instructors', 'ratings'])
            ->withCount(['enrollments', 'ratings'])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Fetch featured categories
        $featuredCategories = CourseCategory::featured()
            ->withCount('courses')
            ->with(['courses' => function($query) {
                $query->published()->limit(3);
            }])
            ->orderBy('courses_count', 'desc')
            ->limit(6)
            ->get();

        // Fetch all categories for navigation
        $allCategories = CourseCategory::active()
            ->withCount('courses')
            ->orderBy('name')
            ->get();

        return view('pages.home', compact(
            'sliders',
            'featuredCourses',
            'latestCourses',
            'popularCourses',
            'topDiscountedCourses',
            'subscriptionBundlesCourses',
            'subscriptionBundles',
            'liveMeetingCourses',
            'recentCoursesSection',
            'featuredCategories',
            'allCategories'
        ));
    }
}
