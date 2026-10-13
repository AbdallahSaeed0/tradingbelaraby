<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Language;
use App\Models\Translation;

class NbePaymentTranslationsSeeder extends Seeder
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
            // Checkout page — card option
            [
                'key' => 'Credit / Debit Card',
                'en' => 'Credit / Debit Card',
                'ar' => 'بطاقة ائتمان / خصم',
            ],
            [
                'key' => 'Pay securely with Visa or Mastercard',
                'en' => 'Pay securely with Visa or Mastercard',
                'ar' => 'ادفع بأمان باستخدام فيزا أو ماستركارد',
            ],

            // NBE payment page
            [
                'key' => 'credit_card_payment',
                'en' => 'Card Payment',
                'ar' => 'الدفع بالبطاقة',
            ],
            [
                'key' => 'nbe_cards_accepted',
                'en' => 'Visa and Mastercard accepted',
                'ar' => 'نقبل بطاقات فيزا وماستركارد',
            ],
            [
                'key' => 'loading_payment_form',
                'en' => 'Loading secure payment form...',
                'ar' => 'جاري تحميل نموذج الدفع الآمن...',
            ],
            [
                'key' => 'nbe_ssl_encrypted',
                'en' => 'SSL encrypted',
                'ar' => 'اتصال مشفّر',
            ],
            [
                'key' => 'nbe_3ds_protected',
                'en' => '3D Secure protected',
                'ar' => 'محمي بتقنية 3D Secure',
            ],
            [
                'key' => 'nbe_processed_by_bank',
                'en' => 'Processed by National Bank of Egypt',
                'ar' => 'تتم المعالجة عبر البنك الأهلي المصري',
            ],
            [
                'key' => 'nbe_amount_charged',
                'en' => 'Amount to be charged',
                'ar' => 'المبلغ الذي سيتم خصمه',
            ],
            [
                'key' => 'nbe_currency_note',
                'en' => 'Your card will be charged in this currency at the current exchange rate.',
                'ar' => 'سيتم الخصم من بطاقتك بهذه العملة وفق سعر الصرف الحالي.',
            ],
            [
                'key' => 'payment_load_error',
                'en' => 'Unable to load the payment form. Please refresh the page and try again.',
                'ar' => 'تعذّر تحميل نموذج الدفع. يرجى تحديث الصفحة والمحاولة مرة أخرى.',
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

        $this->command->info('NBE payment translations seeded successfully!');
    }
}
