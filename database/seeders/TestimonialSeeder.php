<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Testimonial;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Sarah Johnson',
                'name_ar' => 'سارة جونسون',
                'position' => 'Software Engineer',
                'position_ar' => 'مهندسة برمجيات',
                'company' => 'Tech Solutions Inc.',
                'company_ar' => 'شركة حلول التكنولوجيا',
                'content' => 'The courses here are absolutely amazing! The instructors are knowledgeable and the content is up-to-date with the latest industry standards. I learned so much and was able to advance my career significantly.',
                'content_ar' => 'الدورات هنا مذهلة تماماً! المدربون على دراية عالية والمحتوى محدث بأحدث معايير الصناعة. تعلمت الكثير وتمكنت من تطوير مسيرتي المهنية بشكل كبير.',
                'rating' => 5,
                'order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Michael Chen',
                'name_ar' => 'مايكل تشين',
                'position' => 'UX Designer',
                'position_ar' => 'مصمم تجربة المستخدم',
                'company' => 'Creative Design Studio',
                'company_ar' => 'استوديو التصميم الإبداعي',
                'content' => 'I was looking for a comprehensive design course and found exactly what I needed here. The practical projects and real-world examples made learning much more effective.',
                'content_ar' => 'كنت أبحث عن دورة تصميم شاملة ووجدت بالضبط ما أحتاجه هنا. المشاريع العملية والأمثلة من العالم الحقيقي جعلت التعلم أكثر فعالية.',
                'rating' => 5,
                'order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Emily Rodriguez',
                'name_ar' => 'إيميلي رودريغيز',
                'position' => 'Marketing Manager',
                'position_ar' => 'مديرة التسويق',
                'company' => 'Global Marketing Co.',
                'company_ar' => 'شركة التسويق العالمية',
                'content' => 'The marketing courses provided me with valuable insights and strategies that I immediately applied to my work. The instructors are industry experts who really know their stuff.',
                'content_ar' => 'قدمت لي دورات التسويق رؤى واستراتيجيات قيمة طبقها فوراً في عملي. المدربون خبراء في الصناعة يعرفون حقاً ما يتحدثون عنه.',
                'rating' => 4,
                'order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'David Kim',
                'name_ar' => 'ديفيد كيم',
                'position' => 'Data Scientist',
                'position_ar' => 'عالم بيانات',
                'company' => 'Analytics Pro',
                'company_ar' => 'محلل البيانات المحترف',
                'content' => 'The data science courses are incredibly comprehensive. I went from knowing nothing about machine learning to implementing complex algorithms in my projects.',
                'content_ar' => 'دورات علوم البيانات شاملة بشكل لا يصدق. انتقلت من عدم معرفة شيء عن التعلم الآلي إلى تطبيق خوارزميات معقدة في مشاريعي.',
                'rating' => 5,
                'order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Lisa Thompson',
                'name_ar' => 'ليزا طومسون',
                'position' => 'Project Manager',
                'position_ar' => 'مديرة المشاريع',
                'company' => 'Innovation Labs',
                'company_ar' => 'مختبرات الابتكار',
                'content' => 'The project management certification I earned here opened many doors for me. The course structure and support system are excellent.',
                'content_ar' => 'شهادة إدارة المشاريع التي حصلت عليها هنا فتحت لي أبواباً كثيرة. هيكل الدورة ونظام الدعم ممتازان.',
                'rating' => 4,
                'order' => 5,
                'is_active' => true,
            ],
        ];

        foreach ($testimonials as $testimonialData) {
            Testimonial::updateOrCreate(
                ['name' => $testimonialData['name']],
                $testimonialData
            );
        }

        $this->command->info('Sample testimonials created successfully!');
    }
}
