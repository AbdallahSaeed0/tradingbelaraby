<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogCategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $per = $request->per_page ?? 15;

        $categories = BlogCategory::when($search, function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->withCount(['blogs', 'publishedBlogs'])
            ->ordered()
            ->paginate($per)
            ->withQueryString();

        return view('admin.blog-categories.index', compact('categories', 'search', 'per'));
    }

    public function data(Request $request)
    {
        $search = $request->q;
        $per = $request->per_page ?? 15;

        $categories = BlogCategory::when($search, function($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->withCount(['blogs', 'publishedBlogs'])
            ->ordered()
            ->paginate($per)
            ->withQueryString();

        if ($request->ajax()) {
            return view('admin.blog-categories.partials.table', compact('categories'))->render();
        }

        abort(404);
    }

    public function create()
    {
        return view('admin.blog-categories.create', ['category' => new BlogCategory]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean'
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blog-categories', 'public');
        }

        BlogCategory::create($data);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category created successfully');
    }

    public function show(BlogCategory $category)
    {
        $category->load(['blogs' => function($query) {
            $query->latest()->paginate(10);
        }]);

        return view('admin.blog-categories.show', compact('category'));
    }

    public function edit(BlogCategory $category)
    {
        return view('admin.blog-categories.edit', compact('category'));
    }

    public function update(Request $request, BlogCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,' . $category->id,
            'name_ar' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive',
            'order' => 'nullable|integer|min:0',
            'is_featured' => 'boolean'
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('image')) {
            // Delete old image
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('blog-categories', 'public');
        }

        $category->update($data);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category updated successfully');
    }

    public function destroy(BlogCategory $category)
    {
        // Check if category has blogs
        if ($category->hasBlogs()) {
            return back()->with('error', 'Cannot delete category that has blogs. Please move or delete the blogs first.');
        }

        // Delete image
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();

        return back()->with('success', 'Blog category deleted successfully');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:blog_categories,id'
        ]);

        $categories = BlogCategory::whereIn('id', $request->categories)->get();
        $deletedCount = 0;
        $errorCount = 0;

        foreach ($categories as $category) {
            if ($category->hasBlogs()) {
                $errorCount++;
                continue;
            }

            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();
            $deletedCount++;
        }

        $message = "Successfully deleted {$deletedCount} categories.";
        if ($errorCount > 0) {
            $message .= " {$errorCount} categories could not be deleted because they contain blogs.";
        }

        return back()->with('success', $message);
    }

    public function toggleStatus(BlogCategory $category)
    {
        $category->update([
            'status' => $category->status === 'active' ? 'inactive' : 'active'
        ]);

        return back()->with('success', 'Category status updated successfully');
    }

    public function analytics()
    {
        $totalCategories = BlogCategory::count();
        $activeCategories = BlogCategory::active()->count();
        $categoriesWithBlogs = BlogCategory::has('blogs')->count();
        $topCategories = BlogCategory::withCount('blogs')
            ->orderBy('blogs_count', 'desc')
            ->limit(5)
            ->get();

        $recentCategories = BlogCategory::latest()->limit(5)->get();

        return view('admin.blog-categories.analytics', compact(
            'totalCategories',
            'activeCategories',
            'categoriesWithBlogs',
            'topCategories',
            'recentCategories'
        ));
    }
}
