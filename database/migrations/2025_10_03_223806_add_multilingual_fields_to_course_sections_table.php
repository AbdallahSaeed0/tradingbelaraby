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
        Schema::table('course_sections', function (Blueprint $table) {
            // Add multilingual fields for section content
            $table->string('title_ar')->nullable()->after('title');
            $table->text('description_ar')->nullable()->after('description');

            // Add learning objectives for this section
            $table->json('learning_objectives')->nullable()->after('description_ar');
            $table->json('learning_objectives_ar')->nullable()->after('learning_objectives');

            // Add section-specific resources
            $table->json('resources')->nullable()->after('learning_objectives_ar');
            $table->json('resources_ar')->nullable()->after('resources');

            // Add section type (e.g., 'theory', 'practice', 'assessment')
            $table->string('section_type')->default('content')->after('resources_ar');

            // Add difficulty level
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner')->after('section_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropColumn([
                'title_ar',
                'description_ar',
                'learning_objectives',
                'learning_objectives_ar',
                'resources',
                'resources_ar',
                'section_type',
                'difficulty_level',
            ]);
        });
    }
};
