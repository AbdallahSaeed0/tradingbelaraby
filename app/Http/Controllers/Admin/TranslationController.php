<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Translation;
use App\Helpers\TranslationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TranslationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Translation::with('language');

        // Filter by language
        if ($request->filled('language_id')) {
            $query->where('language_id', $request->language_id);
        }

        // Filter by group
        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        // Filter by key
        if ($request->filled('key')) {
            $query->where('translation_key', 'like', '%' . $request->key . '%');
        }

        $translations = $query->orderBy('language_id')
            ->orderBy('group')
            ->orderBy('translation_key')
            ->paginate(15);

        $languages = Language::active()->get();
        $groups = ['general', 'admin', 'front'];

        return view('admin.translations.index', compact('translations', 'languages', 'groups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $languages = Language::active()->get();
        $groups = ['general', 'admin', 'front'];

        return view('admin.translations.create', compact('languages', 'groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
            'translation_key' => 'required|string|max:255',
            'translation_value' => 'required|string',
            'group' => 'required|in:general,admin,front'
        ]);

        // Check if translation already exists
        $existing = Translation::where('language_id', $request->language_id)
            ->where('translation_key', $request->translation_key)
            ->where('group', $request->group)
            ->first();

        if ($existing) {
            return back()->withInput()
                ->withErrors(['translation_key' => 'This translation key already exists for this language and group.']);
        }

        Translation::create($request->all());

        // Clear translation cache
        TranslationHelper::clearCache();

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translation created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Translation $translation)
    {
        return view('admin.translations.show', compact('translation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Translation $translation)
    {
        $languages = Language::active()->get();
        $groups = ['general', 'admin', 'front'];

        return view('admin.translations.edit', compact('translation', 'languages', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Translation $translation)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
            'translation_key' => 'required|string|max:255',
            'translation_value' => 'required|string',
            'group' => 'required|in:general,admin,front'
        ]);

        // Check if translation already exists (excluding current one)
        $existing = Translation::where('language_id', $request->language_id)
            ->where('translation_key', $request->translation_key)
            ->where('group', $request->group)
            ->where('id', '!=', $translation->id)
            ->first();

        if ($existing) {
            return back()->withInput()
                ->withErrors(['translation_key' => 'This translation key already exists for this language and group.']);
        }

        $translation->update($request->all());

        // Clear translation cache
        TranslationHelper::clearCache();

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translation updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Translation $translation)
    {
        $translation->delete();

        // Clear translation cache
        TranslationHelper::clearCache();

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translation deleted successfully.');
    }

    /**
     * Bulk import translations
     */
    public function bulkImport(Request $request)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
            'group' => 'required|in:general,admin,front',
            'translations' => 'required|json'
        ]);

        $translations = json_decode($request->translations, true);
        $imported = 0;
        $skipped = 0;

        foreach ($translations as $key => $value) {
            $existing = Translation::where('language_id', $request->language_id)
                ->where('translation_key', $key)
                ->where('group', $request->group)
                ->first();

            if (!$existing) {
                Translation::create([
                    'language_id' => $request->language_id,
                    'translation_key' => $key,
                    'translation_value' => $value,
                    'group' => $request->group
                ]);
                $imported++;
            } else {
                $skipped++;
            }
        }

        // Clear translation cache
        TranslationHelper::clearCache();

        return redirect()->route('admin.translations.index')
            ->with('success', "Import completed: {$imported} imported, {$skipped} skipped.");
    }

    /**
     * Export translations
     */
    public function export(Request $request)
    {
        $request->validate([
            'language_id' => 'required|exists:languages,id',
            'group' => 'required|in:general,admin,front'
        ]);

        $translations = Translation::where('language_id', $request->language_id)
            ->where('group', $request->group)
            ->get();

        $data = [];
        foreach ($translations as $translation) {
            $data[$translation->translation_key] = $translation->translation_value;
        }

        $language = Language::find($request->language_id);
        $filename = "translations_{$language->code}_{$request->group}.json";

        return response()->json($data)
            ->header('Content-Disposition', "attachment; filename={$filename}")
            ->header('Content-Type', 'application/json');
    }

    /**
     * Clear translation cache
     */
    public function clearCache()
    {
        TranslationHelper::clearCache();

        return redirect()->route('admin.translations.index')
            ->with('success', 'Translation cache cleared successfully.');
    }
}
