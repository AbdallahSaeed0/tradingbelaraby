<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\TermsConditions;
use App\Models\Translation;

class TermsConditionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default terms and conditions
        TermsConditions::firstOrCreate(
            ['slug' => 'terms-and-conditions'],
            [
                'title' => 'Terms and Conditions',
                'title_ar' => 'الشروط والأحكام',
                'description' => '<h2>Terms and Conditions</h2>
<p>Welcome to our website. If you continue to browse and use this website, you are agreeing to comply with and be bound by the following terms and conditions of use.</p>

<h3>1. Introduction</h3>
<p>These terms and conditions govern your use of this website; by using this website, you accept these terms and conditions in full.</p>

<h3>2. License to Use Website</h3>
<p>Unless otherwise stated, we or our licensors own the intellectual property rights in the website and material on the website.</p>

<h3>3. Acceptable Use</h3>
<p>You must not use this website in any way that causes, or may cause, damage to the website or impairment of the availability or accessibility of the website.</p>

<h3>4. Limitations of Liability</h3>
<p>We will not be liable to you in relation to the contents of, or use of, or otherwise in connection with, this website.</p>

<h3>5. Contact Information</h3>
<p>If you have any questions about these Terms and Conditions, please contact us through our contact page.</p>',
                'description_ar' => '<h2>الشروط والأحكام</h2>
<p>مرحبًا بك في موقعنا. إذا واصلت تصفح هذا الموقع واستخدامه، فأنت توافق على الالتزام بالشروط والأحكام التالية للاستخدام.</p>

<h3>1. المقدمة</h3>
<p>تحكم هذه الشروط والأحكام استخدامك لهذا الموقع؛ باستخدام هذا الموقع، فإنك تقبل هذه الشروط والأحكام بالكامل.</p>

<h3>2. ترخيص استخدام الموقع</h3>
<p>ما لم ينص على خلاف ذلك، فإننا أو مرخصينا نمتلك حقوق الملكية الفكرية في الموقع والمواد الموجودة على الموقع.</p>

<h3>3. الاستخدام المقبول</h3>
<p>يجب ألا تستخدم هذا الموقع بأي طريقة تسبب أو قد تسبب ضررًا للموقع أو إعاقة توفر أو إمكانية الوصول إلى الموقع.</p>

<h3>4. حدود المسؤولية</h3>
<p>لن نكون مسؤولين تجاهك فيما يتعلق بمحتويات أو استخدام أو أي شيء آخر فيما يتعلق بهذا الموقع.</p>

<h3>5. معلومات الاتصال</h3>
<p>إذا كانت لديك أي أسئلة حول هذه الشروط والأحكام، يرجى الاتصال بنا من خلال صفحة الاتصال الخاصة بنا.</p>',
                'is_active' => true,
            ]
        );

        // Add translation keys
        $translations = [
            [
                'language_id' => 1, // English
                'translation_key' => 'terms_and_conditions',
                'translation_value' => 'Terms and Conditions',
                'group' => 'common'
            ],
            [
                'language_id' => 2, // Arabic
                'translation_key' => 'terms_and_conditions',
                'translation_value' => 'الشروط والأحكام',
                'group' => 'common'
            ],
            [
                'language_id' => 3, // French
                'translation_key' => 'terms_and_conditions',
                'translation_value' => 'Termes et Conditions',
                'group' => 'common'
            ],
            [
                'language_id' => 1, // English
                'translation_key' => 'back_to_home',
                'translation_value' => 'Back to Home',
                'group' => 'common'
            ],
            [
                'language_id' => 2, // Arabic
                'translation_key' => 'back_to_home',
                'translation_value' => 'العودة للرئيسية',
                'group' => 'common'
            ],
            [
                'language_id' => 3, // French
                'translation_key' => 'back_to_home',
                'translation_value' => 'Retour à l\'accueil',
                'group' => 'common'
            ],
        ];

        foreach ($translations as $translation) {
            Translation::firstOrCreate(
                [
                    'language_id' => $translation['language_id'],
                    'translation_key' => $translation['translation_key']
                ],
                [
                    'translation_value' => $translation['translation_value'],
                    'group' => $translation['group']
                ]
            );
        }
    }
}

