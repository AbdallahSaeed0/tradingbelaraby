<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\AdminType;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin types
        $adminType = AdminType::where('name', 'admin')->first();
        $instructorType = AdminType::where('name', 'instructor')->first();
        $employeeType = AdminType::where('name', 'employee')->first();

        if (!$adminType || !$instructorType || !$employeeType) {
            $this->command->error('Admin types not found. Please run AdminTypeSeeder first.');
            return;
        }

        DB::table('admins')->insert([
            [
                'name' => 'Super Admin',
                'email' => 'admin@eclass.com',
                'password' => Hash::make('123456'),
                'admin_type_id' => $adminType->id,
                'created_at' => now(),
                'updated_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Main Instructor',
                'email' => 'instructor@eclass.com',
                'password' => Hash::make('123456'),
                'admin_type_id' => $instructorType->id,
                'created_at' => now(),
                'updated_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Office Employee',
                'email' => 'employee@eclass.com',
                'password' => Hash::make('123456'),
                'admin_type_id' => $employeeType->id,
                'created_at' => now(),
                'updated_at' => now(),
                'is_active' => true,
            ],
        ]);
    }
}
