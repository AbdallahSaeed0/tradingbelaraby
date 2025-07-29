<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\AdminType;
use Illuminate\Support\Facades\Hash;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the instructor admin type
        $instructorType = AdminType::where('name', 'instructor')->first();

        if (!$instructorType) {
            $this->command->error('Instructor admin type not found. Please run AdminTypeSeeder first.');
            return;
        }

        $instructors = [
            [
                'name' => 'Dr. Sarah Johnson',
                'email' => 'sarah.johnson@eclass.com',
                'password' => Hash::make('123456'),
                'admin_type_id' => $instructorType->id,
                'phone' => '+1 (555) 123-4567',
                'is_active' => true,
            ],
            [
                'name' => 'Prof. Michael Chen',
                'email' => 'michael.chen@eclass.com',
                'password' => Hash::make('123456'),
                'admin_type_id' => $instructorType->id,
                'phone' => '+1 (555) 234-5678',
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Emily Rodriguez',
                'email' => 'emily.rodriguez@eclass.com',
                'password' => Hash::make('123456'),
                'admin_type_id' => $instructorType->id,
                'phone' => '+1 (555) 345-6789',
                'is_active' => true,
            ],
            [
                'name' => 'Prof. David Kim',
                'email' => 'david.kim@eclass.com',
                'password' => Hash::make('123456'),
                'admin_type_id' => $instructorType->id,
                'phone' => '+1 (555) 456-7890',
                'is_active' => true,
            ],
            [
                'name' => 'Dr. Lisa Thompson',
                'email' => 'lisa.thompson@eclass.com',
                'password' => Hash::make('123456'),
                'admin_type_id' => $instructorType->id,
                'phone' => '+1 (555) 567-8901',
                'is_active' => true,
            ],
        ];

        foreach ($instructors as $instructorData) {
            Admin::updateOrCreate(
                ['email' => $instructorData['email']],
                $instructorData
            );
        }

        $this->command->info('Sample instructors created successfully!');
    }
}
