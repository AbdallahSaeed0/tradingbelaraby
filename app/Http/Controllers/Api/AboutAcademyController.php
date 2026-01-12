<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AboutUniversity;
use App\Models\ContactSettings;
use App\Models\MainContentSettings;
use Illuminate\Http\JsonResponse;

class AboutAcademyController extends Controller
{
    /**
     * Display academy information including about content and contact details.
     */
    public function index(): JsonResponse
    {
        // Get active AboutUniversity record
        $aboutUniversity = AboutUniversity::active()->first();

        // Get active ContactSettings record
        $contactSettings = ContactSettings::active()->first();

        // Get active MainContentSettings for logo
        $mainContentSettings = MainContentSettings::active()->first();

        // Prepare response data
        $data = [
            'about' => null,
            'contact' => null,
            'logo' => null,
        ];

        // Add about university data if available
        if ($aboutUniversity) {
            $data['about'] = [
                'id' => (string) $aboutUniversity->id,
                'title' => $aboutUniversity->title,
                'title_ar' => $aboutUniversity->title_ar,
                'description' => $aboutUniversity->description,
                'description_ar' => $aboutUniversity->description_ar,
                'image' => $aboutUniversity->image_url,
                'background_image' => $aboutUniversity->background_image_url,
            ];
        }

        // Add contact settings data if available
        if ($contactSettings) {
            $data['contact'] = [
                'phone' => $contactSettings->phone,
                'email' => $contactSettings->email,
                'address' => $contactSettings->address,
                'office_hours' => $contactSettings->office_hours ?? $contactSettings->formatted_office_hours,
                'social_links' => [
                    'facebook' => $contactSettings->social_facebook,
                    'twitter' => $contactSettings->social_twitter,
                    'youtube' => $contactSettings->social_youtube,
                    'linkedin' => $contactSettings->social_linkedin,
                    'snapchat' => $contactSettings->social_snapchat,
                    'tiktok' => $contactSettings->social_tiktok,
                ],
            ];
        }

        // Add logo from MainContentSettings if available
        if ($mainContentSettings && $mainContentSettings->logo_url) {
            $data['logo'] = $mainContentSettings->logo_url;
        }

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}
