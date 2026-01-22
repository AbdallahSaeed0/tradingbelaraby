<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
        /**
     * Display the categories page.
     *
     * @return \Illuminate\View\View
     */
    public function categories(Request $request)
    {
        $categories = \App\Models\CourseCategory::withCount('courses')->get();

        // Get selected category
        $selectedCategory = null;
        if ($request->has('category')) {
            $selectedCategory = \App\Models\CourseCategory::where('slug', $request->category)->first();
        }

        // Get courses based on selection
        $coursesQuery = \App\Models\Course::with(['category', 'instructor', 'instructors'])
            ->published();

        if ($selectedCategory) {
            $coursesQuery->where('category_id', $selectedCategory->id);
        } else {
            // Show featured courses if no category selected
            $coursesQuery->featured();
        }

        $courses = $coursesQuery->latest()->paginate(12);

        return view('pages.categories', compact('categories', 'courses', 'selectedCategory'));
    }

    /**
     * Display the blog page.
     *
     * @return \Illuminate\View\View
     */
    public function blog()
    {
        return view('pages.blog');
    }

    /**
     * Display the contact page.
     *
     * @return \Illuminate\View\View
     */
    public function contact()
    {
        return view('pages.contact');
    }



    /**
     * Display the course content page.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function courseContent($id)
    {
        return view('courses.content', compact('id'));
    }

    /**
     * Display the quiz page.
     *
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function quiz($id)
    {
        return view('courses.quiz', compact('id'));
    }

    /**
     * Display the terms and conditions page.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function termsConditions($slug)
    {
        $termsConditions = \App\Models\TermsConditions::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$termsConditions) {
            abort(404);
        }

        return view('pages.terms-conditions', compact('termsConditions'));
    }

    /**
     * Display the about us page.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function aboutUs($slug)
    {
        $aboutUs = \App\Models\AboutUs::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$aboutUs) {
            abort(404);
        }

        return view('pages.about-us', compact('aboutUs'));
    }

    /**
     * Display the academy policy page.
     *
     * @param string $slug
     * @return \Illuminate\View\View
     */
    public function academyPolicy($slug)
    {
        $academyPolicy = \App\Models\AcademyPolicy::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$academyPolicy) {
            abort(404);
        }

        return view('pages.academy-policy', compact('academyPolicy'));
    }
}
