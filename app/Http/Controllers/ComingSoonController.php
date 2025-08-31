<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComingSoonController extends Controller
{
    public function index(Request $request)
    {
        // Detect browser language
        $acceptLanguage = $request->header('Accept-Language');
        $language = 'en'; // Default to English

        if ($acceptLanguage) {
            // Parse Accept-Language header
            $languages = explode(',', $acceptLanguage);
            $primaryLanguage = explode(';', $languages[0])[0];
            $languageCode = explode('-', $primaryLanguage)[0];

            // Check if it's Arabic
            if (in_array($languageCode, ['ar'])) {
                $language = 'ar';
            }
        }

        // Set app locale
        app()->setLocale($language);

        return view('coming-soon', compact('language'));
    }

    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:subscribers,email',
            'whatsapp_number' => 'nullable|string|max:20',
            'country' => 'required|string|max:255',
            'years_of_experience' => 'required|in:10,20,30,40,50',
            'notes' => 'nullable|string|max:1000',
            'language' => 'required|string|in:en,ar',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $subscriber = Subscriber::create($request->all());

            return response()->json([
                'success' => true,
                'message' => custom_trans('subscription_success', 'coming_soon')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => custom_trans('subscription_error', 'coming_soon')
            ], 500);
        }
    }
}
