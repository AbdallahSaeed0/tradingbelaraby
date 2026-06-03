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
                'title'          => 'Recorded Videos',
                'title_ar'       => 'مقاطع فيديو مسجلة',
                'description'    => 'High-quality recorded lessons available anytime, on any device.',
                'description_ar' => 'دروس مسجلة عالية الجودة متاحة في أي وقت وعلى أي جهاز، جزء من المحتوى التعليمي للطلاب.',
                'icon'           => 'fas fa-play-circle',
                'number'         => 63,
                'order'          => 1,
                'is_active'      => true,
            ],
            [
                'title'          => 'Live Classes',
                'title_ar'       => 'الدورات المباشرة',
                'description'    => 'Interactive live sessions delivered over the internet via Zoom.',
                'description_ar' => 'معظم دوراتنا متاحة عبر الإنترنت ومباشرة عبر تطبيق Zoom.',
                'icon'           => 'fas fa-broadcast-tower',
                'number'         => 94,
                'order'          => 2,
                'is_active'      => true,
            ],
            [
                'title'          => 'Happy Trainees',
                'title_ar'       => 'متدربين سعداء',
                'description'    => 'Thousands of satisfied learners who achieved their financial goals.',
                'description_ar' => 'جميع المتدربين سعداء لأنهم يتعلمون كيفية التداول باستخدام التحليل الفني والمالي.',
                'icon'           => 'fas fa-smile',
                'number'         => 84,
                'order'          => 3,
                'is_active'      => true,
            ],
            [
                'title'          => 'Skillful Instructors',
                'title_ar'       => 'مدربين ماهرين',
                'description'    => 'Certified instructors with 10+ years of financial markets experience.',
                'description_ar' => 'جميع المدربين معتمدون من IFTA بشهادة CFTe، وشهادة CETA من ESTA. جميعهم يتمتعون بخبرة تزيد عن عشر سنوات في التحليل الفني والمالي.',
                'icon'           => 'fas fa-chalkboard-teacher',
                'number'         => 45,
                'order'          => 4,
                'is_active'      => true,
            ],
        ];

        foreach ($features as $feature) {
            Feature::create($feature);
        }
    }
}
