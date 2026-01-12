<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BlogResource;
use App\Http\Resources\BlogCategoryResource;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BlogController extends Controller
{
    /**
     * Display a listing of blogs.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->input('per_page', 15);
        $perPage = min($perPage, 50);

        $query = Blog::where('status', 'published')
            ->with(['category', 'author']);

        // Filter by category
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
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Featured blogs
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        $blogs = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => BlogResource::collection($blogs->items()),
            'meta' => [
                'current_page' => $blogs->currentPage(),
                'last_page' => $blogs->lastPage(),
                'per_page' => $blogs->perPage(),
                'total' => $blogs->total(),
            ],
        ]);
    }

    /**
     * Display the specified blog.
     */
    public function show(string $id): JsonResponse
    {
        $blog = Blog::where('status', 'published')
            ->with(['category', 'author'])
            ->findOrFail($id);

        // Increment view count
        $blog->incrementViews();

        // Load related blogs
        $relatedBlogs = Blog::where('status', 'published')
            ->where('id', '!=', $blog->id)
            ->where(function($query) use ($blog) {
                if ($blog->category_id) {
                    $query->where('category_id', $blog->category_id);
                }
            })
            ->with(['category', 'author'])
            ->limit(4)
            ->get();

        return response()->json([
            'success' => true,
            'data' => new BlogResource($blog),
            'related' => BlogResource::collection($relatedBlogs),
        ]);
    }

    /**
     * Get blog categories.
     */
    public function categories(Request $request): JsonResponse
    {
        $categories = BlogCategory::active()
            ->withCount(['blogs' => function($query) {
                $query->where('status', 'published');
            }])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => BlogCategoryResource::collection($categories),
        ]);
    }
}
