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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained()->onDelete('cascade');
            $table->string('translation_key'); // courses, users, etc.
            $table->text('translation_value'); // The translated text
            $table->string('group')->default('general'); // general, admin, frontend, etc.
            $table->timestamps();

            // Ensure unique combination of language, key, and group
            $table->unique(['language_id', 'translation_key', 'group']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
