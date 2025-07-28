<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Slider;

class SliderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliders = [
            [
                'title' => 'Education is the best key success in life',
                'welcome_text' => 'WELCOME TO E-CLASS',
                'subtitle' => 'Online Courses',
                'background_image' => 'https://eclass.mediacity.co.in/demo2/public/images/slider/slider_img02.png',
                'button_text' => 'Search',
                'button_url' => null,
                'search_placeholder' => 'Search Courses',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Transform Your Future with Online Learning',
                'welcome_text' => 'LEARN FROM EXPERTS',
                'subtitle' => 'Expert-Led Courses',
                'background_image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80',
                'button_text' => 'Explore',
                'button_url' => null,
                'search_placeholder' => 'Find Your Course',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Study at Your Own Pace, Anywhere',
                'welcome_text' => 'FLEXIBLE LEARNING',
                'subtitle' => 'Self-Paced Learning',
                'background_image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80',
                'button_text' => 'Start Learning',
                'button_url' => null,
                'search_placeholder' => 'Browse Categories',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($sliders as $slider) {
            Slider::create($slider);
        }
    }
}
