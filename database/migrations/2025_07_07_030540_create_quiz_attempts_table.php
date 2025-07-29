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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Attempt details
            $table->integer('attempt_number')->default(1);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();

            // Scoring
            $table->integer('score_earned')->default(0);
            $table->integer('total_possible_score')->default(0);
            $table->decimal('percentage_score', 5, 2)->default(0);
            $table->boolean('is_passed')->default(false);

            // Time tracking
            $table->integer('time_taken_minutes')->nullable();
            $table->boolean('was_timed_out')->default(false);

            // Status
            $table->enum('status', ['in_progress', 'completed', 'abandoned', 'timed_out'])->default('in_progress');

            // Answers storage
            $table->json('answers')->nullable(); // Store student answers
            $table->json('correct_answers')->nullable(); // Store correct answers for comparison

            // Grading
            $table->boolean('is_graded')->default(false);
            $table->timestamp('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->text('instructor_notes')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['quiz_id', 'user_id']);
            $table->index(['user_id', 'status']);
            $table->index(['quiz_id', 'status']);
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
