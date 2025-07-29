<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LanguageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $languages = Language::orderBy('is_default', 'desc')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.languages.index', compact('languages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.languages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'code' => 'required|string|max:5|unique:languages,code',
            'direction' => 'required|in:ltr,rtl',
            'is_active' => 'boolean',
            'is_default' => 'boolean'
        ]);

        DB::transaction(function () use ($request) {
            // If this is set as default, remove default from other languages
            if ($request->boolean('is_default')) {
                Language::where('is_default', true)->update(['is_default' => false]);
            }

            Language::create($request->all());
        });

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Language $language)
    {
        return view('admin.languages.show', compact('language'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Language $language)
    {
        return view('admin.languages.edit', compact('language'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Language $language)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'native_name' => 'required|string|max:255',
            'code' => 'required|string|max:5|unique:languages,code,' . $language->id,
            'direction' => 'required|in:ltr,rtl',
            'is_active' => 'boolean',
            'is_default' => 'boolean'
        ]);

        DB::transaction(function () use ($request, $language) {
            // If this is set as default, remove default from other languages
            if ($request->boolean('is_default')) {
                Language::where('id', '!=', $language->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }

            $language->update($request->all());
        });

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Language $language)
    {
        // Don't allow deletion of default language
        if ($language->is_default) {
            return redirect()->route('admin.languages.index')
                ->with('error', 'Cannot delete the default language.');
        }

        $language->delete();

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language deleted successfully.');
    }

    /**
     * Toggle language active status
     */
    public function toggleActive(Language $language)
    {
        // Don't allow deactivating default language
        if ($language->is_default) {
            return redirect()->route('admin.languages.index')
                ->with('error', 'Cannot deactivate the default language.');
        }

        $language->update(['is_active' => !$language->is_active]);

        return redirect()->route('admin.languages.index')
            ->with('success', 'Language status updated successfully.');
    }

    /**
     * Set language as default
     */
    public function setDefault(Language $language)
    {
        DB::transaction(function () use ($language) {
            Language::where('is_default', true)->update(['is_default' => false]);
            $language->update(['is_default' => true]);
        });

        return redirect()->route('admin.languages.index')
            ->with('success', 'Default language updated successfully.');
    }
}
