<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BundlesController extends Controller
{
    public function index(Request $request)
    {
        $query = Bundle::with('courses')->withCount('courses');

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Pagination
        $perPage = (int) $request->get('per_page', 10);
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $bundles = $query->latest()->paginate($perPage);

        return view('admin.bundles.index', compact('bundles'));
    }

    public function create()
    {
        $courses = Course::published()->orderBy('name')->get();
        return view('admin.bundles.create', compact('courses'));
    }

    public function store(Request $request)
    {
        try {
            // Handle is_featured before validation (checkboxes don't send value when unchecked)
            $request->merge(['is_featured' => $request->has('is_featured') ? 1 : 0]);
            
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'name_ar' => 'nullable|string|max:255',
                'description' => 'required|string',
                'description_ar' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'original_price' => 'nullable|numeric|min:0',
                'status' => 'required|in:draft,published,archived',
                'is_featured' => 'required|boolean',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,svg|max:2048',
                'course_ids' => 'required|array|min:2',
                'course_ids.*' => 'required|exists:courses,id',
            ], [
                'course_ids.required' => 'Please select at least 2 courses.',
                'course_ids.min' => 'Please select at least 2 courses.',
                'image.max' => 'The image must not be greater than 2048 kilobytes.',
            ]);

            // Generate slug
            $data['slug'] = Str::slug($data['name']);
            
            // Ensure unique slug
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Bundle::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('bundles', 'public');
            }

            // Set is_featured (already handled in merge above, but ensure boolean)
            $data['is_featured'] = (bool) $data['is_featured'];

            // Create bundle
            $bundle = Bundle::create($data);

            // Attach courses
            if ($request->has('course_ids') && is_array($request->course_ids)) {
                $bundle->courses()->attach($request->course_ids);
            }

            return redirect()->route('admin.bundles.index')
                ->with('success', 'Bundle created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Bundle $bundle)
    {
        $bundle->load('courses');
        
        return view('admin.bundles.show', compact('bundle'));
    }

    public function edit(Bundle $bundle)
    {
        $bundle->load('courses');
        $courses = Course::published()->orderBy('name')->get();
        
        return view('admin.bundles.edit', compact('bundle', 'courses'));
    }

    public function update(Request $request, Bundle $bundle)
    {
        // Handle is_featured before validation (checkboxes don't send value when unchecked)
        $request->merge(['is_featured' => $request->has('is_featured') ? 1 : 0]);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'required|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp,svg|max:2048',
            'course_ids' => 'required|array|min:2',
            'course_ids.*' => 'required|exists:courses,id',
            'remove_image' => 'nullable|boolean',
        ], [
            'course_ids.required' => 'Please select at least 2 courses.',
            'course_ids.min' => 'Please select at least 2 courses.',
            'image.max' => 'The image must not be greater than 2048 kilobytes.',
        ]);

        try {
            // Update slug if name changed
            if ($bundle->name !== $data['name']) {
                $data['slug'] = Str::slug($data['name']);
                
                // Ensure unique slug
                $originalSlug = $data['slug'];
                $counter = 1;
                while (Bundle::where('slug', $data['slug'])->where('id', '!=', $bundle->id)->exists()) {
                    $data['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            // Handle image removal
            if ($request->has('remove_image') && $request->remove_image) {
                if ($bundle->image) {
                    Storage::disk('public')->delete($bundle->image);
                    $data['image'] = null;
                }
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($bundle->image) {
                    Storage::disk('public')->delete($bundle->image);
                }
                $data['image'] = $request->file('image')->store('bundles', 'public');
            }

            // Set is_featured (already handled in merge above, but ensure boolean)
            $data['is_featured'] = (bool) $data['is_featured'];

            // Update bundle
            $bundle->update($data);

            // Sync courses
            if ($request->has('course_ids') && is_array($request->course_ids)) {
                $bundle->courses()->sync($request->course_ids);
            }

            return redirect()->route('admin.bundles.index')
                ->with('success', 'Bundle updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'An error occurred: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(Bundle $bundle)
    {
        // Delete image if exists
        if ($bundle->image) {
            Storage::disk('public')->delete($bundle->image);
        }

        $bundle->delete();

        return redirect()->route('admin.bundles.index')
            ->with('success', 'Bundle deleted successfully.');
    }
}

