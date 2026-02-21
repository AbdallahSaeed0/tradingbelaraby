<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademyPolicy;

class AcademyPolicyController extends Controller
{
    /**
     * Get academy policy content for app display.
     * Returns localized content based on Accept-Language header.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $academyPolicy = AcademyPolicy::where('is_active', true)->first();

        if (!$academyPolicy) {
            return response()->json([
                'success' => false,
                'message' => 'Academy policy not found',
            ], 404);
        }

        $lang = $request->header('Accept-Language', 'en');
        $lang = str_starts_with($lang, 'ar') ? 'ar' : 'en';

        $data = [
            'title' => $lang === 'ar' ? ($academyPolicy->title_ar ?? $academyPolicy->title) : ($academyPolicy->title ?? $academyPolicy->title_ar),
            'description' => $lang === 'ar' ? ($academyPolicy->description_ar ?? $academyPolicy->description) : ($academyPolicy->description ?? $academyPolicy->description_ar),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
