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
        $isInstructor = $admin->adminType && $admin->adminType->name === 'instructor';
        $isAdmin = $admin->adminType && $admin->adminType->name === 'admin';

        if ($isInstructor && !$isAdmin) {
            // Instructor dashboard - only their own content
            $stats = [
                'total_admins' => \App\Models\Admin::count(),
                'total_courses' => \App\Models\Course::where('instructor_id', $admin->id)->count(),
                'total_blogs' => \App\Models\Blog::count(),
                'total_users' => \App\Models\User::count(),
                'published_courses' => \App\Models\Course::where('instructor_id', $admin->id)->where('status', 'published')->count(),
                'published_blogs' => \App\Models\Blog::where('status', 'published')->count(),
                'total_instructors' => \App\Models\Admin::whereHas('adminType', function($query) {
                    $query->where('name', 'instructor');
                })->where('is_active', true)->count(),
                'total_traders' => \App\Models\Trader::active()->count(),
            ];

            // Get latest content for instructor
            $latestBlogs = \App\Models\Blog::with(['category', 'author'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            $latestCourses = \App\Models\Course::with(['category', 'instructor'])
                ->where('instructor_id', $admin->id)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            $latestInstructors = \App\Models\Admin::whereHas('adminType', function($query) {
                $query->where('name', 'instructor');
            })->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            $latestTraders = \App\Models\Trader::active()
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

        } else {
            // Admin dashboard - all content
            $stats = [
                'total_admins' => \App\Models\Admin::count(),
                'total_courses' => \App\Models\Course::count(),
                'total_blogs' => \App\Models\Blog::count(),
                'total_users' => \App\Models\User::count(),
                'published_courses' => \App\Models\Course::where('status', 'published')->count(),
                'published_blogs' => \App\Models\Blog::where('status', 'published')->count(),
                'total_instructors' => \App\Models\Admin::whereHas('adminType', function($query) {
                    $query->where('name', 'instructor');
                })->where('is_active', true)->count(),
                'total_traders' => \App\Models\Trader::active()->count(),
            ];

            // Get latest content for admin
            $latestBlogs = \App\Models\Blog::with(['category', 'author'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            $latestCourses = \App\Models\Course::with(['category', 'instructor'])
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            $latestInstructors = \App\Models\Admin::whereHas('adminType', function($query) {
                $query->where('name', 'instructor');
            })->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();

            $latestTraders = \App\Models\Trader::active()
                ->orderBy('created_at', 'desc')
                ->limit(3)
                ->get();
        }

        return view('admin.dashboard', compact('stats', 'latestBlogs', 'latestCourses', 'latestInstructors', 'latestTraders'));
    }
}
