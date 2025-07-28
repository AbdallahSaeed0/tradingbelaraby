<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $admin = auth('admin')->user();

        // Get statistics based on permissions
        if ($admin->hasPermission('view_own_analytics') && !$admin->hasPermission('view_analytics')) {
            // Instructor dashboard - only their own content
            $stats = [
                'total_courses' => \App\Models\Course::where('instructor_id', $admin->id)->count(),
                'total_students' => \App\Models\User::count(), // All students
                'total_revenue' => 0, // Calculate based on instructor's courses
                'recent_activity' => [], // Get instructor's recent activity
            ];
        } else {
            // Admin dashboard - all content
            $stats = [
                'total_courses' => \App\Models\Course::count(),
                'total_students' => \App\Models\User::count(),
                'total_revenue' => 0, // Calculate total revenue
                'recent_activity' => [], // Get all recent activity
            ];
        }

        return view('admin.dashboard', compact('stats'));
    }
}
