<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\JsonResponse;

class SliderController extends Controller
{
    /**
     * Get active sliders for home page (same as website).
     */
    public function index(): JsonResponse
    {
        $sliders = Slider::active()
            ->ordered()
            ->get()
            ->map(function ($slider) {
                return [
                    'id' => (string) $slider->id,
                    'title' => $slider->title,
                    'title_ar' => $slider->title_ar,
                    'description' => $slider->description,
                    'description_ar' => $slider->description_ar,
                    'background_image_url' => $slider->background_image_url,
                    'button_text' => $slider->button_text,
                    'button_text_ar' => $slider->button_text_ar,
                    'button_url' => $slider->button_url,
                    'order' => $slider->order,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $sliders,
        ]);
    }
}
