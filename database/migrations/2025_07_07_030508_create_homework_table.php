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
        Schema::create('homework', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('admins')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('course_sections')->onDelete('cascade');
            $table->foreignId('lecture_id')->nullable()->constrained('course_lectures')->onDelete('cascade');

            // Homework details
            $table->string('name');
            $table->text('description');
            $table->text('instructions')->nullable();

            // File attachments
            $table->string('attachment_file')->nullable(); // Main homework file
            $table->json('additional_files')->nullable(); // Multiple additional files

            // Scoring and evaluation
            $table->integer('max_score')->default(100);
            $table->integer('weight_percentage')->default(10); // Weight in course grade

            // Scheduling
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('due_date');
            $table->timestamp('late_submission_until')->nullable(); // Grace period

            // Homework settings
            $table->boolean('is_published')->default(true);
            $table->boolean('allow_late_submission')->default(false);
            $table->boolean('require_file_upload')->default(true);
            $table->boolean('allow_text_submission')->default(false);

            // Submission tracking
            $table->integer('total_assignments')->default(0);
            $table->integer('submitted_assignments')->default(0);
            $table->integer('graded_assignments')->default(0);

            // Statistics
            $table->decimal('average_score', 5, 2)->default(0);
            $table->integer('on_time_submissions')->default(0);
            $table->integer('late_submissions')->default(0);

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['course_id', 'is_published']);
            $table->index(['section_id', 'is_published']);
            $table->index(['lecture_id', 'is_published']);
            $table->index(['instructor_id', 'is_published']);
            $table->index('due_date');
            $table->index(['due_date', 'is_published']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework');
    }
};
