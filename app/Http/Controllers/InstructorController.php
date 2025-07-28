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
        ->with(['courses' => function($query) {
            $query->where('status', 'published');
        }])
        ->findOrFail($id);

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
