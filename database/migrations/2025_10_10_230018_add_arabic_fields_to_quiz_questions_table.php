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
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->text('question_text_ar')->nullable()->after('question_text');
            $table->json('options_ar')->nullable()->after('options');
            $table->json('correct_answers_text_ar')->nullable()->after('correct_answers_text');
            $table->text('sample_answer_ar')->nullable()->after('sample_answer');
            $table->text('explanation_ar')->nullable()->after('explanation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->dropColumn([
                'question_text_ar',
                'options_ar',
                'correct_answers_text_ar',
                'sample_answer_ar',
                'explanation_ar'
            ]);
        });
    }
};
