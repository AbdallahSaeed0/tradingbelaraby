<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CTAVideo;

class CTAVideoSeeder extends Seeder
{
    public function run(): void
    {
        CTAVideo::updateOrCreate(
            ['id' => 1],
            [
                'title' => 'Start learning anywhere, anytime...',
                'title_ar' => 'ابدأ التعلم في أي مكان وفي أي وقت...',
                'description' => 'Customers in today\'s tech-savvy market demand comprehensive information about any new good or service they are thinking about purchasing.',
                'description_ar' => 'يطلب العملاء في السوق الحالي المتمرس في التكنولوجيا معلومات شاملة حول أي سلعة أو خدمة جديدة يفكرون في شرائها.',
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'background_image' => null,
                'is_active' => true,
            ]
        );
    }
}
