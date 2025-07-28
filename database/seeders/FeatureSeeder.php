<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Feature;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $features = [
            [
                'title' => 'Skillful Instructor',
                'description' => 'Skillful Instructor is a LMS designed to help instructors create, manage, and deliver online courses.',
                'icon' => 'https://eclass.mediacity.co.in/demo2/public/images/facts/16751563391644382079instructor.png',
                'number' => 45,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Happy Student',
                'description' => 'Happy Student is likely a company or brand name that provides educational services, although without further context.',
                'icon' => 'https://eclass.mediacity.co.in/demo2/public/images/facts/16751563901644382489student.png',
                'number' => 84,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Live Classes',
                'description' => 'Live classes (LMS) refer to educational or training sessions that are delivered in real-time, usually over the internet.',
                'icon' => 'https://eclass.mediacity.co.in/demo2/public/images/facts/16751564601644382519live.png',
                'number' => 94,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Video',
                'description' => 'LMS videos refer to videos that are used as part of a (LMS) to deliver educational content to students.',
                'icon' => 'https://eclass.mediacity.co.in/demo2/public/images/facts/16751566461644382554video.png',
                'number' => 63,
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($features as $feature) {
            Feature::create($feature);
        }
    }
}
