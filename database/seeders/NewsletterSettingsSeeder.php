<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NewsletterSettings;

class NewsletterSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        NewsletterSettings::create([
            'title' => 'Subscribe for Newsletter',
            'title_ar' => 'اشترك في النشرة الإخبارية',
            'description' => 'Manage Your Business With Our Software',
            'description_ar' => 'إدارة عملك مع برامجنا',
            'button_text' => 'Subscribe Now',
            'button_text_ar' => 'اشترك الآن',
            'placeholder' => 'Email Address...',
            'placeholder_ar' => 'عنوان البريد الإلكتروني...',
            'icon' => 'fa-solid fa-paper-plane',
            'is_active' => true,
        ]);
    }
}
