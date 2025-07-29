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
        Schema::create('questions_answers', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Student asking
            $table->foreignId('instructor_id')->nullable()->constrained('admins')->onDelete('set null'); // Instructor answering
            $table->foreignId('lecture_id')->nullable()->constrained('course_lectures')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('course_sections')->onDelete('cascade');

            // Question details
            $table->string('question_title');
            $table->text('question_content');
            $table->enum('question_type', ['general', 'lecture_specific', 'technical', 'clarification'])->default('general');

            // Answer details
            $table->text('answer_content')->nullable();
            $table->timestamp('answered_at')->nullable();

            // Status and visibility
            $table->enum('status', ['pending', 'answered', 'closed', 'flagged'])->default('pending');
            $table->boolean('is_public')->default(true); // Public or private question
            $table->boolean('is_anonymous')->default(false); // Anonymous question

            // Engagement metrics
            $table->integer('views_count')->default(0);
            $table->integer('helpful_votes')->default(0);
            $table->integer('total_votes')->default(0);

            // Tags and categorization
            $table->json('tags')->nullable(); // For categorizing questions
            $table->string('priority')->default('normal'); // low, normal, high, urgent

            // Moderation
            $table->text('moderation_notes')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('moderated_at')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['course_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index(['instructor_id', 'status']);
            $table->index(['lecture_id', 'status']);
            $table->index(['section_id', 'status']);
            $table->index('question_type');
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions_answers');
    }
};
