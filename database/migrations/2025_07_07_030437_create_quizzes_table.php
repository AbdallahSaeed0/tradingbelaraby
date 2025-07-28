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
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('admins')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('course_sections')->onDelete('cascade');
            $table->foreignId('lecture_id')->nullable()->constrained('course_lectures')->onDelete('cascade');

            // Quiz details
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();

            // Quiz settings
            $table->integer('time_limit_minutes')->nullable(); // NULL = no time limit
            $table->integer('total_questions')->default(0);
            $table->integer('total_marks')->default(0);
            $table->integer('passing_score')->default(60); // Percentage

            // Quiz configuration
            $table->boolean('is_randomized')->default(false); // Randomize question order
            $table->boolean('show_results_immediately')->default(true);
            $table->boolean('allow_retake')->default(true);
            $table->integer('max_attempts')->default(3);
            $table->boolean('is_published')->default(true);

            // Quiz type and difficulty
            $table->enum('quiz_type', ['practice', 'assessment', 'final_exam'])->default('practice');
            $table->enum('difficulty', ['easy', 'medium', 'hard'])->default('medium');

            // Scheduling
            $table->timestamp('available_from')->nullable();
            $table->timestamp('available_until')->nullable();

            // Statistics
            $table->integer('total_attempts')->default(0);
            $table->integer('average_score')->default(0);
            $table->integer('passing_attempts')->default(0);

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['course_id', 'is_published']);
            $table->index(['section_id', 'is_published']);
            $table->index(['lecture_id', 'is_published']);
            $table->index(['instructor_id', 'is_published']);
            $table->index('quiz_type');
            $table->index('difficulty');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
