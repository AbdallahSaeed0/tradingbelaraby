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
        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');

            // Question details
            $table->text('question_text');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'fill_blank', 'essay', 'matching'])->default('multiple_choice');
            $table->integer('points')->default(1);
            $table->integer('order')->default(0);

            // Options for multiple choice questions
            $table->json('options')->nullable(); // ["option1", "option2", "option3", "option4"]
            $table->json('correct_answers')->nullable(); // [0, 2] for multiple correct answers

            // For true/false questions
            $table->boolean('correct_answer_boolean')->nullable();

            // For fill in the blank
            $table->json('correct_answers_text')->nullable(); // ["answer1", "answer2"]

            // For essay questions
            $table->text('sample_answer')->nullable();
            $table->integer('word_limit')->nullable();

            // Question settings
            $table->boolean('is_required')->default(true);
            $table->boolean('shuffle_options')->default(false);
            $table->text('explanation')->nullable(); // Explanation for correct answer

            // Media attachments
            $table->string('image')->nullable();
            $table->string('audio')->nullable();
            $table->string('video')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['quiz_id', 'order']);
            $table->index('question_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_questions');
    }
};
