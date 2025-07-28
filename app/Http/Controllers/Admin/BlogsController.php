<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $category = $request->category;
        $status = $request->status;
        $per = $request->per_page ?? 15;

        $blogs = Blog::when($search, function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%");
            })
            ->when($category, function($query) use ($category) {
                $query->where('category_id', $category);
            })
            ->when($status, function($query) use ($status) {
                $query->where('status', $status);
            })
            ->with('category')
            ->latest()
            ->paginate($per)
            ->withQueryString();

        $categories = BlogCategory::active()->ordered()->get();

        return view('admin.blogs.index', compact('blogs', 'search', 'per', 'categories', 'category', 'status'));
    }

    public function data(Request $request)
    {
        $search = $request->q;
        $category = $request->category;
        $status = $request->status;
        $per = $request->per_page ?? 15;

        $blogs = Blog::when($search, function($query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('author', 'like', "%{$search}%");
            })
            ->when($category, function($query) use ($category) {
                $query->where('category_id', $category);
            })
            ->when($status, function($query) use ($status) {
                $query->where('status', $status);
            })
            ->with('category')
            ->latest()
            ->paginate($per)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.blogs.partials.table', compact('blogs'))->render();
        }

        abort(404);
    }

    public function create()
    {
        $categories = BlogCategory::active()->ordered()->get();
        return view('admin.blogs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'excerpt' => 'nullable|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'author' => 'nullable|string|max:255',
            'is_featured' => 'boolean'
        ]);

        $data['slug'] = Str::slug($data['title']);
        $data['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog created successfully');
    }

    public function show(Blog $blog)
    {
        $blog->load('category');
        return view('admin.blogs.show', compact('blog'));
    }

    public function edit(Blog $blog)
    {
        $categories = BlogCategory::active()->ordered()->get();
        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'excerpt' => 'nullable|string',
            'category_id' => 'nullable|exists:blog_categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published,archived',
            'author' => 'nullable|string|max:255',
            'is_featured' => 'boolean'
        ]);

        $data['slug'] = Str::slug($data['title']);
        $data['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('image')) {
            // Delete old image
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('success', 'Blog updated successfully');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }

        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('success', 'Blog deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'blogs' => 'required|array',
            'blogs.*' => 'exists:blogs,id'
        ]);

        $blogs = Blog::whereIn('id', $request->blogs)->get();
        $deletedCount = 0;

        foreach ($blogs as $blog) {
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $blog->delete();
            $deletedCount++;
        }

        return response()->json([
            'success' => true,
            'message' => "Successfully deleted {$deletedCount} blogs"
        ]);
    }

    public function toggleStatus(Blog $blog)
    {
        $newStatus = $blog->status === 'published' ? 'draft' : 'published';
        $blog->update(['status' => $newStatus]);

        return back()->with('success', 'Blog status updated successfully');
    }

    public function toggleFeatured(Blog $blog)
    {
        $blog->update(['is_featured' => !$blog->is_featured]);

        return back()->with('success', 'Blog featured status updated successfully');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'blogs' => 'required|array',
            'blogs.*' => 'exists:blogs,id',
            'status' => 'required|in:draft,published,archived'
        ]);

        $count = Blog::whereIn('id', $request->blogs)
            ->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => "Updated status for {$count} blogs."
        ]);
    }

    public function bulkToggleFeatured(Request $request)
    {
        $request->validate([
            'blogs' => 'required|array',
            'blogs.*' => 'exists:blogs,id',
        ]);

        $blogs = Blog::whereIn('id', $request->blogs)->get();
        foreach ($blogs as $blog) {
            $blog->is_featured = !$blog->is_featured;
            $blog->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Toggled featured status for selected blogs.'
        ]);
    }

    public function analytics()
    {
        $totalBlogs = Blog::count();
        $publishedBlogs = Blog::published()->count();
        $draftBlogs = Blog::where('status', 'draft')->count();
        $featuredBlogs = Blog::featured()->count();

        $topCategories = BlogCategory::withCount('blogs')
            ->orderBy('blogs_count', 'desc')
            ->limit(5)
            ->get();

        $recentBlogs = Blog::with('category')
            ->latest()
            ->limit(5)
            ->get();

        $blogsByStatus = [
            'published' => Blog::published()->count(),
            'draft' => Blog::where('status', 'draft')->count(),
            'archived' => Blog::where('status', 'archived')->count(),
        ];

        return view('admin.blogs.analytics', compact(
            'totalBlogs',
            'publishedBlogs',
            'draftBlogs',
            'featuredBlogs',
            'topCategories',
            'recentBlogs',
            'blogsByStatus'
        ));
    }
}
