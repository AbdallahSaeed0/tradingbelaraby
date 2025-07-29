<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FAQ;

class FAQSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'title' => 'Lifetime Access',
                'title_ar' => 'الوصول مدى الحياة',
                'content' => 'Lifetime access FAQ for an e-class refers to a set of frequently asked questions about how students can access their course materials, videos, and resources indefinitely after enrollment. This typically includes information about what is included in lifetime access, how to access materials, and any limitations or conditions.',
                'content_ar' => 'الوصول مدى الحياة للدورات الإلكترونية يشير إلى مجموعة من الأسئلة الشائعة حول كيفية وصول الطلاب إلى المواد التعليمية والفيديوهات والموارد بشكل غير محدود بعد التسجيل. يتضمن عادة معلومات حول ما هو مدرج في الوصول مدى الحياة، وكيفية الوصول إلى المواد، وأي قيود أو شروط.',
                'order' => 1,
                'is_active' => true,
                'is_expanded' => true,
            ],
            [
                'title' => 'Account/Profile',
                'title_ar' => 'الحساب/الملف الشخصي',
                'content' => 'Account/Profile FAQ for an e-class refers to questions about user accounts and profiles. This includes information about creating accounts, updating profile information, password management, account security, and troubleshooting common account-related issues.',
                'content_ar' => 'أسئلة الحساب/الملف الشخصي للدورات الإلكترونية تشير إلى الأسئلة حول حسابات المستخدمين والملفات الشخصية. يتضمن معلومات حول إنشاء الحسابات، تحديث معلومات الملف الشخصي، إدارة كلمات المرور، أمان الحساب، وحل المشاكل الشائعة المتعلقة بالحساب.',
                'order' => 2,
                'is_active' => true,
                'is_expanded' => false,
            ],
            [
                'title' => 'Course Taking',
                'title_ar' => 'أخذ الدورات',
                'content' => 'Course Taking FAQ for an e-class refers to a set of questions about taking courses. This includes information about how to navigate through course content, complete assignments, participate in discussions, track progress, and understand course requirements and expectations.',
                'content_ar' => 'أسئلة أخذ الدورات للدورات الإلكترونية تشير إلى مجموعة من الأسئلة حول أخذ الدورات. يتضمن معلومات حول كيفية التنقل في محتوى الدورة، إكمال المهام، المشاركة في المناقشات، تتبع التقدم، وفهم متطلبات وتوقعات الدورة.',
                'order' => 3,
                'is_active' => true,
                'is_expanded' => false,
            ],
            [
                'title' => 'Troubleshooting',
                'title_ar' => 'حل المشاكل',
                'content' => 'Troubleshooting FAQ for an e-class refers to questions about resolving issues. This includes common technical problems, platform navigation issues, content access problems, and step-by-step solutions for various challenges students might encounter while using the e-learning platform.',
                'content_ar' => 'أسئلة حل المشاكل للدورات الإلكترونية تشير إلى الأسئلة حول حل المشاكل. يتضمن المشاكل التقنية الشائعة، مشاكل التنقل في المنصة، مشاكل الوصول إلى المحتوى، والحلول خطوة بخطوة للتحديات المختلفة التي قد يواجهها الطلاب أثناء استخدام منصة التعلم الإلكتروني.',
                'order' => 4,
                'is_active' => true,
                'is_expanded' => false,
            ],
        ];

        foreach ($faqs as $faq) {
            FAQ::updateOrCreate(
                ['title' => $faq['title']],
                $faq
            );
        }
    }
}
