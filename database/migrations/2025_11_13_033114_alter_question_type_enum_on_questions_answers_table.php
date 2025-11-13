<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `questions_answers`
            MODIFY COLUMN `question_type` ENUM(
                'general',
                'technical',
                'assignment',
                'schedule',
                'content',
                'other',
                'lecture_specific',
                'clarification'
            ) NOT NULL DEFAULT 'general'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE `questions_answers`
            MODIFY COLUMN `question_type` ENUM(
                'general',
                'lecture_specific',
                'technical',
                'clarification'
            ) NOT NULL DEFAULT 'general'
        ");
    }
};
