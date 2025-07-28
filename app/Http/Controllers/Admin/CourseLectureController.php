<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseSection;
use App\Models\CourseLecture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseLectureController extends Controller
{
    public function store(Request $request, CourseSection $section)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:video,document,text',
            'duration_minutes' => 'nullable|integer|min:1',
            'video_url' => 'nullable|url',
            'file' => 'nullable|file|max:10240', // 10MB max
            'content_text' => 'nullable|string',
            'order' => 'required|integer|min:1',
        ]);

        // Handle file upload if present
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('lectures', 'public');
            $data['file_path'] = $filePath;
        }

        $lecture = $section->lectures()->create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lecture created successfully',
                'lecture' => $lecture
            ]);
        }

        return redirect()->back()->with('success', 'Lecture created successfully');
    }

    public function update(Request $request, CourseSection $section, CourseLecture $lecture)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:video,document,text',
            'duration_minutes' => 'nullable|integer|min:1',
            'video_url' => 'nullable|url',
            'file' => 'nullable|file|max:10240', // 10MB max
            'content_text' => 'nullable|string',
            'order' => 'required|integer|min:1',
        ]);

        // Handle file upload if present
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($lecture->file_path && Storage::disk('public')->exists($lecture->file_path)) {
                Storage::disk('public')->delete($lecture->file_path);
            }

            $filePath = $request->file('file')->store('lectures', 'public');
            $data['file_path'] = $filePath;
        }

        $lecture->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lecture updated successfully',
                'lecture' => $lecture
            ]);
        }

        return redirect()->back()->with('success', 'Lecture updated successfully');
    }

    public function destroy(CourseSection $section, CourseLecture $lecture)
    {
        // Delete file if exists
        if ($lecture->file_path && Storage::disk('public')->exists($lecture->file_path)) {
            Storage::disk('public')->delete($lecture->file_path);
        }

        $lecture->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Lecture deleted successfully'
            ]);
        }

        return redirect()->back()->with('success', 'Lecture deleted successfully');
    }
}
