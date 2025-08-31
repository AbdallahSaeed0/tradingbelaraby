<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Translation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComingSoonTranslationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get languages
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->error('English and Arabic languages must exist before running this seeder.');
            return;
        }

        $translations = [
            // English translations
            'en' => [
                'title' => 'Coming Soon',
                'subtitle' => 'We are working hard to bring you an amazing learning experience. Stay tuned!',
                'sign_with_us' => 'Sign With Us',
                'name' => 'Name',
                'phone' => 'Phone',
                'email' => 'Email',
                'whatsapp_number' => 'WhatsApp Number',
                'country' => 'Country',
                'years_of_experience' => 'Years of Experience',
                'select_experience' => 'Select years of experience',
                'years' => 'Years',
                'notes' => 'Notes',
                'notes_placeholder' => 'Tell us more about yourself...',
                'submit' => 'Submit',
                'subscription_success' => 'Thank you for subscribing! We will contact you soon.',
                'subscription_error' => 'Something went wrong. Please try again.',
            ],
            // Arabic translations
            'ar' => [
                'title' => 'قريباً',
                'subtitle' => 'نحن نعمل بجد لنقدم لك تجربة تعليمية مذهلة. ترقبونا!',
                'sign_with_us' => 'سجل معنا',
                'name' => 'الاسم',
                'phone' => 'رقم الهاتف',
                'email' => 'البريد الإلكتروني',
                'whatsapp_number' => 'رقم الواتساب',
                'country' => 'الدولة',
                'years_of_experience' => 'سنوات الخبرة',
                'select_experience' => 'اختر سنوات الخبرة',
                'years' => 'سنة',
                'notes' => 'ملاحظات',
                'notes_placeholder' => 'أخبرنا المزيد عن نفسك...',
                'submit' => 'إرسال',
                'subscription_success' => 'شكراً لك على التسجيل! سنتواصل معك قريباً.',
                'subscription_error' => 'حدث خطأ ما. يرجى المحاولة مرة أخرى.',
            ],
        ];

        foreach ($translations as $langCode => $langTranslations) {
            $language = $langCode === 'en' ? $english : $arabic;

            foreach ($langTranslations as $key => $value) {
                Translation::updateOrCreate(
                    [
                        'language_id' => $language->id,
                        'translation_key' => $key,
                        'group' => 'coming_soon'
                    ],
                    [
                        'translation_value' => $value
                    ]
                );
            }
        }

        $this->command->info('Coming Soon translations seeded successfully!');
    }
}
