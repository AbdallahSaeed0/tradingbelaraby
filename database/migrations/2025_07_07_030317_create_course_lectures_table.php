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
        Schema::create('course_lectures', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->constrained('course_sections')->onDelete('cascade');

            // Lecture details
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0); // For ordering within section

            // Content type and files
            $table->enum('content_type', ['video', 'document', 'audio', 'text', 'live'])->default('video');
            $table->string('video_file')->nullable(); // Uploaded video file
            $table->string('video_url')->nullable(); // External video URL (YouTube, Vimeo, etc.)
            $table->string('document_file')->nullable(); // PDF, PPT, etc.
            $table->text('content_text')->nullable(); // For text-based lectures

            // Live lecture support
            $table->string('live_link')->nullable(); // Zoom, Teams, etc.
            $table->timestamp('live_scheduled_at')->nullable();
            $table->boolean('is_live')->default(false);

            // Duration and completion
            $table->integer('duration_minutes')->default(0);
            $table->boolean('is_free')->default(false); // Free preview lecture

            // Lecture status
            $table->boolean('is_published')->default(true);
            $table->boolean('is_completed')->default(false); // For tracking completion

            // Additional resources
            $table->json('attachments')->nullable(); // Additional files
            $table->text('notes')->nullable(); // Instructor notes

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['course_id', 'section_id', 'order']);
            $table->index(['section_id', 'order']);
            $table->index(['is_live', 'live_scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_lectures');
    }
};
