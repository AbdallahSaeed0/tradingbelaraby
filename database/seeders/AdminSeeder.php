<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('admins')->insert([
            [
                'name' => 'Super Admin',
                'email' => 'admin@eclass.com',
                'password' => Hash::make('123456'),
                'type' => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Main Instructor',
                'email' => 'instructor@eclass.com',
                'password' => Hash::make('123456'),
                'type' => 'instructor',
                'created_at' => now(),
                'updated_at' => now(),
                'is_active' => true,
            ],
            [
                'name' => 'Office Employee',
                'email' => 'employee@eclass.com',
                'password' => Hash::make('123456'),
                'type' => 'employee',
                'created_at' => now(),
                'updated_at' => now(),
                'is_active' => true,
            ],
        ]);
    }
}
