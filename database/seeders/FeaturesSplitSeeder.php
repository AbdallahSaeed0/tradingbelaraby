<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FeaturesSplit;
use App\Models\FeaturesSplitItem;

class FeaturesSplitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main content
        FeaturesSplit::create([
            'title' => 'Our Best Features',
            'title_ar' => 'أفضل ميزاتنا',
            'description' => 'Customers in today\'s tech-savvy market demand comprehensive information about any new good or service they are thinking about purchasing.',
            'description_ar' => 'يطلب العملاء في السوق التقني اليوم معلومات شاملة حول أي سلعة أو خدمة جديدة يفكرون في شرائها.',
            'is_active' => true,
        ]);

        // Create feature items
        $features = [
            [
                'title' => 'Privacy',
                'title_ar' => 'الخصوصية',
                'description' => 'We take privacy seriously and ensure all your data is protected with industry-standard security measures.',
                'description_ar' => 'نحن نأخذ الخصوصية على محمل الجد ونتأكد من حماية جميع بياناتك بإجراءات أمنية معيارية للصناعة.',
                'icon' => 'fas fa-user-shield',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'title' => 'Support',
                'title_ar' => 'الدعم',
                'description' => 'For support, please contact us through our various channels and we\'ll get back to you as soon as possible.',
                'description_ar' => 'للحصول على الدعم، يرجى الاتصال بنا من خلال قنواتنا المختلفة وسنرد عليك في أقرب وقت ممكن.',
                'icon' => 'fas fa-headset',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'title' => 'Help and Support',
                'title_ar' => 'المساعدة والدعم',
                'description' => 'I am here to help and support you throughout your learning journey with comprehensive assistance.',
                'description_ar' => 'أنا هنا لمساعدتك ودعمك طوال رحلة التعلم الخاصة بك مع المساعدة الشاملة.',
                'icon' => 'fas fa-hands-helping',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'title' => 'Contact Us',
                'title_ar' => 'اتصل بنا',
                'description' => 'If you have any questions or need assistance, don\'t hesitate to reach out to our team.',
                'description_ar' => 'إذا كان لديك أي أسئلة أو تحتاج إلى مساعدة، لا تتردد في التواصل مع فريقنا.',
                'icon' => 'fas fa-envelope',
                'order' => 4,
                'is_active' => true,
            ],
            [
                'title' => '24 * 7',
                'title_ar' => '24 * 7',
                'description' => 'We understand the importance of round-the-clock support and are available whenever you need us.',
                'description_ar' => 'نحن نفهم أهمية الدعم على مدار الساعة ونحن متاحون متى احتجت إلينا.',
                'icon' => 'fas fa-clock',
                'order' => 5,
                'is_active' => true,
            ],
            [
                'title' => 'Product Developments',
                'title_ar' => 'تطوير المنتجات',
                'description' => 'Product development is the process of creating, designing, and bringing new products to market.',
                'description_ar' => 'تطوير المنتج هو عملية إنشاء وتصميم وإحضار منتجات جديدة إلى السوق.',
                'icon' => 'fas fa-cogs',
                'order' => 6,
                'is_active' => true,
            ],
        ];

        foreach ($features as $feature) {
            FeaturesSplitItem::create($feature);
        }

        $this->command->info('Features Split Section seeded successfully!');
    }
}
