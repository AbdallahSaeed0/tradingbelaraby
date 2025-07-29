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
        Schema::create('course_ratings', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Rating details
            $table->integer('rating')->comment('1-5 stars');
            $table->text('review')->nullable();
            $table->text('title')->nullable(); // Review title

            // Rating categories (optional detailed ratings)
            $table->integer('content_quality')->nullable(); // 1-5
            $table->integer('instructor_quality')->nullable(); // 1-5
            $table->integer('value_for_money')->nullable(); // 1-5
            $table->integer('course_material')->nullable(); // 1-5

            // Review status
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // For moderation

            // Helpful votes
            $table->integer('helpful_votes')->default(0);
            $table->integer('total_votes')->default(0);

            // Timestamps
            $table->timestamps();

            // Ensure one rating per user per course
            $table->unique(['course_id', 'user_id']);

            // Indexes
            $table->index(['course_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_ratings');
    }
};
