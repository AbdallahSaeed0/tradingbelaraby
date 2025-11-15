<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Seed default admin accounts
        $this->call([
            AdminTypeSeeder::class,
            AdminSeeder::class,
            LanguageSeeder::class,
            FrontPageTranslationsSeeder::class,
            InstructorSeeder::class,
            CourseSeeder::class,
            TestimonialSeeder::class,
            FeatureSeeder::class,
            HeroFeatureSeeder::class,
            AboutUniversitySeeder::class,
            FAQSeeder::class,
            ScholarshipBannerSeeder::class,
            CTAVideoSeeder::class,
            FeaturesSplitSeeder::class,
            InfoSplitSeeder::class,
            NewsletterSettingsSeeder::class,
            BlogSeeder::class,
            ContactSettingsSeeder::class,
        ]);
    }
}
