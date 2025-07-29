<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ScholarshipBanner;

class ScholarshipBannerSeeder extends Seeder
{
    public function run(): void
    {
        ScholarshipBanner::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Scholarship Programs',
                'title_ar' => 'برامج المنح الدراسية',
                'button_text' => 'Become An Instructor',
                'button_text_ar' => 'كن مدرساً',
                'button_url' => '#',
                'background_image' => null,
                'is_active' => true,
            ]
        );
    }
}
