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
        Schema::create('course_enrollments', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Enrollment details
            $table->enum('status', ['active', 'completed', 'cancelled', 'expired'])->default('active');
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // For time-limited access

            // Progress tracking
            $table->integer('progress_percentage')->default(0); // 0-100
            $table->integer('lessons_completed')->default(0);
            $table->integer('total_lessons')->default(0);
            $table->timestamp('last_accessed_at')->nullable();

            // Certificate
            $table->string('certificate_path')->nullable();
            $table->timestamp('certificate_issued_at')->nullable();

            // Payment information
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();

            // Notes
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();

            // Ensure one enrollment per user per course
            $table->unique(['course_id', 'user_id']);

            // Indexes
            $table->index(['course_id', 'status']);
            $table->index(['user_id', 'status']);
            $table->index('enrolled_at');
            $table->index('completed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_enrollments');
    }
};
