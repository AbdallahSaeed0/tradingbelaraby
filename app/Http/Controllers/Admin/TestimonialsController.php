<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialsController extends Controller
{
    /**
     * Display a listing of testimonials.
     */
    public function index(Request $request)
    {
        $query = Testimonial::query();

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('company', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // Sort
        $sort = $request->get('sort', 'order');
        $direction = $request->get('direction', 'asc');
        $query->orderBy($sort, $direction);

        $testimonials = $query->paginate(15);

        return view('admin.settings.testimonials.index', compact('testimonials'));
    }

    /**
     * Store a newly created testimonial.
     */
    public function store(Request $request)
    {
        // Custom validation: content or voice is required (at least one)
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'position_ar' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'company_ar' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'content_ar' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'voice' => 'nullable|file|mimes:mp3,wav,m4a,ogg,aac,webm|max:51200',
            'voice_url' => 'nullable|url',
            'rating' => 'required|integer|min:1|max:5',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
        ], [
            'content.required_without' => 'Either content or voice recording is required.',
            'voice.required_without' => 'Either content or voice recording is required.',
        ]);

        // Validate that at least content or voice is provided
        $hasContent = !empty(trim($request->content ?? '')) || !empty(trim($request->content_ar ?? ''));
        $hasVoice = $request->hasFile('voice') || !empty(trim($request->voice_url ?? ''));
        
        if (!$hasContent && !$hasVoice) {
            return response()->json([
                'success' => false,
                'message' => __('Either content or voice recording (file or Google Drive link) is required.'),
                'errors' => ['content' => [__('Either content or voice recording (file or Google Drive link) is required.')]]
            ], 422);
        }

        // If both voice file and voice_url are provided, prioritize file upload
        if ($request->hasFile('voice') && $request->filled('voice_url')) {
            // Clear voice_url if file is uploaded
            $request->merge(['voice_url' => null]);
        }

        try {
            $data = $request->except(['avatar', 'voice', 'voice_url']);
            $data['is_active'] = $request->has('is_active') ? true : false;

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatarPath = $request->file('avatar')->store('testimonials', 'public');
                $data['avatar'] = $avatarPath;
            }

            // Handle voice upload (file)
            if ($request->hasFile('voice')) {
                $voicePath = $request->file('voice')->store('testimonials/voices', 'public');
                $data['voice'] = $voicePath;
            }

            // Handle voice URL (Google Drive link)
            if ($request->filled('voice_url')) {
                $data['voice_url'] = $request->voice_url;
            }

            $testimonial = Testimonial::create($data);

            return response()->json([
                'success' => true,
                'message' => __('Testimonial created successfully'),
                'testimonial' => $testimonial
            ]);
        } catch (\Exception $e) {
            \Log::error('Testimonial creation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while creating the testimonial: ') . $e->getMessage(),
                'errors' => ['general' => [$e->getMessage()]]
            ], 500);
        }
    }

    /**
     * Update the specified testimonial.
     */
    public function update(Request $request, Testimonial $testimonial)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'position_ar' => 'nullable|string|max:255',
            'company' => 'nullable|string|max:255',
            'company_ar' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'content_ar' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'voice' => 'nullable|file|mimes:mp3,wav,m4a,ogg,aac,webm|max:51200',
            'voice_url' => 'nullable|url',
            'rating' => 'required|integer|min:1|max:5',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'nullable',
            'remove_voice' => 'nullable|boolean',
        ]);

        // Validate that at least content or voice is provided (unless removing voice and keeping existing content)
        $willHaveContent = !empty(trim($request->content ?? '')) || !empty(trim($request->content_ar ?? '')) || (!empty(trim($testimonial->content ?? '')) || !empty(trim($testimonial->content_ar ?? '')));
        $willHaveVoice = $request->hasFile('voice') || !empty(trim($request->voice_url ?? '')) || ($testimonial->voice && !$request->has('remove_voice')) || ($testimonial->voice_url && !$request->has('remove_voice'));

        if (!$willHaveContent && !$willHaveVoice) {
            return response()->json([
                'success' => false,
                'message' => __('Either content or voice recording is required.'),
                'errors' => ['content' => [__('Either content or voice recording is required.')]]
            ], 422);
        }

        $data = $request->except(['avatar', 'voice', 'voice_url', 'remove_voice']);
        $data['is_active'] = $request->has('is_active') ? true : false;

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($testimonial->avatar) {
                Storage::disk('public')->delete($testimonial->avatar);
            }

            $avatarPath = $request->file('avatar')->store('testimonials', 'public');
            $data['avatar'] = $avatarPath;
        }

        // Handle voice upload (file)
        if ($request->hasFile('voice')) {
            // Delete old voice file
            if ($testimonial->voice) {
                Storage::disk('public')->delete($testimonial->voice);
            }
            // Also clear voice_url if uploading a new file
            $data['voice_url'] = null;

            $voicePath = $request->file('voice')->store('testimonials/voices', 'public');
            $data['voice'] = $voicePath;
        }

        // Handle voice URL (Google Drive link)
        if ($request->filled('voice_url')) {
            // Delete old voice file if switching to URL
            if ($testimonial->voice) {
                Storage::disk('public')->delete($testimonial->voice);
            }
            $data['voice'] = null;
            $data['voice_url'] = $request->voice_url;
        }

        // Handle voice removal
        if ($request->has('remove_voice') && $request->remove_voice) {
            if ($testimonial->voice) {
                Storage::disk('public')->delete($testimonial->voice);
            }
            $data['voice'] = null;
            $data['voice_url'] = null;
        }

        $testimonial->update($data);

        return response()->json([
            'success' => true,
            'message' => __('Testimonial updated successfully'),
        ]);
    }

    /**
     * Remove the specified testimonial.
     */
    public function destroy(Testimonial $testimonial)
    {
        // Delete avatar file
        if ($testimonial->avatar) {
            Storage::disk('public')->delete($testimonial->avatar);
        }

        // Delete voice file
        if ($testimonial->voice) {
            Storage::disk('public')->delete($testimonial->voice);
        }

        $testimonial->delete();

        return response()->json([
            'success' => true,
            'message' => __('Testimonial deleted successfully'),
        ]);
    }

    /**
     * Toggle testimonial status.
     */
    public function toggleStatus(Testimonial $testimonial)
    {
        $testimonial->update(['is_active' => !$testimonial->is_active]);

        return response()->json([
            'success' => true,
            'message' => __('Testimonial status updated successfully'),
            'is_active' => $testimonial->is_active,
        ]);
    }

    /**
     * Update testimonials order.
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'testimonials' => 'required|array',
            'testimonials.*.id' => 'required|exists:testimonials,id',
            'testimonials.*.order' => 'required|integer|min:0',
        ]);

        foreach ($request->testimonials as $item) {
            Testimonial::where('id', $item['id'])->update(['order' => $item['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => __('Testimonials order updated successfully'),
        ]);
    }

    /**
     * Bulk action on testimonials.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'testimonials' => 'required|array',
            'testimonials.*' => 'exists:testimonials,id',
        ]);

        $testimonials = Testimonial::whereIn('id', $request->testimonials);

        switch ($request->action) {
            case 'activate':
                $testimonials->update(['is_active' => true]);
                $message = __('Testimonials activated successfully');
                break;
            case 'deactivate':
                $testimonials->update(['is_active' => false]);
                $message = __('Testimonials deactivated successfully');
                break;
            case 'delete':
                // Delete avatar and voice files
                $testimonials->get()->each(function($testimonial) {
                    if ($testimonial->avatar) {
                        Storage::disk('public')->delete($testimonial->avatar);
                    }
                    if ($testimonial->voice) {
                        Storage::disk('public')->delete($testimonial->voice);
                    }
                });
                $testimonials->delete();
                $message = __('Testimonials deleted successfully');
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }
}
