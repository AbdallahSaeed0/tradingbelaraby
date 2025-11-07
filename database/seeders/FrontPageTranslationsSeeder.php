<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FrontPageTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command?->error('English and Arabic languages must exist before running the front page translations seeder.');
            return;
        }

        $translations = include database_path('seeders/data/front_page_translations.php');

        foreach ($translations as $key => $payload) {
            $englishValue = $payload['en'] ?? (Str::contains($key, '_') ? Str::headline($key) : $key);
            $arabicValue = $payload['ar'] ?? $englishValue;

            $this->seedTranslation($english->id, $key, $englishValue);
            $this->seedTranslation($arabic->id, $key, $arabicValue);
        }

        $this->command?->info('Front page translations seeded successfully.');
    }

    private function seedTranslation(int $languageId, string $key, string $value): void
    {
        Translation::updateOrCreate(
            [
                'language_id' => $languageId,
                'translation_key' => $key,
                'group' => 'front',
            ],
            [
                'translation_value' => $value,
            ]
        );
    }
}

