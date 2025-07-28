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
        $coursesQuery = \App\Models\Course::with(['category', 'instructor'])
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
}
