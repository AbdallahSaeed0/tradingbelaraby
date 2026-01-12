<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LanguageController extends Controller
{
    /**
     * Display a listing of available languages.
     */
    public function index(Request $request): JsonResponse
    {
        $activeOnly = $request->boolean('active_only', false);

        // For now, return hardcoded languages
        // TODO: Create languages table and model later
        $languages = [
            [
                'id' => '1',
                'code' => 'en',
                'name' => 'English',
                'name_ar' => 'الإنجليزية',
                'is_active' => true,
                'is_default' => true,
            ],
            [
                'id' => '2',
                'code' => 'ar',
                'name' => 'Arabic',
                'name_ar' => 'العربية',
                'is_active' => true,
                'is_default' => false,
            ],
        ];

        // Filter active languages if requested
        if ($activeOnly) {
            $languages = array_filter($languages, fn($lang) => $lang['is_active'] === true);
        }

        return response()->json(array_values($languages));
    }
}
