<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoriesController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->q;
        $perPage = $request->per_page ?? 15;
        $categories = CourseCategory::when($search, fn($q)=>$q->where('name','like',"%{$search}%"))
            ->latest()->paginate($perPage)->withQueryString();
        return view('admin.categories.index', compact('categories','search','perPage'));
    }

    public function data(Request $request)
    {
        $search = $request->q;
        $perPage = $request->per_page ?? 15;
        $categories = CourseCategory::when($search, fn($q)=>$q->where('name','like',"%{$search}%"))
            ->latest()->paginate($perPage)->withQueryString();
        if($request->ajax())
            return view('admin.categories.partials.table', compact('categories'))->render();
        abort(404);
    }

    public function create()
    {
        return view('admin.categories.create', ['category'=>new CourseCategory]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_featured' => 'boolean'
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('course-categories', 'public');
        }

        CourseCategory::create($data);
        return redirect()->route('admin.categories.index')->with('success','Category created successfully');
    }

    public function edit(CourseCategory $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(Request $request, CourseCategory $category)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_featured' => 'boolean'
        ]);

        $data['slug'] = Str::slug($data['name']);
        $data['is_featured'] = $request->has('is_featured');

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($category->image) {
                Storage::disk('public')->delete($category->image);
            }
            $data['image'] = $request->file('image')->store('course-categories', 'public');
        }

        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success','Category updated successfully');
    }

    public function show(CourseCategory $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    public function destroy(CourseCategory $category)
    {
        // Delete image if exists
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }

        $category->delete();
        return back()->with('success','Category deleted successfully');
    }
}
