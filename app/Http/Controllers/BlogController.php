<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::published()
            ->with(['category', 'author'])
            ->orderBy('created_at', 'desc');

        // Filter by category if provided
        if ($request->filled('category')) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhereHas('author', function($authorQuery) use ($search) {
                      $authorQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $blogs = $query->paginate(9)->withQueryString();
        $categories = BlogCategory::active()->withCount('blogs')->get();

        return view('pages.blog', compact('blogs', 'categories'));
    }

    public function show(Blog $blog)
    {
        // Increment view count
        $blog->incrementViews();

        // Load related blogs
        $relatedBlogs = Blog::published()
            ->where('id', '!=', $blog->id)
            ->where('category_id', $blog->category_id)
            ->with(['category', 'author'])
            ->limit(3)
            ->get();

        // Load categories for sidebar
        $categories = BlogCategory::active()->withCount('blogs')->get();

        return view('pages.blog-single', compact('blog', 'relatedBlogs', 'categories'));
    }
}
