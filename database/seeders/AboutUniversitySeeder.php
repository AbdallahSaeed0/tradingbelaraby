<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AboutUniversity;
use App\Models\AboutUniversityFeature;

class AboutUniversitySeeder extends Seeder
{
    public function run(): void
    {
        // Create main about university content
        AboutUniversity::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'A Few Words About the University',
                'title_ar' => 'كلمات قليلة عن الجامعة',
                'description' => 'Our community is being called to reimagine the future. As the only university where a renowned design school comes together with premier colleges, we are making learning more relevant and transformational.',
                'description_ar' => 'مجتمعنا مدعو لإعادة تصور المستقبل. كالجامعة الوحيدة التي يجتمع فيها مدرسة تصميم مشهورة مع كليات رائدة، نحن نجعل التعلم أكثر صلة وتحويلية.',
                'image' => 'https://eclass.mediacity.co.in/demo2/public/images/feature/1695115271about_img_02.png',
                'background_image' => 'https://eclass.mediacity.co.in/demo2/public/frontcss/img/bg/an-img-02.png',
                'is_active' => true,
            ]
        );

        // Create about university features
        $features = [
            [
                'title' => 'Instructor involvement',
                'title_ar' => 'مشاركة المدرب',
                'description' => 'Instructors are the primary facilitators in LMS courses.',
                'description_ar' => 'المدربون هم الميسرون الأساسيون في دورات نظام إدارة التعلم.',
                'number' => 1,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Bundle Courses',
                'title_ar' => 'دورات الحزمة',
                'description' => 'Bundle courses are often created by subject matter experts.',
                'description_ar' => 'غالباً ما يتم إنشاء دورات الحزمة من قبل خبراء في الموضوع.',
                'number' => 2,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Instructor Subscription',
                'title_ar' => 'اشتراك المدرب',
                'description' => 'An Instructor Subscription in an LMS (Learning Management System).',
                'description_ar' => 'اشتراك المدرب في نظام إدارة التعلم.',
                'number' => 3,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Live Meetings',
                'title_ar' => 'الاجتماعات المباشرة',
                'description' => 'Live Meetings in an LMS (Learning Management System).',
                'description_ar' => 'الاجتماعات المباشرة في نظام إدارة التعلم.',
                'number' => 4,
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($features as $feature) {
            AboutUniversityFeature::updateOrCreate(
                ['title' => $feature['title']],
                $feature
            );
        }
    }
}
