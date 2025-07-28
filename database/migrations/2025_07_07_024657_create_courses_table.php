<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();

            // Basic course information
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->string('image')->nullable();
            $table->string('duration'); // e.g., "2 hours", "3 weeks", "6 months"
            $table->decimal('price', 10, 2)->default(0.00);
            $table->decimal('original_price', 10, 2)->nullable(); // for discount display

            // Certificate template
            $table->string('certificate_template')->nullable(); // PDF file path
            $table->text('certificate_text')->nullable(); // Template text with placeholders

            // Category relationship
            $table->foreignId('category_id')->constrained('course_categories')->onDelete('cascade');

            // What to learn - JSON array for flexible learning objectives
            $table->json('what_to_learn')->nullable(); // ["Learn HTML", "Master CSS", "Build websites"]

            // FAQ - JSON array for course-specific FAQs
            $table->json('faq_course')->nullable(); // [{"question": "...", "answer": "..."}]

            // Instructor relationship
            $table->foreignId('instructor_id')->constrained('admins')->onDelete('cascade');

            // Course status and visibility
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_free')->default(false);

            // Course content and structure
            $table->integer('total_lessons')->default(0);
            $table->integer('total_duration_minutes')->default(0);

            // Enrollment and completion tracking
            $table->integer('enrolled_students')->default(0);
            $table->integer('completion_rate')->default(0); // percentage

            // Rating and feedback
            $table->decimal('average_rating', 3, 2)->default(0.00); // 0.00 to 5.00
            $table->integer('total_ratings')->default(0);
            $table->integer('total_reviews')->default(0);

            // SEO and meta
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();


            // Timestamps
            $table->timestamps();

            // Indexes for better performance
            $table->index(['status', 'is_featured']);
            $table->index(['category_id', 'status']);
            $table->index(['instructor_id', 'status']);
            $table->index('average_rating');
            $table->index('price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
