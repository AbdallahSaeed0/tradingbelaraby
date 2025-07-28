<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactSettings;

class ContactSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ContactSettings::updateOrCreate(
            ['id' => 1],
            [
                'phone' => '+1 (555) 123-4567',
                'email' => 'info@example.com',
                'address' => '123 Main Street, Suite 100, New York, NY 10001, United States',
                'map_embed_url' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.215583132064!2d-74.00601508459367!3d40.71277537933185!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25a165bedccab%3A0x2cb2ddf003b5ae01!2sNew%20York%2C%20NY%2C%20USA!5e0!3m2!1sen!2s!4v1640995200000!5m2!1sen!2s',
                'map_latitude' => '40.7128',
                'map_longitude' => '-74.0060',
                'office_hours' => 'Monday - Friday: 9:00 AM - 6:00 PM\nSaturday: 10:00 AM - 4:00 PM\nSunday: Closed',
                'social_facebook' => 'https://facebook.com/example',
                'social_twitter' => 'https://twitter.com/example',
                'social_youtube' => 'https://youtube.com/example',
                'social_linkedin' => 'https://linkedin.com/company/example',
                'is_active' => true,
            ]
        );
    }
} 