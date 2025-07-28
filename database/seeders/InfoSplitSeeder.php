<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InfoSplit;

class InfoSplitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InfoSplit::create([
            'title' => 'Eclass Learning Management System',
            'title_ar' => 'نظام إدارة التعلم الإلكتروني',
            'description' => 'Our community is being called to reimagine the future. As the only university where a renowned design school comes together with premier colleges, we are making learning more relevant and transformational.',
            'description_ar' => 'يُطلب من مجتمعنا إعادة تصور المستقبل. كجامعة فريدة حيث تجتمع مدرسة تصميم مشهورة مع كليات رائدة، نجعل التعلم أكثر صلة وتحولاً.',
            'button_text' => 'Read More',
            'button_text_ar' => 'اقرأ المزيد',
            'button_url' => '#',
            'is_active' => true,
        ]);

        $this->command->info('Info Split Section seeded successfully!');
    }
}
