<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Feature;

/**
 * Fixes existing feature records:
 *   - Replaces broken external image URLs with FontAwesome icon classes
 *   - Fills in missing Arabic translations
 *
 * Run with: php artisan db:seed --class=UpdateFeatureIconsSeeder
 */
class UpdateFeatureIconsSeeder extends Seeder
{
    public function run(): void
    {
        $updates = [
            // Match by English title (case-insensitive)
            'skillful instructor' => [
                'title'          => 'Skillful Instructors',
                'title_ar'       => 'مدربين ماهرين',
                'description_ar' => 'جميع المدربين معتمدون من IFTA بشهادة CFTe، وشهادة CETA من ESTA. جميعهم يتمتعون بخبرة تزيد عن عشر سنوات في التحليل الفني والمالي.',
                'icon'           => 'fas fa-chalkboard-teacher',
            ],
            'happy student' => [
                'title'          => 'Happy Trainees',
                'title_ar'       => 'متدربين سعداء',
                'description_ar' => 'جميع المتدربين سعداء لأنهم يتعلمون كيفية التداول باستخدام التحليل الفني والمالي.',
                'icon'           => 'fas fa-smile',
            ],
            'live classes' => [
                'title_ar'       => 'الدورات المباشرة',
                'description_ar' => 'معظم دوراتنا متاحة عبر الإنترنت ومباشرة عبر تطبيق Zoom.',
                'icon'           => 'fas fa-broadcast-tower',
            ],
            'video' => [
                'title'          => 'Recorded Videos',
                'title_ar'       => 'مقاطع فيديو مسجلة',
                'description_ar' => 'دروس مسجلة عالية الجودة متاحة في أي وقت وعلى أي جهاز، جزء من المحتوى التعليمي للطلاب.',
                'icon'           => 'fas fa-play-circle',
            ],
        ];

        Feature::all()->each(function (Feature $feature) use ($updates) {
            $titleLower = strtolower($feature->title ?? '');
            foreach ($updates as $keyword => $data) {
                if (str_contains($titleLower, $keyword)) {
                    $feature->update($data);
                    $this->command->info("Updated feature: {$feature->title}");
                    break;
                }
            }
        });

        $this->command->info('Feature icons updated successfully.');
    }
}
