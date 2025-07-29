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
        Schema::create('live_classes', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('admins')->onDelete('cascade');

            // Live class details
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('link'); // Zoom, Teams, Google Meet, etc.

            // Scheduling
            $table->timestamp('scheduled_at');
            $table->integer('duration_minutes')->default(60);
            $table->enum('status', ['scheduled', 'live', 'completed', 'cancelled'])->default('scheduled');

            // Capacity and enrollment
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);

            // Recording and materials
            $table->string('recording_url')->nullable(); // After class recording
            $table->json('materials')->nullable(); // Pre/post class materials

            // Settings
            $table->boolean('is_free')->default(false);
            $table->boolean('requires_registration')->default(true);
            $table->text('instructions')->nullable(); // Pre-class instructions

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['course_id', 'scheduled_at']);
            $table->index(['instructor_id', 'scheduled_at']);
            $table->index(['status', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('live_classes');
    }
};
