<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AdminType;

class AdminTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'admin',
                'description' => 'Full system administrator with all permissions (System Type - Cannot be modified)',
                'permissions' => [
                    'manage_admins',
                    'manage_users',
                    'manage_courses',
                    'manage_categories',
                    'manage_enrollments',
                    'manage_quizzes',
                    'manage_homework',
                    'manage_live_classes',
                    'manage_questions_answers',
                    'manage_blogs',
                    'manage_translations',
                    'manage_languages',
                    'view_analytics',
                    'export_data',
                    'import_data',
                    'manage_own_courses',
                    'manage_own_quizzes',
                    'manage_own_homework',
                    'manage_own_live_classes',
                    'view_own_analytics',
                    'manage_own_questions_answers',
                    'manage_notifications',
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'instructor',
                'description' => 'Course instructor with course management permissions',
                'permissions' => [
                    'manage_own_courses',
                    'manage_own_quizzes',
                    'manage_own_homework',
                    'manage_own_live_classes',
                    'view_own_analytics',
                    'manage_own_questions_answers',
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'employee',
                'description' => 'Support employee with limited permissions',
                'permissions' => [
                    'view_courses',
                    'view_enrollments',
                    'view_users',
                    'manage_questions_answers',
                    'view_analytics',
                ],
                'sort_order' => 3,
            ],
        ];

        foreach ($types as $type) {
            AdminType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
