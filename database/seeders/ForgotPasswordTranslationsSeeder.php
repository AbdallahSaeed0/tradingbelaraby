<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Translation;

class ForgotPasswordTranslationsSeeder extends Seeder
{
    public function run(): void
    {
        $english = Language::where('code', 'en')->first();
        $arabic = Language::where('code', 'ar')->first();

        if (!$english || !$arabic) {
            $this->command->error('Languages not found. Please run LanguageSeeder first.');
            return;
        }

        $translations = [
            // Verify OTP page
            [
                'key' => 'Verify OTP',
                'en' => 'Verify OTP',
                'ar' => 'التحقق من رمز التحقق',
            ],
            [
                'key' => 'Enter the 6-digit OTP sent to your email.',
                'en' => 'Enter the 6-digit OTP sent to your email.',
                'ar' => 'أدخل رمز التحقق المكون من 6 أرقام المرسل إلى بريدك الإلكتروني.',
            ],
            [
                'key' => 'Email:',
                'en' => 'Email:',
                'ar' => 'البريد الإلكتروني:',
            ],
            [
                'key' => 'Email:',
                'en' => 'Email:',
                'ar' => 'البريد الإلكتروني:',
            ],
            [
                'key' => 'OTP Code (6 digits)',
                'en' => 'OTP Code (6 digits)',
                'ar' => 'رمز التحقق (6 أرقام)',
            ],
            [
                'key' => 'Request new OTP',
                'en' => 'Request new OTP',
                'ar' => 'طلب رمز تحقق جديد',
            ],
            
            // Set New Password page
            [
                'key' => 'Set New Password',
                'en' => 'Set New Password',
                'ar' => 'تعيين كلمة مرور جديدة',
            ],
            [
                'key' => 'Enter your new password below.',
                'en' => 'Enter your new password below.',
                'ar' => 'أدخل كلمة المرور الجديدة أدناه.',
            ],
            [
                'key' => 'New Password',
                'en' => 'New Password',
                'ar' => 'كلمة المرور الجديدة',
            ],
            [
                'key' => 'Confirm Password',
                'en' => 'Confirm Password',
                'ar' => 'تأكيد كلمة المرور',
            ],
            
            // Validation messages
            [
                'key' => 'Password must be at least 6 characters.',
                'en' => 'Password must be at least 6 characters.',
                'ar' => 'يجب أن تكون كلمة المرور 6 أحرف على الأقل.',
            ],
            [
                'key' => 'Passwords do not match.',
                'en' => 'Passwords do not match.',
                'ar' => 'كلمات المرور غير متطابقة.',
            ],
            [
                'key' => 'Invalid or expired OTP. Please request a new one.',
                'en' => 'Invalid or expired OTP. Please request a new one.',
                'ar' => 'رمز التحقق غير صحيح أو منتهي الصلاحية. يرجى طلب رمز جديد.',
            ],
            [
                'key' => 'Please verify your OTP first.',
                'en' => 'Please verify your OTP first.',
                'ar' => 'يرجى التحقق من رمز التحقق أولاً.',
            ],
            [
                'key' => 'Please enter your email first.',
                'en' => 'Please enter your email first.',
                'ar' => 'يرجى إدخال بريدك الإلكتروني أولاً.',
            ],
        ];

        foreach ($translations as $translation) {
            // English
            Translation::updateOrCreate(
                [
                    'language_id' => $english->id,
                    'translation_key' => $translation['key'],
                    'group' => 'front',
                ],
                [
                    'translation_value' => $translation['en'],
                ]
            );

            // Arabic
            Translation::updateOrCreate(
                [
                    'language_id' => $arabic->id,
                    'translation_key' => $translation['key'],
                    'group' => 'front',
                ],
                [
                    'translation_value' => $translation['ar'],
                ]
            );
        }

        $this->command->info('Forgot password translations seeded successfully!');
    }
}
