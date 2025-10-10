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
        Schema::create('course_instructor', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
            $table->foreignId('instructor_id')->constrained('admins')->onDelete('cascade');
            $table->timestamps();

            // Prevent duplicate entries
            $table->unique(['course_id', 'instructor_id']);
        });

        // Migrate existing data from courses table
        DB::statement('
            INSERT INTO course_instructor (course_id, instructor_id, created_at, updated_at)
            SELECT id, instructor_id, created_at, updated_at
            FROM courses
            WHERE instructor_id IS NOT NULL
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_instructor');
    }
};
