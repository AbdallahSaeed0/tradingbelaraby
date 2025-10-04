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
        Schema::table('course_lectures', function (Blueprint $table) {
            // Add multilingual fields for lecture content
            $table->string('title_ar')->nullable()->after('title');
            $table->text('description_ar')->nullable()->after('description');
            $table->text('content_text_ar')->nullable()->after('content_text');
            $table->text('notes_ar')->nullable()->after('notes');

            // Add learning objectives for this lecture
            $table->json('learning_objectives')->nullable()->after('notes_ar');
            $table->json('learning_objectives_ar')->nullable()->after('learning_objectives');

            // Add lecture-specific resources and materials
            $table->json('lecture_resources')->nullable()->after('learning_objectives_ar');
            $table->json('lecture_resources_ar')->nullable()->after('lecture_resources');

            // Add lecture type (e.g., 'theory', 'demo', 'exercise', 'quiz', 'assignment')
            $table->string('lecture_type')->default('content')->after('lecture_resources_ar');

            // Add difficulty level for this specific lecture
            $table->enum('difficulty_level', ['beginner', 'intermediate', 'advanced'])->default('beginner')->after('lecture_type');

            // Add prerequisites for this lecture
            $table->json('prerequisites')->nullable()->after('difficulty_level');
            $table->json('prerequisites_ar')->nullable()->after('prerequisites');

            // Add lecture tags for better organization
            $table->json('tags')->nullable()->after('prerequisites_ar');
            $table->json('tags_ar')->nullable()->after('tags');

            // Add transcript support for video content
            $table->longText('transcript')->nullable()->after('tags_ar');
            $table->longText('transcript_ar')->nullable()->after('transcript');

            // Add subtitle/caption support
            $table->json('subtitles')->nullable()->after('transcript_ar');
            $table->json('subtitles_ar')->nullable()->after('subtitles');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('course_lectures', function (Blueprint $table) {
            $table->dropColumn([
                'title_ar',
                'description_ar',
                'content_text_ar',
                'notes_ar',
                'learning_objectives',
                'learning_objectives_ar',
                'lecture_resources',
                'lecture_resources_ar',
                'lecture_type',
                'difficulty_level',
                'prerequisites',
                'prerequisites_ar',
                'tags',
                'tags_ar',
                'transcript',
                'transcript_ar',
                'subtitles',
                'subtitles_ar',
            ]);
        });
    }
};
