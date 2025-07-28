<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Language;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'name' => 'English',
                'native_name' => 'English',
                'code' => 'en',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => true
            ],
            [
                'name' => 'Arabic',
                'native_name' => 'العربية',
                'code' => 'ar',
                'direction' => 'rtl',
                'is_active' => true,
                'is_default' => false
            ],
            [
                'name' => 'French',
                'native_name' => 'Français',
                'code' => 'fr',
                'direction' => 'ltr',
                'is_active' => true,
                'is_default' => false
            ]
        ];

        foreach ($languages as $language) {
            Language::updateOrCreate(
                ['code' => $language['code']],
                $language
            );
        }
    }
}
