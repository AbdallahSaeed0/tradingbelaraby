<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;

class InstructorController extends Controller
{
    /**
     * Display the specified instructor profile.
     */
    public function show($id)
    {
        $instructor = Admin::whereHas('adminType', function($query) {
            $query->where('name', 'instructor');
        })
        ->where('is_active', true)
        ->findOrFail($id);

        // Load courses from both legacy relationship and many-to-many
        $legacyCourses = $instructor->courses()->where('status', 'published')->with('instructors')->get();
        $assignedCourses = $instructor->coursesAssigned()->where('status', 'published')->with('instructors')->get();

        // Merge and remove duplicates
        $allCourses = $legacyCourses->merge($assignedCourses)->unique('id');
        $instructor->setRelation('courses', $allCourses);

        return view('instructor.show', compact('instructor'));
    }

    /**
     * Display all instructors.
     */
    public function index()
    {
        $instructors = Admin::whereHas('adminType', function($query) {
            $query->where('name', 'instructor');
        })
        ->where('is_active', true)
        ->withCount('courses')
        ->get();

        return view('instructor.index', compact('instructors'));
    }
}
