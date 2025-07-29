<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create blog categories first
        $categories = [
            [
                'name' => 'Education',
                'slug' => 'education',
                'description' => 'Educational articles and tips',
                'status' => 'active',
                'order' => 1,
            ],
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Technology related articles',
                'status' => 'active',
                'order' => 2,
            ],
            [
                'name' => 'Online Learning',
                'slug' => 'online-learning',
                'description' => 'Online learning tips and guides',
                'status' => 'active',
                'order' => 3,
            ],
        ];

        foreach ($categories as $categoryData) {
            BlogCategory::updateOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        // Get category IDs
        $educationCategory = BlogCategory::where('slug', 'education')->first();
        $technologyCategory = BlogCategory::where('slug', 'technology')->first();
        $onlineLearningCategory = BlogCategory::where('slug', 'online-learning')->first();

        // Create sample blogs
        $blogs = [
            [
                'title' => 'Blogging Courses, Training, Classes & Tutorials Online',
                'slug' => 'blogging-courses-training-classes-tutorials-online',
                'description' => 'Learn the art of blogging with our comprehensive online courses. From basic writing skills to advanced content marketing strategies, we cover everything you need to become a successful blogger.',
                'excerpt' => 'Master the art of blogging with our comprehensive online courses covering everything from basic writing to advanced content marketing.',
                'category_id' => $educationCategory->id,
                'author' => 'Admin',
                'status' => 'published',
                'is_featured' => true,
                'views_count' => 1250,
            ],
            [
                'title' => 'The Future of Online Education: Trends and Innovations',
                'slug' => 'future-online-education-trends-innovations',
                'description' => 'Explore the latest trends and innovations shaping the future of online education. From AI-powered learning platforms to virtual reality classrooms, discover what\'s next in digital learning.',
                'excerpt' => 'Discover the latest trends and innovations that are revolutionizing online education and digital learning experiences.',
                'category_id' => $technologyCategory->id,
                'author' => 'Admin',
                'status' => 'published',
                'is_featured' => true,
                'views_count' => 980,
            ],
            [
                'title' => 'Effective Study Techniques for Online Learning',
                'slug' => 'effective-study-techniques-online-learning',
                'description' => 'Master the most effective study techniques specifically designed for online learning environments. Learn how to stay focused, manage your time, and achieve better results.',
                'excerpt' => 'Learn proven study techniques that will help you succeed in online learning environments and achieve better academic results.',
                'category_id' => $onlineLearningCategory->id,
                'author' => 'Admin',
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 750,
            ],
            [
                'title' => 'Building a Successful Online Course: A Complete Guide',
                'slug' => 'building-successful-online-course-complete-guide',
                'description' => 'Step-by-step guide to creating and launching a successful online course. From content planning to marketing strategies, we cover everything you need to know.',
                'excerpt' => 'A comprehensive guide to creating, launching, and marketing successful online courses that engage and educate your students.',
                'category_id' => $educationCategory->id,
                'author' => 'Admin',
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 620,
            ],
            [
                'title' => 'Digital Marketing Strategies for Educational Platforms',
                'slug' => 'digital-marketing-strategies-educational-platforms',
                'description' => 'Learn effective digital marketing strategies specifically tailored for educational platforms and online learning businesses.',
                'excerpt' => 'Discover proven digital marketing strategies that will help your educational platform reach more students and grow your business.',
                'category_id' => $technologyCategory->id,
                'author' => 'Admin',
                'status' => 'published',
                'is_featured' => false,
                'views_count' => 450,
            ],
        ];

        foreach ($blogs as $blogData) {
            Blog::updateOrCreate(
                ['slug' => $blogData['slug']],
                $blogData
            );
        }
    }
}
