<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InfoSplit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InfoSplitController extends Controller
{
    public function index()
    {
        $infoSplit = InfoSplit::first();
        return view('admin.settings.info-split.index', compact('infoSplit'));
    }

    public function store(Request $request)
    {
        // Debug: Log the request data
        Log::info('Info Split Store Request:', $request->all());

        $request->validate([
            'title' => 'required|string|max:255',
            'title_ar' => 'nullable|string|max:255',
            'description' => 'required|string',
            'description_ar' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'button_text' => 'required|string|max:255',
            'button_text_ar' => 'nullable|string|max:255',
            'button_url' => 'nullable|url|max:255',
            'is_active' => 'nullable|in:0,1',
        ]);

        $data = $request->except(['image']);
        $data['is_active'] = $request->input('is_active') == '1';

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($request->old_image) {
                Storage::disk('public')->delete($request->old_image);
            }
            $data['image'] = $request->file('image')->store('info-split', 'public');
        }

        InfoSplit::updateOrCreate(['id' => 1], $data);

        return response()->json([
            'success' => true,
            'message' => __('Info Split Section updated successfully!')
        ]);
    }

    public function toggleStatus()
    {
        $content = InfoSplit::first();
        if ($content) {
            $content->update(['is_active' => !$content->is_active]);
            return response()->json([
                'success' => true,
                'message' => $content->is_active ? __('Section activated!') : __('Section deactivated!')
            ]);
        }
        return response()->json(['success' => false, 'message' => __('Content not found!')]);
    }
}
