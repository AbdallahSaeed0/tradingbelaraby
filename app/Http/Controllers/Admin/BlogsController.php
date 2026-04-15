<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\SendBlogToTelegram;
use App\Models\Blog;
use App\Models\BlogCategory;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
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
            ->with(['category', 'author'])
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
        $authors = Admin::whereHas('adminType', function($query) {
            $query->whereIn('name', ['admin', 'instructor']);
        })->where('is_active', true)->get();

        return view('admin.blogs.create', compact('categories', 'authors'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // English fields
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'excerpt' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Arabic fields
            'title_ar' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'excerpt_ar' => 'nullable|string',
            'image_ar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Other fields
            'category_id' => 'nullable|exists:blog_categories,id',
            'author_id' => 'nullable|exists:admins,id',
            'status' => 'required|in:draft,scheduled,published,archived',
            'publish_at' => 'nullable|date|required_if:status,scheduled',
            'is_featured' => 'boolean',
            'post_to_telegram' => 'boolean',
            'telegram_send_ar' => 'boolean',
            'telegram_send_en' => 'boolean',
            'custom_slug' => 'nullable|string|max:255|unique:blogs,custom_slug',
            'tags' => 'nullable|string',

            // SEO fields
            'meta_title' => 'nullable|string|max:255',
            'meta_title_ar' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_description_ar' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'meta_keywords_ar' => 'nullable|string',
        ]);

        // Generate slugs
        $data['slug'] = Str::slug($data['title']);
        if ($data['title_ar']) {
            $data['slug_ar'] = Str::slug($data['title_ar']);
        }

        $data['is_featured'] = $request->has('is_featured');
        $this->applyTelegramFlags($request, $data);

        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        } elseif ($data['status'] === 'scheduled') {
            $data['published_at'] = null;
        }

        // Process JSON fields - keep as JSON strings for database storage
        if ($request->filled('tags')) {
            $data['tags'] = $request->tags; // Keep as JSON string
        }

        if ($request->filled('meta_keywords')) {
            $data['meta_keywords'] = $request->meta_keywords; // Keep as JSON string
        }

        if ($request->filled('meta_keywords_ar')) {
            $data['meta_keywords_ar'] = $request->meta_keywords_ar; // Keep as JSON string
        }

        // Handle file uploads
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        if ($request->hasFile('image_ar')) {
            $data['image_ar'] = $request->file('image_ar')->store('blogs', 'public');
        }

        try {
            $blog = Blog::create($data);

            if ($blog->status === 'published' && $blog->shouldSendToTelegram()) {
                SendBlogToTelegram::dispatch($blog->id);
            }

            return redirect()->route('admin.blogs.index')->with('success', 'Blog created successfully');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            Log::error('Blog creation failed: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create blog: ' . $e->getMessage());
        }
    }

    public function show(Blog $blog)
    {
        $blog->load(['category', 'author']);
        return view('admin.blogs.show', compact('blog'));
    }

    public function edit(Blog $blog)
    {
        $categories = BlogCategory::active()->ordered()->get();
        $authors = Admin::whereHas('adminType', function($query) {
            $query->whereIn('name', ['admin', 'instructor']);
        })->where('is_active', true)->get();

        return view('admin.blogs.edit', compact('blog', 'categories', 'authors'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            // English fields
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'excerpt' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Arabic fields
            'title_ar' => 'nullable|string|max:255',
            'description_ar' => 'nullable|string',
            'excerpt_ar' => 'nullable|string',
            'image_ar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',

            // Other fields
            'category_id' => 'nullable|exists:blog_categories,id',
            'author_id' => 'nullable|exists:admins,id',
            'status' => 'required|in:draft,scheduled,published,archived',
            'publish_at' => 'nullable|date|required_if:status,scheduled',
            'is_featured' => 'boolean',
            'post_to_telegram' => 'boolean',
            'telegram_send_ar' => 'boolean',
            'telegram_send_en' => 'boolean',
            'custom_slug' => 'nullable|string|max:255|unique:blogs,custom_slug,' . $blog->id,
            'tags' => 'nullable|string',

            // SEO fields
            'meta_title' => 'nullable|string|max:255',
            'meta_title_ar' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_description_ar' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
            'meta_keywords_ar' => 'nullable|string',
        ]);

        // Generate slugs
        $data['slug'] = Str::slug($data['title']);
        if ($data['title_ar']) {
            $data['slug_ar'] = Str::slug($data['title_ar']);
        }

        $data['is_featured'] = $request->has('is_featured');
        $this->applyTelegramFlags($request, $data);

        if ($data['status'] === 'published') {
            if ($blog->status !== 'published' || $blog->published_at === null) {
                $data['published_at'] = now();
            }
        } elseif ($data['status'] === 'scheduled') {
            $data['published_at'] = null;
        }

        // Process JSON fields
        if ($request->filled('tags')) {
            $data['tags'] = $request->tags; // Keep as JSON string
        }

        if ($request->filled('meta_keywords')) {
            $data['meta_keywords'] = $request->meta_keywords; // Keep as JSON string
        }

        if ($request->filled('meta_keywords_ar')) {
            $data['meta_keywords_ar'] = $request->meta_keywords_ar; // Keep as JSON string
        }

        // Handle file uploads
        if ($request->hasFile('image')) {
            // Delete old image
            if ($blog->image) {
                Storage::disk('public')->delete($blog->image);
            }
            $data['image'] = $request->file('image')->store('blogs', 'public');
        }

        if ($request->hasFile('image_ar')) {
            // Delete old Arabic image
            if ($blog->image_ar) {
                Storage::disk('public')->delete($blog->image_ar);
            }
            $data['image_ar'] = $request->file('image_ar')->store('blogs', 'public');
        }

        $wasPublished = $blog->status === 'published';
        $blog->update($data);
        $blog->refresh();

        if ((!$wasPublished && $blog->status === 'published') && $blog->shouldSendToTelegram()) {
            SendBlogToTelegram::dispatch($blog->id);
        }

        return redirect()->route('admin.blogs.index')->with('success', 'Blog updated successfully');
    }

    public function destroy(Blog $blog)
    {
        if ($blog->image) {
            Storage::disk('public')->delete($blog->image);
        }

        if ($blog->image_ar) {
            Storage::disk('public')->delete($blog->image_ar);
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
            if ($blog->image_ar) {
                Storage::disk('public')->delete($blog->image_ar);
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
        $blog->update([
            'status' => $newStatus,
            'published_at' => $newStatus === 'published' ? now() : null,
        ]);

        return back()->with('success', 'Blog status updated successfully');
    }

    public function updateStatus(Request $request, Blog $blog)
    {
        $request->validate([
            'status' => 'required|in:published,draft,scheduled,archived'
        ]);

        $updateData = ['status' => $request->status];
        if ($request->status === 'published') {
            $updateData['published_at'] = now();
        } elseif ($request->status === 'scheduled') {
            $updateData['published_at'] = null;
        }
        $wasPublished = $blog->status === 'published';
        $blog->update($updateData);
        $blog->refresh();

        if ((!$wasPublished && $blog->status === 'published') && $blog->shouldSendToTelegram()) {
            SendBlogToTelegram::dispatch($blog->id);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Blog status updated successfully'
            ]);
        }

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
            'status' => 'required|in:draft,scheduled,published,archived'
        ]);

        $updateData = ['status' => $request->status];
        if ($request->status === 'published') {
            $updateData['published_at'] = now();
        } elseif ($request->status === 'scheduled') {
            $updateData['published_at'] = null;
        }

        $selectedBlogs = Blog::whereIn('id', $request->blogs)->get();
        $count = Blog::whereIn('id', $request->blogs)->update($updateData);

        if ($request->status === 'published') {
            foreach ($selectedBlogs as $blog) {
                if ($blog->status !== 'published' && $blog->shouldSendToTelegram()) {
                    SendBlogToTelegram::dispatch($blog->id);
                }
            }
        }

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
        $scheduledBlogs = Blog::where('status', 'scheduled')->count();
        $featuredBlogs = Blog::featured()->count();
        $totalViews = Blog::sum('views_count');

        // Top performing blogs by views
        $topBlogs = Blog::with('category')
            ->orderBy('views_count', 'desc')
            ->limit(10)
            ->get();

        // Category statistics
        $categoryStats = BlogCategory::withCount('blogs')
            ->orderBy('blogs_count', 'desc')
            ->get()
            ->map(function ($category) {
                return (object) [
                    'category_name' => $category->name,
                    'blog_count' => $category->blogs_count
                ];
            });

        $recentBlogs = Blog::with('category')
            ->latest()
            ->limit(5)
            ->get();

        $blogsByStatus = [
            'published' => Blog::published()->count(),
            'draft' => Blog::where('status', 'draft')->count(),
            'scheduled' => Blog::where('status', 'scheduled')->count(),
            'archived' => Blog::where('status', 'archived')->count(),
        ];

        return view('admin.blogs.analytics', compact(
            'totalBlogs',
            'publishedBlogs',
            'draftBlogs',
            'scheduledBlogs',
            'featuredBlogs',
            'totalViews',
            'topBlogs',
            'categoryStats',
            'recentBlogs',
            'blogsByStatus'
        ));
    }

    /**
     * @param array<string, mixed> $data
     */
    private function applyTelegramFlags(Request $request, array &$data): void
    {
        $data['post_to_telegram'] = $request->boolean('post_to_telegram');
        $data['telegram_send_ar'] = $data['post_to_telegram'] ? $request->boolean('telegram_send_ar') : false;
        $data['telegram_send_en'] = $data['post_to_telegram'] ? $request->boolean('telegram_send_en') : false;
    }
}
