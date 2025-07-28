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
        Schema::create('lecture_completions', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('lecture_id')->constrained('course_lectures')->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');

            // Completion details
            $table->boolean('is_completed')->default(false);
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_accessed_at')->useCurrent();

            // Progress tracking
            $table->integer('watch_time_seconds')->default(0);
            $table->decimal('progress_percentage', 5, 2)->default(0); // 0-100%
            $table->boolean('watched_entire_video')->default(false);

            // Notes and bookmarks
            $table->text('notes')->nullable();
            $table->json('bookmarks')->nullable(); // Timestamps of bookmarked moments
            $table->json('watched_segments')->nullable(); // Track which parts were watched

            // Quiz and homework completion
            $table->boolean('quiz_completed')->default(false);
            $table->boolean('homework_submitted')->default(false);

            // Timestamps
            $table->timestamps();

            // Ensure one completion record per user per lecture
            $table->unique(['user_id', 'lecture_id']);

            // Indexes
            $table->index(['user_id', 'course_id']);
            $table->index(['lecture_id', 'is_completed']);
            $table->index(['course_id', 'user_id']);
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecture_completions');
    }
};
