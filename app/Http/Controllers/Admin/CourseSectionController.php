<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Http\Request;

class CourseSectionController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:1',
        ]);

        $section = $course->sections()->create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Section created successfully',
                'section' => $section->load('lectures')
            ]);
        }

        return redirect()->back()->with('success', 'Section created successfully');
    }

    public function update(Request $request, Course $course, CourseSection $section)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:1',
        ]);

        $section->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Section updated successfully',
                'section' => $section->load('lectures')
            ]);
        }

        return redirect()->back()->with('success', 'Section updated successfully');
    }

    public function destroy(Course $course, CourseSection $section)
    {
        $section->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Section deleted successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Section deleted successfully');
    }
}
