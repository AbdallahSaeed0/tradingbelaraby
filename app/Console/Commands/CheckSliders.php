<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Slider;

class CheckSliders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:sliders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check sliders and add Arabic content for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking existing sliders...');
        
        $sliders = Slider::all();
        
        if ($sliders->isEmpty()) {
            $this->warn('No sliders found. Creating a test slider...');
            
            $slider = Slider::create([
                'title' => 'Welcome to E-Class',
                'title_ar' => 'مرحباً بك في إي-كلاس',
                'welcome_text' => 'WELCOME TO E-CLASS',
                'welcome_text_ar' => 'مرحباً بك في إي-كلاس',
                'subtitle' => 'Education is the best key success in life',
                'subtitle_ar' => 'التعليم هو أفضل مفتاح للنجاح في الحياة',
                'background_image' => 'https://eclass.mediacity.co.in/demo2/public/images/slider/slider_img02.png',
                'button_text' => 'Search',
                'button_text_ar' => 'بحث',
                'search_placeholder' => 'Search Courses',
                'search_placeholder_ar' => 'البحث عن الدورات',
                'order' => 1,
                'is_active' => true,
            ]);
            
            $this->info('Test slider created with ID: ' . $slider->id);
        } else {
            $this->info('Found ' . $sliders->count() . ' sliders:');
            
            foreach ($sliders as $slider) {
                $this->line('ID: ' . $slider->id . ' - Title: ' . $slider->title);
                $this->line('  Title AR: ' . ($slider->title_ar ?: 'NULL'));
                
                // Force add Arabic content
                $slider->update([
                    'title_ar' => 'عنوان بالعربية - ' . $slider->title,
                    'welcome_text_ar' => 'نص ترحيب بالعربية',
                    'subtitle_ar' => 'نص فرعي بالعربية',
                    'button_text_ar' => 'زر بالعربية',
                    'search_placeholder_ar' => 'البحث بالعربية',
                ]);
                $this->info('Added Arabic content to slider ID: ' . $slider->id);
            }
        }
        
        $this->info('Done!');
    }
}
