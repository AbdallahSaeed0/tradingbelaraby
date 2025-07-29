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
        Schema::create('homework_submissions', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('homework_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Submission details
            $table->text('submission_text')->nullable(); // Text submission
            $table->string('submission_file')->nullable(); // Main submission file
            $table->json('additional_files')->nullable(); // Multiple additional files

            // Submission timing
            $table->timestamp('submitted_at')->useCurrent();
            $table->boolean('is_late')->default(false);
            $table->integer('days_late')->default(0);

            // Grading
            $table->integer('score_earned')->nullable();
            $table->integer('max_score')->default(100);
            $table->decimal('percentage_score', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->text('instructor_notes')->nullable();

            // Grading status
            $table->enum('status', ['submitted', 'graded', 'returned', 'late'])->default('submitted');
            $table->boolean('is_graded')->default(false);
            $table->timestamp('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('admins')->onDelete('set null');

            // Plagiarism and originality
            $table->boolean('plagiarism_checked')->default(false);
            $table->decimal('originality_score', 5, 2)->nullable(); // Percentage of original content
            $table->text('plagiarism_report')->nullable();

            // Student notes
            $table->text('student_notes')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['homework_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['homework_id', 'status']);
            $table->index('submitted_at');
            $table->index('is_late');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homework_submissions');
    }
};
