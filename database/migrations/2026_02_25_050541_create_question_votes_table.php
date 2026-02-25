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
        Schema::create('question_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('questions_answer_id')->constrained('questions_answers')->cascadeOnDelete();
            $table->string('vote_type', 20)->default('helpful'); // helpful, not_helpful
            $table->timestamps();
            $table->unique(['user_id', 'questions_answer_id'], 'question_votes_user_question_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('question_votes');
    }
};
