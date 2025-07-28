<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HeroFeature;

class HeroFeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $heroFeatures = [
            [
                'title' => 'Learn Anytime, Anywhere',
                'title_ar' => 'تعلم في أي وقت وفي أي مكان',
                'subtitle' => 'Online Courses for Creative',
                'subtitle_ar' => 'دورات عبر الإنترنت للمبدعين',
                'icon' => 'fas fa-anchor',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Become a researcher',
                'title_ar' => 'كن باحثاً',
                'subtitle' => 'Improve Your Skills Online',
                'subtitle_ar' => 'حسّن مهاراتك عبر الإنترنت',
                'icon' => 'fas fa-bars',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Most Popular Courses',
                'title_ar' => 'الدورات الأكثر شعبية',
                'subtitle' => 'Learn on your schedule',
                'subtitle_ar' => 'تعلم حسب جدولك الزمني',
                'icon' => 'fas fa-basketball-ball',
                'order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($heroFeatures as $heroFeature) {
            HeroFeature::updateOrCreate(
                ['title' => $heroFeature['title']],
                $heroFeature
            );
        }
    }
}
