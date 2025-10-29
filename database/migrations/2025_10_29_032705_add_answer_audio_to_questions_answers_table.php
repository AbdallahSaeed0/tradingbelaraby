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
        Schema::table('questions_answers', function (Blueprint $table) {
            $table->string('answer_audio')->nullable()->after('answer_content');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions_answers', function (Blueprint $table) {
            $table->dropColumn('answer_audio');
        });
    }
};
